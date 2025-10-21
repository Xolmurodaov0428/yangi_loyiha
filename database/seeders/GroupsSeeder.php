<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;

class GroupsSeeder extends Seeder
{
    public function run()
    {
        // Guruhlar yaratish
        $groups = [
            ['name' => 'Informatika 1', 'faculty' => 'Informatika'],
            ['name' => 'Informatika 2', 'faculty' => 'Informatika'],
            ['name' => 'Matematika 1', 'faculty' => 'Matematika'],
            ['name' => 'Fizika 1', 'faculty' => 'Fizika'],
        ];

        foreach ($groups as $groupData) {
            $group = Group::updateOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
        }

        // Rahbarlarni guruhlarga biriktirish
        $supervisors = User::where('role', 'supervisor')->get();
        $groups = Group::all();

        if ($supervisors->isNotEmpty() && $groups->isNotEmpty()) {
            foreach ($supervisors as $supervisor) {
                // Har bir rahbarga 1-2 ta guruh biriktirish
                $randomGroups = $groups->random(min(2, $groups->count()));
                foreach ($randomGroups as $group) {
                    $supervisor->groups()->syncWithoutDetaching($group->id);
                }
            }
        }

        echo "✅ Guruhlar yaratildi va rahbarlarga biriktirildi.\n";
    }
}
