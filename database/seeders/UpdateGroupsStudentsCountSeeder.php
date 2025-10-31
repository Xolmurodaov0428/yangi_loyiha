<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateGroupsStudentsCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update students_count for all groups
        \App\Models\Group::all()->each(function ($group) {
            $group->students_count = $group->students()->count();
            $group->save();
        });
    }
}
