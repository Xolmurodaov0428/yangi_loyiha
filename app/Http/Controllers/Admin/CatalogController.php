<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Faculty;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;

class CatalogController extends Controller
{
    // ========== GURUHLAR ==========
    public function groups()
    {
        $groups = Group::with('supervisors')
            ->withCount('students')
            ->paginate(15);
        return view('admin.catalogs.groups', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique within the same faculty
                \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($request) {
                    return $query->where('faculty', $request->faculty);
                }),
            ],
            'faculty' => 'nullable|string|max:255',
            'daily_sessions' => 'required|integer|min:1|max:4',
        ]);

        $validated['student_count'] = 0;
        $validated['is_active'] = true;

        Group::create($validated);

        return redirect()->route('admin.catalogs.groups')
            ->with('success', 'Guruh muvaffaqiyatli qo\'shildi!');
    }

    public function updateGroup(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        // Store group ID in session for modal reopening if validation fails
        session(['editing_group_id' => $id]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique within the same faculty, excluding current group
                \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($request) {
                    return $query->where('faculty', $request->faculty);
                })->ignore($id),
            ],
            'faculty' => 'nullable|string|max:255',
            'daily_sessions' => 'required|integer|min:1|max:4',
        ], [
            'name.required' => 'Guruh nomi majburiy',
            'name.unique' => 'Bu guruh nomi "' . $request->faculty . '" fakultetida allaqachon mavjud',
        ]);

        // Handle is_active checkbox (if not checked, it won't be in request)
        $validated['is_active'] = $request->has('is_active');

        $group->update($validated);

        // Clear the session after successful update
        session()->forget('editing_group_id');

        return redirect()->route('admin.catalogs.groups')
            ->with('success', "Guruh '{$group->name}' muvaffaqiyatli yangilandi!");
    }

    public function deleteGroup(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $studentsCount = $group->students()->count();

        // Agar talabalar bilan birga o'chirish so'ralgan bo'lsa
        if ($request->has('delete_students') && $request->delete_students == '1') {
            // Avval talabalarni o'chirish
            $group->students()->delete();
            $group->delete();

            return redirect()->route('admin.catalogs.groups')
                ->with('success', "Guruh va {$studentsCount} ta talaba muvaffaqiyatli o'chirildi!");
        }

        // Tekshirish: Guruhda talabalar bormi?
        if ($studentsCount > 0) {
            return back()
                ->with('error', 'Bu guruhda talabalar mavjud. Avval talabalarni boshqa guruhga o\'tkazing!')
                ->with('students_count', $studentsCount)
                ->with('group_id', $id)
                ->with('group_name', $group->name);
        }

        $group->delete();

        return redirect()->route('admin.catalogs.groups')
            ->with('success', 'Guruh muvaffaqiyatli o\'chirildi!');
    }

    public function toggleGroup($id)
    {
        $group = Group::findOrFail($id);
        $group->is_active = !$group->is_active;
        $group->save();

        $status = $group->is_active ? 'faollashtirildi' : 'nofaol qilindi';

        return redirect()->route('admin.catalogs.groups')
            ->with('success', "Guruh '{$group->name}' {$status}!");
    }

    public function importGroups(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $imported = 0;
            $errors = [];
            
            $rows = (new FastExcel)->import($file);
            
            foreach ($rows as $index => $row) {
                // Get first two values (column A and B)
                $values = array_values($row);
                $name = $values[0] ?? null;
                $faculty = $values[1] ?? null;
                
                if (empty($name)) continue;
                
                // Check if group with same name exists in the same faculty
                $exists = Group::where('name', $name)
                    ->where('faculty', $faculty)
                    ->exists();
                
                if ($exists) {
                    $errors[] = "Qator " . ($index + 2) . ": Guruh '{$name}' '{$faculty}' fakultetida allaqachon mavjud";
                    continue;
                }
                
                $validator = Validator::make([
                    'name' => $name,
                    'faculty' => $faculty,
                ], [
                    'name' => 'required|string|max:255',
                    'faculty' => 'nullable|string|max:255',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Qator " . ($index + 2) . ": " . $validator->errors()->first();
                    continue;
                }
                
                Group::create([
                    'name' => $name,
                    'faculty' => $faculty,
                    'student_count' => 0,
                    'is_active' => true,
                ]);
                
                $imported++;
            }
            
            $message = "{$imported} ta guruh muvaffaqiyatli yuklandi!";
            if (!empty($errors)) {
                $message .= " Xatolar: " . implode(', ', array_slice($errors, 0, 3));
            }
            
            return redirect()->route('admin.catalogs.groups')->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function downloadGroupsTemplate()
    {
        $data = [
            ['Guruh nomi', 'Fakultet'],
            ['221-20', 'Informatika'],
            ['222-20', 'Matematika'],
            ['223-20', 'Fizika'],
        ];

        return (new FastExcel(collect($data)))->download('guruhlar_namuna.xlsx');
    }

    // ========== TASHKILOTLAR ==========
    public function organizations()
    {
        $organizations = Organization::withCount('students')->paginate(15);
        return view('admin.catalogs.organizations', compact('organizations'));
    }

    public function storeOrganization(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name',
            'address' => 'nullable|string|max:500',
            'radius' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['is_active'] = true;

        Organization::create($validated);

        return redirect()->route('admin.catalogs.organizations')
            ->with('success', 'Tashkilot muvaffaqiyatli qo\'shildi!');
    }

    public function updateOrganization(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $id,
            'address' => 'nullable|string|max:500',
            'radius' => 'nullable|numeric|min:0|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $organization->update($validated);

        return redirect()->route('admin.catalogs.organizations')
            ->with('success', 'Tashkilot muvaffaqiyatli yangilandi!');
    }

    public function deleteOrganization($id)
    {
        $organization = Organization::findOrFail($id);

        // Tekshirish: Tashkilotda talabalar bormi?
        if ($organization->students()->count() > 0) {
            return back()->with('error', 'Bu tashkilotda talabalar mavjud. Avval talabalarni boshqa tashkilotga o\'tkazing!');
        }

        $organization->delete();

        return redirect()->route('admin.catalogs.organizations')
            ->with('success', 'Tashkilot muvaffaqiyatli o\'chirildi!');
    }

    public function importOrganizations(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $imported = 0;
            $errors = [];
            
            $rows = (new FastExcel)->import($file);
            
            foreach ($rows as $index => $row) {
                $values = array_values($row);
                $name = $values[0] ?? null;
                $address = $values[1] ?? null;
                $phone = $values[2] ?? null;
                $email = $values[3] ?? null;
                
                if (empty($name)) continue;
                
                $validator = Validator::make([
                    'name' => $name,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                ], [
                    'name' => 'required|string|max:255|unique:organizations,name',
                    'address' => 'nullable|string|max:500',
                    'phone' => 'nullable|string|max:20',
                    'email' => 'nullable|email|max:255',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Qator " . ($index + 2) . ": " . $validator->errors()->first();
                    continue;
                }
                
                Organization::create([
                    'name' => $name,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                    'is_active' => true,
                ]);
                
                $imported++;
            }
            
            $message = "{$imported} ta tashkilot muvaffaqiyatli yuklandi!";
            if (!empty($errors)) {
                $message .= " Xatolar: " . implode(', ', array_slice($errors, 0, 3));
            }
            
            return redirect()->route('admin.catalogs.organizations')->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function downloadOrganizationsTemplate()
    {
        $data = [
            ['Tashkilot nomi', 'Manzil', 'Telefon', 'Email'],
            ['Toshkent IT Park', 'Amir Temur 108', '+998901234567', 'info@itpark.uz'],
            ['Milliy kutubxona', 'Navoiy ko\'chasi', '+998712345678', 'library@uz'],
            ['Texnika universiteti', 'Universitetskaya 2', '+998713456789', 'info@tdtu.uz'],
        ];

        return (new FastExcel(collect($data)))->download('tashkilotlar_namuna.xlsx');
    }

    public function organizationStudents($id)
    {
        $organization = Organization::findOrFail($id);
        $students = Student::where('organization_id', $id)
            ->with(['group.supervisors', 'supervisor'])
            ->paginate(20);
        
        return view('admin.catalogs.organization-students', compact('organization', 'students'));
    }

    // ========== FAKULTETLAR ==========
    public function faculties()
    {
        $faculties = Faculty::withCount(['students', 'groups'])->paginate(15);
        return view('admin.catalogs.faculties', compact('faculties'));
    }

    public function storeFaculty(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name',
            'code' => 'nullable|string|max:50|unique:faculties,code',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = true;

        Faculty::create($validated);

        return redirect()->route('admin.catalogs.faculties')
            ->with('success', 'Fakultet muvaffaqiyatli qo\'shildi!');
    }

    public function updateFaculty(Request $request, $id)
    {
        $faculty = Faculty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faculties,name,' . $id,
            'code' => 'nullable|string|max:50|unique:faculties,code,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.catalogs.faculties')
            ->with('success', 'Fakultet muvaffaqiyatli yangilandi!');
    }

    public function deleteFaculty($id)
    {
        $faculty = Faculty::findOrFail($id);

        // Tekshirish: Fakultetda talabalar bormi?
        if ($faculty->students()->count() > 0) {
            return back()->with('error', 'Bu fakultetda talabalar mavjud. Avval talabalarni boshqa fakultetga o\'tkazing!');
        }

        $faculty->delete();

        return redirect()->route('admin.catalogs.faculties')
            ->with('success', 'Fakultet muvaffaqiyatli o\'chirildi!');
    }

    public function importFaculties(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $imported = 0;
            $errors = [];
            
            $rows = (new FastExcel)->import($file);
            
            foreach ($rows as $index => $row) {
                $values = array_values($row);
                $name = $values[0] ?? null;
                $code = $values[1] ?? null;
                $description = $values[2] ?? null;
                
                if (empty($name)) continue;
                
                $validator = Validator::make([
                    'name' => $name,
                    'code' => $code,
                    'description' => $description,
                ], [
                    'name' => 'required|string|max:255|unique:faculties,name',
                    'code' => 'nullable|string|max:50|unique:faculties,code',
                    'description' => 'nullable|string|max:1000',
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Qator " . ($index + 2) . ": " . $validator->errors()->first();
                    continue;
                }
                
                Faculty::create([
                    'name' => $name,
                    'code' => $code,
                    'description' => $description,
                    'is_active' => true,
                ]);
                
                $imported++;
            }
            
            $message = "{$imported} ta fakultet muvaffaqiyatli yuklandi!";
            if (!empty($errors)) {
                $message .= " Xatolar: " . implode(', ', array_slice($errors, 0, 3));
            }
            
            return redirect()->route('admin.catalogs.faculties')->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function downloadFacultiesTemplate()
    {
        $data = [
            ['Fakultet nomi', 'Kod', 'Tavsif'],
            ['Informatika', 'IT', 'Kompyuter fanlari va axborot texnologiyalari'],
            ['Matematika', 'MATH', 'Amaliy matematika va statistika'],
            ['Iqtisodiyot', 'ECON', 'Iqtisodiyot va menejment'],
        ];

        return (new FastExcel(collect($data)))->download('fakultetlar_namuna.xlsx');
    }
}