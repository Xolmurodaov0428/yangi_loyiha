<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\ActivityLog;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login sahifasini ko'rsatish
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login jarayonini amalga oshirish
     */
    public function login(Request $request)
    {
        // Rate limiting - IP va login bo'yicha
        $key = 'login.' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            ActivityLog::log('login_blocked', "IP {$request->ip()} dan ko'p urinishlar: " . $request->input('login'));
            
            return redirect()->back()
                ->withErrors(['login' => "Juda ko'p urinish. {$seconds} soniyadan keyin qayta urinib ko'ring."])
                ->withInput($request->only('login'));
        }

        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|min:6',
        ], [
            'login.required' => 'Login (email yoki foydalanuvchi nomi) kiriting',
            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $login = $request->input('login');
        
        // Email bo'lsa email orqali, aks holda username orqali autentifikatsiya qilamiz
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $login, 'password' => $request->password];
        } else {
            $credentials = ['username' => $login, 'password' => $request->password];
        }
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Muvaffaqiyatli login - rate limiter ni tozalash
            RateLimiter::clear($key);
            
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Check if user is active
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['login' => 'Hisobingiz faol emas. Administrator bilan bog\'laning.'])
                    ->withInput($request->only('login'));
            }
            
            // Check if user needs approval
            if (isset($user->approved_at) && !$user->approved_at) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['login' => 'Hisobingiz hali tasdiqlanmagan. Administrator bilan bog\'laning.'])
                    ->withInput($request->only('login'));
            }
            
            // Log successful login
            ActivityLog::log('login', "{$user->name} tizimga kirdi (IP: {$request->ip()})");
            
            // Update last login time
            $user->update(['last_login_at' => now()]);
            
            $target = '/dashboard';
            if ($user && $user->role === 'admin') {
                $target = route('admin.dashboard');
            } elseif ($user && $user->role === 'supervisor') {
                $target = route('supervisor.dashboard');
            }
            
            return redirect()->intended($target)->with('success', 'Muvaffaqiyatli kirdingiz!');
        }

        // Muvaffaqiyatsiz login - rate limiter ni oshirish
        RateLimiter::hit($key, $decayMinutes * 60);
        
        // Log failed login attempt
        ActivityLog::log('login_failed', "Muvaffaqiyatsiz login urinishi: {$login} (IP: {$request->ip()})");

        return redirect()->back()
            ->withErrors(['login' => 'Login yoki parol noto\'g\'ri'])
            ->withInput($request->only('login'));
    }

    /**
     * Register sahifasini ko'rsatish
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Ro'yxatdan o'tish jarayonini amalga oshirish
     */
    public function register(Request $request)
    {
        // Rate limiting for registration
        $key = 'register.' . $request->ip();
        $maxAttempts = 3;
        $decayMinutes = 10;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->withErrors(['email' => "Juda ko'p ro'yxatdan o'tish urinishi. " . ceil($seconds / 60) . " daqiqadan keyin qayta urinib ko'ring."])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$/' // At least one lowercase, uppercase, and number
            ],
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Ism kiriting',
            'name.max' => 'Ism 255 ta belgidan oshmasligi kerak',
            'email.required' => 'Email kiriting',
            'email.email' => 'To\'g\'ri email formatini kiriting',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.regex' => 'Parol kamida bitta katta harf, kichik harf va raqam bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'terms.required' => 'Foydalanish shartlarini qabul qilishingiz kerak',
            'terms.accepted' => 'Foydalanish shartlarini qabul qilishingiz kerak',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // Yangi foydalanuvchi yaratish
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supervisor', // Default role
        ]);
        
        // Log registration
        ActivityLog::log('register', "Yangi foydalanuvchi ro'yxatdan o'tdi: {$user->email} (IP: {$request->ip()})");

        // Login sahifasiga yo'naltirish
        return redirect('/login')->with('success', 'Muvaffaqiyatli ro\'yxatdan o\'tdingiz! Administrator tasdiqlashidan keyin tizimga kira olasiz.');
    }

    /**
     * Parolni unutish sahifasini ko'rsatish
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Parolni tiklash havolasini yuborish
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Rate limiting for password reset
        $key = 'password-reset.' . $request->ip();
        $maxAttempts = 3;
        $decayMinutes = 10;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            ActivityLog::log('password_reset_blocked', "IP {$request->ip()} dan ko'p urinishlar: " . $request->input('email'));
            
            return redirect()->back()
                ->withErrors(['email' => "Juda ko'p urinish. " . ceil($seconds / 60) . " daqiqadan keyin qayta urinib ko'ring."])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email kiriting',
            'email.email' => 'To\'g\'ri email formatini kiriting',
            'email.exists' => 'Bu email ro\'yxatdan o\'tmagan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);

        // User ni topish
        $user = User::where('email', $request->email)->first();
        
        // Xavfsizlik: User faol emasligini tekshirish
        if (isset($user->is_active) && !$user->is_active) {
            ActivityLog::log('password_reset_inactive', "Faol bo'lmagan user uchun parol tiklash: {$request->email} (IP: {$request->ip()})");
            // Xavfsizlik uchun xuddi shu xabarni qaytaramiz
            return redirect()->back()->with('success', 
                'Agar bu email tizimda ro\'yxatdan o\'tgan bo\'lsa, parolni tiklash havolasi yuboriladi.'
            );
        }

        // Token yaratish (cryptographically secure)
        $token = bin2hex(random_bytes(32)); // 64 character token

        // Eski tokenlarni o'chirish
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Yangi token saqlash
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Reset URL yaratish
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));
        
        // Log password reset request
        ActivityLog::log('password_reset_requested', "Parol tiklash so'rovi: {$request->email} (IP: {$request->ip()})");

        // Email yuborish (agar mail configured bo'lsa)
        try {
            // TODO: Email yuborish funksiyasini qo'shish
            // Mail::to($user->email)->send(new PasswordResetMail($resetUrl));
            
            // Development uchun: URL ni session ga saqlash
            if (env('APP_ENV') !== 'production') {
                session(['password_reset_url' => $resetUrl]);
            }
        } catch (\Exception $e) {
            // Email yuborishda xato bo'lsa log qilish
            ActivityLog::log('password_reset_email_failed', "Email yuborishda xato: {$request->email}");
        }

        // Xavfsizlik: Har doim bir xil xabarni qaytarish (email mavjudligini oshkor qilmaslik)
        return redirect()->back()->with('success', 
            'Agar bu email tizimda ro\'yxatdan o\'tgan bo\'lsa, parolni tiklash havolasi yuboriladi. Email qutingizni tekshiring.'
        );
    }

    /**
     * Parolni tiklash sahifasini ko'rsatish
     */
    public function showResetPasswordForm($token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Parolni yangilash
     */
    public function resetPassword(Request $request)
    {
        // Rate limiting for password reset
        $key = 'password-reset-submit.' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 10;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()
                ->withErrors(['email' => "Juda ko'p urinish. " . ceil($seconds / 60) . " daqiqadan keyin qayta urinib ko'ring."])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$/' // At least one lowercase, uppercase, and number
            ],
        ], [
            'email.required' => 'Email kiriting',
            'email.email' => 'To\'g\'ri email formatini kiriting',
            'email.exists' => 'Bu email ro\'yxatdan o\'tmagan',
            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.regex' => 'Parol kamida bitta katta harf, kichik harf va raqam bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key, $decayMinutes * 60);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Token tekshirish
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            RateLimiter::hit($key, $decayMinutes * 60);
            ActivityLog::log('password_reset_invalid_token', "Noto'g'ri token: {$request->email} (IP: {$request->ip()})");
            return redirect()->back()->withErrors(['email' => 'Parolni tiklash havolasi topilmadi yoki yaroqsiz']);
        }

        // Token muddati tekshirish (1 soat)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            RateLimiter::hit($key, $decayMinutes * 60);
            ActivityLog::log('password_reset_expired', "Muddati tugagan token: {$request->email} (IP: {$request->ip()})");
            return redirect()->back()->withErrors(['email' => 'Parolni tiklash havolasi muddati tugagan. Qaytadan so\'rov yuboring.']);
        }
        
        // Token ni verify qilish
        if (!Hash::check($request->token, $resetRecord->token)) {
            RateLimiter::hit($key, $decayMinutes * 60);
            ActivityLog::log('password_reset_token_mismatch', "Token mos kelmadi: {$request->email} (IP: {$request->ip()})");
            return redirect()->back()->withErrors(['email' => 'Parolni tiklash havolasi yaroqsiz']);
        }

        // User ni topish va parolni yangilash
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            RateLimiter::hit($key, $decayMinutes * 60);
            return redirect()->back()->withErrors(['email' => 'Foydalanuvchi topilmadi']);
        }
        
        // Parolni yangilash
        $user->password = Hash::make($request->password);
        $user->save();

        // Tokenni o'chirish
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        // Rate limiter ni tozalash (muvaffaqiyatli)
        RateLimiter::clear($key);
        
        // Log successful password reset
        ActivityLog::log('password_reset_success', "Parol muvaffaqiyatli yangilandi: {$user->email} (IP: {$request->ip()})");

        return redirect('/login')->with('success', 'Parol muvaffaqiyatli yangilandi! Endi yangi parol bilan tizimga kiring.');
    }

    /**
     * Logout qilish
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity
        if ($user) {
            ActivityLog::log('logout', "{$user->name} tizimdan chiqdi (IP: {$request->ip()})");
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Tizimdan chiqdingiz');
    }
}
