@extends('layouts.supervisor')

@section('title', $task->title . ' - Tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Tahrirlash: {{ $task->title }}</h1>
        <a href="{{ route('supervisor.tasks.show', $task) }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('supervisor.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Sarlavha <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Tavsif <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                     id="description" name="description" rows="5" required>{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Muddat <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" 
                                           value="{{ old('due_date', $task->due_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Holati <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $task->status) == 'active' ? 'selected' : '' }}>Faol</option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                                        <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Fayl almashtirish (ixtiyoriy)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                   id="file" name="file">
                            @if($task->file_path)
                                <div class="mt-2">
                                    <span class="d-block mb-1">Hozirgi fayl:</span>
                                    <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-download me-1"></i>Yuklab olish
                                    </a>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_file" id="remove_file">
                                        <label class="form-check-label" for="remove_file">
                                            Faylni o'chirish
                                        </label>
                                    </div>
                                </div>
                            @endif
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Talabalar</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">Guruh <span class="text-danger">*</span></label>
                                    <select class="form-select @error('group_id') is-invalid @enderror" 
                                            id="group_id" name="group_id" required
                                            {{ $task->students->count() > 0 ? 'disabled' : '' }}>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}" 
                                                {{ old('group_id', $task->group_id) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }} ({{ $group->students_count }} ta talaba)
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($task->students->count() > 0)
                                        <div class="form-text text-warning">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            O'zgartirish uchun avval barcha talabalarni olib tashlang
                                        </div>
                                    @endif
                                    @error('group_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Talabalar <span class="text-danger">*</span></label>
                                    <div id="students-container">
                                        @if($task->students->count() > 0)
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle me-2"></i>
                                                {{ $task->students_count }} ta talaba tanlangan
                                                <a href="{{ route('supervisor.tasks.show', $task) }}" class="alert-link">
                                                    Ko'rish
                                                </a>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle me-2"></i>
                                                Iltimos, talabalarni tanlang
                                            </div>
                                            <div class="list-group" id="students-list">
                                                @foreach($groups->firstWhere('id', $task->group_id)->students ?? [] as $student)
                                                    <label class="list-group-item d-flex align-items-center">
                                                        <input class="form-check-input me-2" type="checkbox" 
                                                               name="student_ids[]" value="{{ $student->id }}"
                                                               {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                                                        <span>{{ $student->full_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @error('student_ids')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i>Saqlash
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            @if($task->students->count() === 0)
                <div class="mt-4">
                    <form action="{{ route('supervisor.tasks.destroy', $task) }}" method="POST" 
                          onsubmit="return confirm('Haqiqatan ham ushbu topshiriqni o\'chirmoqchimisiz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash me-2"></i>O'chirish
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('group_id');
        const studentsContainer = document.getElementById('students-container');
        
        // Only add event listener if students list is empty (editing mode with no students)
        const shouldAddListener = <?php echo $task->students->count() === 0 ? 'true' : 'false'; ?>;
        if (groupSelect && shouldAddListener) {
            groupSelect.addEventListener('change', function() {
                const groupId = this.value;
                
                if (!groupId) {
                    studentsContainer.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>Iltimos, avval guruhni tanlang
                        </div>`;
                    return;
                }
                
                // Show loading
                studentsContainer.innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yuklanmoqda...</span>
                        </div>
                        <p class="mt-2 mb-0">Talabalar yuklanmoqda...</p>
                    </div>`;
                
                // Fetch students for the selected group
                fetch(`/api/v1/groups/${groupId}/students`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            studentsContainer.innerHTML = `
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle me-2"></i>Ushbu guruhda talabalar mavjud emas
                                </div>`;
                            return;
                        }
                        
                        let html = '<div class="list-group" id="students-list">';
                        data.forEach(student => {
                            html += `
                                <label class="list-group-item d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" 
                                           name="student_ids[]" value="${student.id}" 
                                           checked>
                                    <span>${student.full_name}</span>
                                </label>`;
                        });
                        html += '</div>';
                        
                        studentsContainer.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        studentsContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle me-2"></i>Talabalarni yuklashda xatolik yuz berdi
                            </div>`;
                    });
            });
        }
    });
</script>
@endpush
@endsection
