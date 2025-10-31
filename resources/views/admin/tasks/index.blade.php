@extends('layouts.admin')

@section('title', 'Topshiriqlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fas fa-tasks me-2 text-primary"></i>Topshiriqlar
            </h1>
            <p class="text-muted mb-0">Tizimdagi barcha topshiriqlarni boshqarish</p>
        </div>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary shadow-sm">
            <i class="fa fa-plus me-2"></i>Yangi topshiriq
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">Nomi</th>
                            <th class="py-3">Rahbar</th>
                            <th class="py-3">Guruh</th>
                            <th class="py-3">Muddat</th>
                            <th class="py-3">Holati</th>
                            <th class="py-3">Progress</th>
                            <th class="py-3 text-end">Harakatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $index => $task)
                            <tr class="align-middle">
                                <td class="py-3">{{ $tasks->firstItem() + $index }}</td>
                                <td class="py-3 fw-medium">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                        <span>{{ $task->title }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user-tie text-muted"></i>
                                        </div>
                                        <span>{{ $task->supervisor->name ?? 'Noma\'lum' }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark">{{ $task->group->name ?? 'Noma\'lum' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar me-2 text-muted"></i>
                                        <span>{{ $task->due_date->format('d.m.Y H:i') }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock'],
                                            'active' => ['class' => 'bg-primary', 'icon' => 'fas fa-play'],
                                            'completed' => ['class' => 'bg-success', 'icon' => 'fas fa-check'],
                                            'cancelled' => ['class' => 'bg-secondary', 'icon' => 'fas fa-times']
                                        ][$task->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question'];
                                    @endphp
                                    <span class="badge {{ $statusConfig['class'] }} d-flex align-items-center">
                                        <i class="{{ $statusConfig['icon'] }} me-1"></i>
                                        {{ __('task.status.' . $task->status) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $totalStudents = $task->students->count();
                                        $submittedStudents = $task->students->where('pivot.status', 'submitted')->count();
                                        $progress = $totalStudents > 0 ? ($submittedStudents / $totalStudents) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                        <span class="small text-muted">{{ $submittedStudents }}/{{ $totalStudents }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-end">
                                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                                        <a href="{{ route('admin.tasks.show', $task) }}" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Ko'rish">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.tasks.edit', $task) }}" 
                                           class="btn btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Tahrirlash">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $task->id }}"
                                                title="O'chirish">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Hali hech qanday topshiriq mavjud emas</h5>
                                        <p class="text-muted mb-4">Yangi topshiriq yaratish uchun "Yangi topshiriq" tugmasini bosing</p>
                                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus me-2"></i>Yangi topshiriq yaratish
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tasks->hasPages())
                <div class="d-flex justify-content-center border-top pt-4 mt-0">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    .table > :not(caption) > * > * {
        padding: 0.75rem 1rem;
    }
    .progress {
        border-radius: 4px;
    }
    .btn-group .btn {
        border-radius: 4px !important;
    }
</style>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush