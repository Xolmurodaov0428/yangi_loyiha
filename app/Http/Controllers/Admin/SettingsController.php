<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Joriy parol noto\'g\'ri!');
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil muvaffaqiyatli yangilandi!');
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_locale' => 'required|in:uz,ru,en',
            'dark_mode' => 'nullable|boolean',
        ]);

        Setting::set('app_name', $request->app_name);
        Setting::set('app_locale', $request->app_locale);
        Setting::set('dark_mode', $request->has('dark_mode') ? '1' : '0');

        return back()->with('success', 'Tizim sozlamalari yangilandi!');
    }

    public function updateTelegram(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
        ]);

        Setting::set('telegram_bot_token', $request->telegram_bot_token ?? '');
        Setting::set('telegram_chat_id', $request->telegram_chat_id ?? '');

        return back()->with('success', 'Telegram sozlamalari yangilandi!');
    }

    public function backup(Request $request)
    {
        try {
            // Create backup filename
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups');
            
            // Create backups directory if not exists
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $filename;

            // Get database credentials from .env
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');

            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );

            // Execute backup
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return back()->with('error', 'Backup yaratishda xatolik yuz berdi!');
            }

            // Send to Telegram if configured
            $botToken = Setting::get('telegram_bot_token');
            $chatId = Setting::get('telegram_chat_id');

            if ($botToken && $chatId && file_exists($filePath)) {
                $this->sendToTelegram($botToken, $chatId, $filePath, $filename);
            }

            return back()->with('success', "Backup muvaffaqiyatli yaratildi: {$filename}");

        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    private function sendToTelegram($botToken, $chatId, $filePath, $filename)
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendDocument";
            
            $response = Http::attach(
                'document', 
                file_get_contents($filePath), 
                $filename
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => "🗄️ Database Backup\n📅 " . date('d.m.Y H:i:s')
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
