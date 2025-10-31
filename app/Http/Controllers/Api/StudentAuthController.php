<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    /**
     * Talaba login qilish
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Talabani username bo'yicha topish
        $student = Student::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        // Agar talaba topilmasa yoki parol noto'g'ri bo'lsa
        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'username' => ['Login yoki parol noto\'g\'ri'],
            ]);
        }

        // Token yaratish
        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli kirildi',
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'username' => $student->username,
                'group_name' => $student->group_name,
                'faculty' => $student->faculty,
                'organization' => $student->organization ? $student->organization->name : null,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Talaba logout qilish
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli chiqildi',
        ]);
    }

    /**
     * Talaba ma'lumotlarini olish
     */
    public function profile(Request $request)
    {
        $student = $request->user();

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'username' => $student->username,
                'group_name' => $student->group_name,
                'faculty' => $student->faculty,
                'organization' => $student->organization ? $student->organization->name : null,
                'internship_start_date' => $student->internship_start_date,
                'internship_end_date' => $student->internship_end_date,
            ],
        ]);
    }
}
