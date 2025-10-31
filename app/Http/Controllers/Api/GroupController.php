<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Get students by group ID
     *
     * @param  int  $groupId
     * @return \Illuminate\Http\Response
     */
    public function getStudents($groupId)
    {
        $group = Group::findOrFail($groupId);
        
        $students = $group->students()
            ->select('id', 'full_name', 'group_id')
            ->orderBy('full_name')
            ->get();
            
        return response()->json($students);
    }

    /**
     * Get students by multiple group IDs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStudentsByGroups(Request $request)
    {
        $request->validate([
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $students = Student::whereIn('group_id', $request->group_ids)
            ->with('group:id,name')
            ->select('id', 'full_name', 'group_id')
            ->orderBy('full_name')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'group_id' => $student->group_id,
                    'group_name' => $student->group->name,
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }
}
