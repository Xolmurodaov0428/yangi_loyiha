@extends('layouts.supervisor')

@section('title', 'Yangi topshiriq qo\'shish')

@push('styles')
<style>
    .task-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #0d6efd;
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
        border-radius: 6px;
        padding: 10px;
        background: white;
    }
    .group-badge {
        font-size: 0.85rem;
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">
            <i class="fas fa-tasks me-2 text-primary"></i>Yangi topshiriq qo'shish
        </h1>
        <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <form action="{{ route('supervisor.tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskForm">
        @csrf
        
        <div class="task-section">
            <h5><i class="fas fa-info-circle me-2"></i>Asosiy ma'lumotlar</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Topshiriq nomi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Masalan: 1-dars uchun vazifa" required>
                        </div>
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Muddat <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                        </div>
                        @error('due_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Tavsif</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="far fa-edit"></i></span>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                             id="description" name="description" rows="3"
                             placeholder="Topshiriq haqida qo'shimcha izohlar...">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="file" class="form-label">Fayl biriktirish (ixtiyoriy)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                    <input class="form-control @error('file') is-invalid @enderror" 
                           type="file" id="file" name="file" 
                           accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>
                <div class="form-text">Ruxsat etilgan formatlar: PDF, DOC, DOCX, XLS, XLSX. Maksimal hajmi: 10MB</div>
                @error('file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="task-section">
            <h5><i class="fas fa-users me-2"></i>Kimlar uchun</h5>
            
            <div class="mb-3">
                <label class="form-label d-block">Guruhlarni tanlang <span class="text-danger">*</span></label>
                <div class="alert alert-info py-2">
                    <i class="fas fa-info-circle me-2"></i>Topshiriqni qaysi guruhlarga yuborishni tanlang
                </div>
                
                <div class="border rounded p-3 bg-white">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="select-all-groups">
                        <label class="form-check-label fw-bold" for="select-all-groups">
                            Barcha guruhlarni tanlash
                        </label>
                    </div>
                    
                    <div class="row" id="groups-container">
                        @foreach($groups as $group)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input group-checkbox" 
                                           type="checkbox" 
                                           name="group_ids[]" 
                                           value="{{ $group->id }}" 
                                           id="group-{{ $group->id }}"
                                           {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="group-{{ $group->id }}">
                                        <span class="badge bg-light text-dark me-2">{{ $group->students_count ?? 0 }}</span>
                                        {{ $group->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-2">
                    <small class="text-muted">Tanlangan guruhlar:</small>
                    <div id="selected-groups" class="d-flex flex-wrap mt-1">
                        <span class="text-muted">Guruhlar tanlanmagan</span>
                    </div>
                </div>
                
                @error('group_ids')
                    <div class="alert alert-danger mt-2 py-2">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Talabalarni tanlang</label>
                <div class="alert alert-warning py-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>Iltimos, avval guruhlarni tanlang
                </div>
                
                <div class="student-list" id="students-list">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users-slash fa-2x mb-2"></i>
                        <p class="mb-0">Avval guruh(lar)ni tanlang</p>
                    </div>
                </div>
                <input type="hidden" name="student_ids" id="student_ids" value="">
                
                <div class="mt-2">
                    <small class="text-muted">Tanlangan talabalar:</small>
                    <div id="selected-students" class="d-flex flex-wrap mt-1">
                        <span class="text-muted">Talabalar tanlanmagan</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-times me-2"></i>Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa fa-paper-plane me-2"></i>Yuborish
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: 'Talabalarni tanlang',
        allowClear: true
    });

    // Handle select all groups
    $('#select-all-groups').change(function() {
        $('.group-checkbox').prop('checked', $(this).prop('checked'));
        updateSelectedGroups();
        loadStudents();
    });

    // Handle individual group selection
    $('.group-checkbox').change(function() {
        updateSelectedGroups();
        loadStudents();
    });

    // Update selected groups display
    function updateSelectedGroups() {
        const selectedGroups = [];
        $('.group-checkbox:checked').each(function() {
            const groupId = $(this).val();
            const groupName = $(this).closest('.form-check').find('label').text().trim();
            selectedGroups.push(`<span class="badge bg-primary me-2 mb-2">${groupName}</span>`);
        });

        const container = $('#selected-groups');
        if (selectedGroups.length > 0) {
            container.html(selectedGroups.join(''));
        } else {
            container.html('<span class="text-muted">Guruhlar tanlanmagan</span>');
        }
    }

    // Load students based on selected groups
    function loadStudents() {
        const selectedGroups = $('.group-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedGroups.length === 0) {
            $('#students-list').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users-slash fa-2x mb-2"></i>
                    <p class="mb-0">Avval guruh(lar)ni tanlang</p>
                </div>
            `);
            $('#selected-students').html('<span class="text-muted">Talabalar tanlanmagan</span>');
            return;
        }

        // Show loading state
        $('#students-list').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yuklanmoqda...</span>
                </div>
                <p class="mt-2 mb-0">Talabalar yuklanmoqda...</p>
            </div>
        `);

        // Load students via AJAX
        console.log('Sending request to API with group IDs:', selectedGroups);
        
        $.ajax({
            url: '{{ url("/api/groups/students") }}',
            method: 'POST',
            data: {
                group_ids: selectedGroups,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('API Response:', response);
                if (response.students.length > 0) {
                    let html = `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="select-all-students">
                            <label class="form-check-label fw-bold" for="select-all-students">
                                Barcha talabalarni tanlash
                            </label>
                        </div>
                        <div class="student-list-container">
                    `;

                    response.students.forEach(function(student) {
                        html += `
                            <div class="student-item">
                                <input class="form-check-input student-checkbox" 
                                       type="checkbox" 
                                       name="student_ids[]" 
                                       value="${student.id}" 
                                       id="student-${student.id}">
                                <label class="form-check-label w-100" for="student-${student.id}">
                                    ${student.full_name} 
                                    <span class="text-muted small">(${student.group_name})</span>
                                </label>
                            </div>
                        `;
                    });

                    html += '</div>';
                    $('#students-list').html(html);

                    // Handle select all students
                    $('#select-all-students').change(function() {
                        $('.student-checkbox').prop('checked', $(this).prop('checked'));
                        updateSelectedStudents();
                    });

                    // Handle individual student selection
                    $('.student-checkbox').change(function() {
                        updateSelectedStudents();
                    });

                    updateSelectedStudents();
                } else {
                    $('#students-list').html(`
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p class="mb-0">Tanlangan guruhlarda talabalar topilmadi</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#students-list').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${errorMessage}
                    </div>
                `);
            }
        });
    }

    // Update selected students display
    function updateSelectedStudents() {
        const selectedStudents = [];
        $('.student-checkbox:checked').each(function() {
            const studentId = $(this).val();
            const studentName = $(this).closest('.student-item').find('label').text().trim();
            selectedStudents.push(`<span class="badge bg-success me-2 mb-2">${studentName}</span>`);
        });

        const container = $('#selected-students');
        if (selectedStudents.length > 0) {
            container.html(selectedStudents.join(''));
        } else {
            container.html('<span class="text-muted">Talabalar tanlanmagan</span>');
        }
    }

    // Form submission
    $('#taskForm').on('submit', function(e) {
        const selectedGroups = $('.group-checkbox:checked').length;
        const selectedStudents = $('.student-checkbox:checked').length;
        
        if (selectedGroups === 0) {
            e.preventDefault();
            alert('Iltimos, kamida bitta guruh tanlang');
            return false;
        }
        
        if (selectedStudents === 0 && $('.student-checkbox').length > 0) {
            e.preventDefault();
            alert('Iltimos, kamida bitta talabani tanlang');
            return false;
        }
        
        // Show loading state on submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Yuborilmoqda...
        `);
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        padding-bottom: 0;
    }
    .student-checkbox {
        margin-right: 8px;
    }
    .student-item {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }
    .student-item:last-child {
        border-bottom: none;
    }
    .student-item:hover {
        background-color: #f8f9fa;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6c757d;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Handle select/deselect all groups
    $('#select-all-groups').on('change', function() {
        $('.group-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
    });

    // Handle individual checkbox changes
    $(document).on('change', '.group-checkbox', function() {
        const allChecked = $('.group-checkbox:checked').length === $('.group-checkbox').length;
        $('#select-all-groups').prop('checked', allChecked);
        
        // Update selected groups display
        updateSelectedGroups();
        
        // Load students for selected groups
        loadStudentsForSelectedGroups();
    });
    
    // Function to update selected groups display
    function updateSelectedGroups() {
        const selectedGroups = [];
        $('.group-checkbox:checked').each(function() {
            selectedGroups.push($(this).next('label').text().trim());
        });
        
        const container = $('#selected-groups');
        if (selectedGroups.length === 0) {
            container.html('<p class="text-muted mb-0">Guruhlar tanlanmagan</p>');
        } else {
            container.html(
                selectedGroups.map(group => 
                    `<span class="badge bg-primary me-1 mb-1">${group}</span>`
                ).join('')
            );
        }
    }

    const studentsList = $('#students-list');
    const studentIdsInput = $('#student_ids');
    let selectedStudents = new Set();

    // Function to update selected students display
    function updateSelectedStudents() {
        const studentsContainer = $('#selected-students');
        if (selectedStudents.size === 0) {
            studentsContainer.html('<p class="text-muted mb-0">Talabalar tanlanmagan</p>');
            return;
        }

        let html = '<div class="d-flex flex-wrap gap-2">';
        selectedStudents.forEach(studentId => {
            const student = $(`#student-${studentId}`).data('student');
            if (student) {
                html += `
                    <span class="badge bg-primary d-flex align-items-center">
                        ${student.full_name}
                        <button type="button" class="btn-close btn-close-white ms-2" 
                                onclick="removeStudent(${studentId})" style="font-size: 0.5rem;"></button>
                        <input type="hidden" name="student_ids[]" value="${studentId}">
                    </span>
                `;
            }
        });
        html += '</div>';
        studentsContainer.html(html);
        studentIdsInput.val(Array.from(selectedStudents).join(','));
    }

    // Global function to remove a student
    window.removeStudent = function(studentId) {
        selectedStudents.delete(studentId.toString());
        $(`#student-${studentId}`).prop('checked', false);
        updateSelectedStudents();
    };

    // Function to load students for selected groups
    function loadStudentsForSelectedGroups() {
        const groupIds = [];
        $('.group-checkbox:checked').each(function() {
            groupIds.push($(this).val());
        });
        
        if (groupIds.length === 0) {
            studentsList.html('<div class="text-muted text-center py-3">Guruh(lar)ni tanlang</div>');
            return;
        }

        // Show loading
        studentsList.html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Yuklanmoqda...</span></div> Talabalar yuklanmoqda...</div>');

        // Clear previous selections
        selectedStudents.clear();
        updateSelectedStudents();

        // Fetch students for all selected groups
        const requests = groupIds.map(groupId => 
            fetch(`/api/groups/${groupId}/students`).then(res => res.json())
        );

        Promise.all(requests)
            .then(results => {
                const allStudents = [];
                const studentMap = new Map();
                
                // Combine and deduplicate students
                results.forEach(students => {
                    students.forEach(student => {
                        if (!studentMap.has(student.id)) {
                            studentMap.set(student.id, student);
                            allStudents.push(student);
                        }
                    });
                });

                if (allStudents.length === 0) {
                    studentsList.html('<div class="text-muted text-center py-3">Tanlangan guruhlarda talabalar mavjud emas</div>');
                    return;
                }

                // Sort students by name
                allStudents.sort((a, b) => a.full_name.localeCompare(b.full_name));

                // Build students list
                let html = `
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-students">
                            <label class="form-check-label" for="select-all-students">
                                Barchasini tanlash
                            </label>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                `;

                allStudents.forEach(student => {
                    const isChecked = selectedStudents.has(student.id.toString());
                    html += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input student-checkbox" type="checkbox" 
                                       value="${student.id}" id="student-${student.id}" 
                                       ${isChecked ? 'checked' : ''}>
                                <label class="form-check-label" for="student-${student.id}" style="cursor: pointer;">
                                    ${student.full_name}
                                </label>
                                <input type="hidden" id="student-data-${student.id}" 
                                       value='${JSON.stringify(student)}'>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                studentsList.html(html);

                // Handle student selection
                $('.student-checkbox').on('change', function() {
                    const studentId = $(this).val();
                    const studentData = JSON.parse($(`#student-data-${studentId}`).val());
                    
                    if ($(this).is(':checked')) {
                        selectedStudents.add(studentId);
                        $(`#student-${studentId}`).data('student', studentData);
                    } else {
                        selectedStudents.delete(studentId);
                    }
                    
                    updateSelectedStudents();
                });

                // Handle select all
                $('#select-all-students').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.student-checkbox').prop('checked', isChecked).trigger('change');
                });
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                studentsList.html('<div class="alert alert-danger">Xatolik yuz berdi. Qaytadan urinib ko\'ring.</div>');
            });
    });

    // Handle form submission
    $('#taskForm').on('submit', function() {
        // Update hidden input with selected student IDs
        const studentIds = Array.from(selectedStudents);
        studentIdsInput.val(studentIds.join(','));
        
        if (studentIds.length === 0) {
            alert('Iltimos, kamida bitta talabani tanlang');
            return false;
        }
        
        return true;
    });

    // Initialize selected groups display
    updateSelectedGroups();
    
    // Load students if there are pre-selected groups (for form validation errors)
    if ($('.group-checkbox:checked').length > 0) {
        loadStudentsForSelectedGroups();
    }
});
</script>
@endpush
