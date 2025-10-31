@extends('layouts.supervisor')

@section('title', 'Topshiriqlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Topshiriqlar</h1>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-2"></i>Yangi topshiriq
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nomi</th>
                            <th>Guruh</th>
                            <th>Muddat</th>
                            <th>Holati</th>
                            <th>Topshirganlar</th>
                            <th>Harakatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $index => $task)
                            <tr>
                                <td>{{ $tasks->firstItem() + $index }}</td>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->group->name ?? 'Noma\'lum' }}</td>
                                <td>{{ $task->due_date->format('d.m.Y H:i') }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'pending' => 'badge bg-warning',
                                            'active' => 'badge bg-primary',
                                            'completed' => 'badge bg-success',
                                            'cancelled' => 'badge bg-secondary'
                                        ][$task->status] ?? 'badge bg-secondary';
                                    @endphp
                                    <span class="{{ $statusClass }}">
                                        {{ __('task.status.' . $task->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $task->students->where('pivot.status', 'submitted')->count() }}
                                    / {{ $task->students->count() }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('supervisor.tasks.show', $task) }}" 
                                           class="btn btn-sm btn-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Ko'rish">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('supervisor.tasks.edit', $task) }}" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Tahrirlash">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('supervisor.tasks.destroy', $task) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Haqiqatan ham ushbu topshiriqni o\'chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="O'chirish">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="py-4 text-muted">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Hali hech qanday topshiriq mavjud emas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tasks->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
