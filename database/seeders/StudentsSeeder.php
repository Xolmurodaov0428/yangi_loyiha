<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Group;
use App\Models\User;

class StudentsSeeder extends Seeder
{
    public function run()
    {
        $groups = Group::all();
        $supervisors = User::where('role', 'supervisor')->get();

        if ($groups->isEmpty() || $supervisors->isEmpty()) {
            echo "⚠️  Guruhlar yoki rahbarlar mavjud emas. Avval ularni yarating.\n";
            return;
        }

        $studentsData = [
            ['full_name' => 'Aliyev Akbar', 'username' => 'aliyev_akbar', 'group_name' => 'Informatika 1'],
            ['full_name' => 'Valiyev Bekzod', 'username' => 'valiyev_bekzod', 'group_name' => 'Informatika 1'],
            ['full_name' => 'Karimova Dilnoza', 'username' => 'karimova_dilnoza', 'group_name' => 'Informatika 2'],
            ['full_name' => 'Toxirov Eldor', 'username' => 'toxirov_eldor', 'group_name' => 'Informatika 2'],
            ['full_name' => 'Sodiqova Farida', 'username' => 'sodiqova_farida', 'group_name' => 'Matematika 1'],
            ['full_name' => 'Hasanova Gulnora', 'username' => 'hasanova_gulnora', 'group_name' => 'Matematika 1'],
            ['full_name' => 'Rahimov Ilhom', 'username' => 'rahimov_ilh om', 'group_name' => 'Fizika 1'],
            ['full_name' => 'Yuldasheva Jamila', 'username' => 'yuldasheva_jamila', 'group_name' => 'Fizika 1'],
        ];

        foreach ($studentsData as $studentData) {
            $group = $groups->where('name', $studentData['group_name'])->first();
            if ($group && $supervisors->isNotEmpty()) {
                $supervisor = $supervisors->random();

                Student::updateOrCreate(
                    ['username' => $studentData['username']],
                    [
                        'full_name' => $studentData['full_name'],
                        'group_name' => $studentData['group_name'],
                        'group_id' => $group->id,
                        'supervisor_id' => $supervisor->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        echo "✅ Talabalar yaratildi va guruh/rahbarlarga biriktirildi.\n";
    }
}
