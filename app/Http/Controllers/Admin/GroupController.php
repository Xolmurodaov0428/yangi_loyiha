<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Update group
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $group->update($validated);

        // Update student_count
        $group->update(['student_count' => $group->students()->count()]);

        // Log activity
        ActivityLog::log('update', "Guruh yangilandi: {$group->name}", 'Group', $group->id);

        return redirect()->route('admin.students.index')
            ->with('success', 'Guruh muvaffaqiyatli yangilandi');
    }

    /**
     * Delete group
     */
    public function destroy(Group $group)
    {
        $groupName = $group->name;
        
        // Check if group has students
        if ($group->students()->count() > 0) {
            return back()->with('error', 'Bu guruhda talabalar mavjud. Avval talabalarni o\'chiring yoki boshqa guruhga o\'tkazing.');
        }

        $group->delete();

        // Log activity
        ActivityLog::log('delete', "Guruh o'chirildi: {$groupName}", 'Group');

        return redirect()->route('admin.students.index')
            ->with('success', 'Guruh muvaffaqiyatli o\'chirildi');
    }
}
