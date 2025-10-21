<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
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
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Log login activity
            ActivityLog::log('login', "{$user->name} tizimga kirdi");
            
            $target = '/dashboard';
            if ($user && $user->role === 'admin') {
                $target = route('admin.dashboard');
            } elseif ($user && $user->role === 'supervisor') {
                $target = route('supervisor.dashboard');
            }
            return redirect()->intended($target)->with('success', 'Muvaffaqiyatli kirdingiz!');
        }

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Ism kiriting',
            'name.max' => 'Ism 255 ta belgidan oshmasligi kerak',
            'email.required' => 'Email kiriting',
            'email.email' => 'To\'g\'ri email formatini kiriting',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'terms.required' => 'Foydalanish shartlarini qabul qilishingiz kerak',
            'terms.accepted' => 'Foydalanish shartlarini qabul qilishingiz kerak',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Yangi foydalanuvchi yaratish
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Login sahifasiga yo'naltirish
        return redirect('/login')->with('success', 'Muvaffaqiyatli ro\'yxatdan o\'tdingiz! Endi tizimga kiring.');
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

        // Token yaratish
        $token = Str::random(60);

        // Eski tokenlarni o'chirish
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Yangi token saqlash
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Real loyihada bu yerda email yuboriladi
        // Hozircha faqat token ko'rsatamiz
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        return redirect()->back()->with('success', 
            'Parolni tiklash havolasi: ' . $resetUrl . ' (Real loyihada bu email orqali yuboriladi)'
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
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email kiriting',
            'email.email' => 'To\'g\'ri email formatini kiriting',
            'email.exists' => 'Bu email ro\'yxatdan o\'tmagan',
            'password.required' => 'Parol kiriting',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Token tekshirish
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return redirect()->back()->withErrors(['email' => 'Parolni tiklash havolasi topilmadi']);
        }

        // Token muddati tekshirish (1 soat)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->back()->withErrors(['email' => 'Parolni tiklash havolasi muddati tugagan']);
        }

        // Parolni yangilash
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Tokenni o'chirish
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Parol muvaffaqiyatli yangilandi! Endi tizimga kiring.');
    }

    /**
     * Logout qilish
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Tizimdan chiqdingiz');
    }
}
