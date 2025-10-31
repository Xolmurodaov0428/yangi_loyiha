@extends('layouts.admin')

@section('title', 'Topshiriq: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fas fa-tasks me-2 text-primary"></i>Topshiriq: {{ $task->title }}
            </h1>
            <p class="text-muted mb-0">Topshiriq tafsilotlari va talabalarning progressi</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning shadow-sm">
                <i class="fa fa-edit me-2"></i>Tahrirlash
            </a>
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white rounded-top-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Topshiriq ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-heading text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Topshiriq nomi</small>
                                    <h6 class="mb-0">{{ $task->title }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-user-tie text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Rahbar</small>
                                    <h6 class="mb-0">{{ $task->supervisor->name ?? 'Noma\'lum' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-users text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Guruh</small>
                                    <h6 class="mb-0">{{ $task->group->name ?? 'Noma\'lum' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock'],
                                            'active' => ['class' => 'bg-primary', 'icon' => 'fas fa-play'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'fas fa-check'],
                                            'cancelled' => ['class' => 'bg-secondary', 'icon' => 'fas fa-times']
                                        ][$task->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question'];
                                    @endphp
                                    <i class="{{ $statusConfig['icon'] }} text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Holati</small>
                                    <span class="badge {{ $statusConfig['class'] }} d-inline-flex align-items-center">
                                        <i class="{{ $statusConfig['icon'] }} me-1"></i>
                                        {{ __('task.status.' . $task->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="far fa-calendar-plus text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Yaratilgan sana</small>
                                    <h6 class="mb-0">{{ $task->created_at->format('d.m.Y H:i') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="far fa-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Muddati</small>
                                    <h6 class="mb-0">{{ $task->due_date->format('d.m.Y H:i') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex align-items-start">
                            <div class="bg-light rounded-circle p-3 me-3">
                                <i class="far fa-file-alt text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block mb-1">Tavsif</small>
                                <div class="border rounded-3 p-3 bg-light mt-2">
                                    {{ $task->description ?? 'Tavsif kiritilmagan' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($task->file_path)
                        <div class="mt-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-paperclip text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Biriktirilgan fayl</small>
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm">
                                            <i class="fas fa-download me-2"></i>{{ basename($task->file_path) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-info text-white rounded-top-3">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Talabalar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ism sharif</th>
                                    <th>Guruh</th>
                                    <th>Holati</th>
                                    <th>Topshirilgan sana</th>
                                    <th>Baholash</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($task->students as $index => $student)
                                    <tr class="align-middle">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-medium">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                                <span>{{ $student->full_name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $student->group->name ?? 'Noma\'lum' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $pivot = $student->pivot;
                                                $statusConfig = [
                                                    'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock'],
                                                    'submitted' => ['class' => 'bg-success', 'icon' => 'fas fa-check'],
                                                    'late' => ['class' => 'bg-danger', 'icon' => 'fas fa-exclamation-triangle']
                                                ][$pivot->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question'];
                                            @endphp
                                            <span class="badge {{ $statusConfig['class'] }} d-inline-flex align-items-center">
                                                <i class="{{ $statusConfig['icon'] }} me-1"></i>
                                                {{ __('task.student_status.' . $pivot->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $pivot->submitted_at ? $pivot->submitted_at->format('d.m.Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            @if($pivot->status === 'submitted')
                                                <span class="badge bg-success">{{ $pivot->score ?? '-' }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="py-3">
                                                <i class="fas fa-users-slash fa-2x text-muted mb-2"></i>
                                                <h6 class="text-muted">Hali hech qanday talaba topshiriqqa biriktirilmagan</h6>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-success text-white rounded-top-3">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistika</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalStudents = $task->students->count();
                        $submittedStudents = $task->students->where('pivot.status', 'submitted')->count();
                        $lateStudents = $task->students->where('pivot.status', 'late')->count();
                        $pendingStudents = $task->students->where('pivot.status', 'pending')->count();
                        $completionRate = $totalStudents > 0 ? ($submittedStudents / $totalStudents) * 100 : 0;
                    @endphp
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <div class="circular-progress" data-percent="{{ round($completionRate) }}">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="8"></circle>
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#28a745" stroke-width="8" 
                                            stroke-dasharray="339.292" 
                                            stroke-dashoffset="{{ 339.292 * (1 - $completionRate / 100) }}" 
                                            transform="rotate(-90 60 60)"></circle>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="h5 mb-0 fw-bold">{{ round($completionRate) }}%</span>
                                    <div class="small text-muted">Bajarildi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <i class="fas fa-users me-2 text-primary"></i>
                                <span class="fw-medium">Jami talabalar</span>
                            </div>
                            <span class="badge bg-primary fs-6">{{ $totalStudents }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                <span class="fw-medium">Topshirganlar</span>
                            </div>
                            <span class="badge bg-success fs-6">{{ $submittedStudents }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                <span class="fw-medium">Kechikkanlar</span>
                            </div>
                            <span class="badge bg-warning fs-6">{{ $lateStudents }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <i class="fas fa-clock me-2 text-secondary"></i>
                                <span class="fw-medium">Topshirmaganlar</span>
                            </div>
                            <span class="badge bg-secondary fs-6">{{ $pendingStudents }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-danger text-white rounded-top-3">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Topshiriqni o'chirish</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Ushbu topshiriqni butunlay o'chirish. Bu amalni ortga qaytarib bo'lmaydi.
                    </p>
                    <button type="button" class="btn btn-danger w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa fa-trash me-2"></i>Topshiriqni o'chirish
                    </button>
                    
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Topshiriqni o'chirish</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>"{{ $task->title }}" nomli topshiriqni o'chirmoqchimisiz?</p>
                                    <p class="text-muted small">Bu amalni ortga qaytarib bo'lmaydi.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">O'chirish</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    .circular-progress {
        display: inline-block;
    }
    .circular-progress svg circle:last-child {
        transition: stroke-dashoffset 0.5s ease;
    }
</style>
@endpush