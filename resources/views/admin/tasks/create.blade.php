@extends('layouts.admin')

@section('title', 'Yangi topshiriq qo\'shish')

@push('styles')
<style>
    .task-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .task-section h5 {
        color: #0d6efd;
        margin-bottom: 1.25rem;
        font-weight: 600;
    }
    .section-divider {
        border-top: 1px dashed #dee2e6;
        margin: 1.5rem 0;
    }
    .student-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background: white;
    }
    .group-badge {
        font-size: 0.85rem;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }
    .step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        right: -50%;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }
    .step.active .step-number {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .step.completed .step-number {
        background: #198754;
        color: white;
        border-color: #198754;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-weight: bold;
        position: relative;
        z-index: 2;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fas fa-tasks me-2 text-primary"></i>Yangi topshiriq qo'shish
            </h1>
            <p class="text-muted mb-0">Yangi topshiriq yaratish uchun quyidagi ma'lumotlarni kiriting</p>
        </div>
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <!-- Step Indicator -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="step-indicator">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-label">Asosiy ma'lumotlar</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label">Guruh va talabalar</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Tasdiqlash</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskForm">
        @csrf
        
        <div class="task-section">
            <h5><i class="fas fa-info-circle me-2"></i>Asosiy ma'lumotlar</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-medium">Topshiriq nomi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-heading text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Masalan: 1-dars uchun vazifa" required>
                        </div>
                        @error('title')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="due_date" class="form-label fw-medium">Muddat <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="far fa-calendar-alt text-muted"></i></span>
                            <input type="datetime-local" class="form-control border-start-0 @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                        </div>
                        @error('due_date')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="description" class="form-label fw-medium">Tavsif</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="far fa-edit text-muted"></i></span>
                    <textarea class="form-control border-start-0 @error('description') is-invalid @enderror" 
                             id="description" name="description" rows="4"
                             placeholder="Topshiriq haqida qo'shimcha izohlar...">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="supervisor_id" class="form-label fw-medium">Rahbar <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                    <select class="form-select border-start-0 @error('supervisor_id') is-invalid @enderror" 
                            id="supervisor_id" name="supervisor_id" required>
                        <option value="">Rahbarni tanlang</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('supervisor_id')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="file" class="form-label fw-medium">Fayl biriktirish (ixtiyoriy)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-paperclip text-muted"></i></span>
                    <input class="form-control border-start-0 @error('file') is-invalid @enderror" 
                           type="file" id="file" name="file" 
                           accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>
                <div class="form-text">Ruxsat etilgan formatlar: PDF, DOC, DOCX, XLS, XLSX. Maksimal hajmi: 10MB</div>
                @error('file')
                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="task-section">
            <h5><i class="fas fa-users me-2"></i>Kimlar uchun</h5>
            
            <div class="mb-4">
                <label class="form-label d-block fw-medium">Guruhlarni tanlang <span class="text-danger">*</span></label>
                <div class="alert alert-info py-2 rounded-3">
                    <i class="fas fa-info-circle me-2"></i>Topshiriqni qaysi guruhlarga yuborishni tanlang
                </div>
                
                <div class="border rounded-3 p-3 bg-white">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="select-all-groups">
                        <label class="form-check-label fw-bold" for="select-all-groups">
                            Barcha guruhlarni tanlash
                        </label>
                    </div>
                    
                    <div class="row" id="groups-container">
                        @foreach($groups as $group)
                            <div class="col-md-4 mb-3">
                                <div class="form-check p-3 border rounded-3 bg-light">
                                    <input class="form-check-input group-checkbox" 
                                           type="checkbox" 
                                           name="group_ids[]" 
                                           value="{{ $group->id }}" 
                                           id="group-{{ $group->id }}"
                                           {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="group-{{ $group->id }}">
                                        <span>{{ $group->name }}</span>
                                        <span class="badge bg-primary">{{ $group->students_count ?? 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted fw-medium">Mavjud guruhlar:</small>
                    <div id="selected-groups" class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($groups as $group)
                            <span class="badge bg-light text-dark">{{ $group->name }}</span>
                        @endforeach
                    </div>
                </div>
                
                @error('group_ids')
                    <div class="alert alert-danger mt-3 py-2 rounded-3">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-medium">Talabalarni tanlang</label>
                <div class="alert alert-warning py-2 rounded-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>Iltimos, avval guruhlarni tanlang
                </div>
                
                <div class="student-list" id="students-list">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users-slash fa-2x mb-3"></i>
                        <p class="mb-0">Avval guruh(lar)ni tanlang</p>
                    </div>
                </div>
                <input type="hidden" name="student_ids" id="student_ids" value="">
                
                <div class="mt-3">
                    <small class="text-muted fw-medium">Tanlangan talabalar:</small>
                    <div id="selected-students" class="d-flex flex-wrap gap-2 mt-2">
                        <span class="text-muted">Talabalar tanlanmagan</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa fa-times me-2"></i>Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                <i class="fa fa-paper-plane me-2"></i>Yuborish
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Select all groups functionality
        $('#select-all-groups').change(function() {
            $('.group-checkbox').prop('checked', $(this).prop('checked'));
            loadStudentsForSelectedGroups();
        });
        
        // Individual group checkbox change
        $('.group-checkbox').change(function() {
            // Update select all checkbox state
            const allChecked = $('.group-checkbox').length === $('.group-checkbox:checked').length;
            $('#select-all-groups').prop('checked', allChecked);
            
            loadStudentsForSelectedGroups();
        });
        
        // Load students for selected groups
        function loadStudentsForSelectedGroups() {
            const selectedGroupIds = $('.group-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedGroupIds.length === 0) {
                $('#students-list').html(`
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-users-slash fa-2x mb-3"></i>
                        <p class="mb-0">Avval guruh(lar)ni tanlang</p>
                    </div>
                `);
                $('#selected-students').html('<span class="text-muted">Talabalar tanlanmagan</span>');
                return;
            }
            
            // Show loading state
            $('#students-list').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yuklanmoqda...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted">Talabalar yuklanmoqda...</p>
                </div>
            `);
            
            // In a real implementation, you would make an AJAX call to fetch students
            // For now, we'll simulate this with a simple message
            setTimeout(() => {
                $('#students-list').html(`
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h6>Talabalarni tanlash funksiyasi</h6>
                        <p class="mb-0"><small>Barcha guruh talabalari uchun topshiriq yaratiladi</small></p>
                    </div>
                `);
            }, 500);
        }
        
        // Initialize on page load
        loadStudentsForSelectedGroups();
    });
</script>
@endpush