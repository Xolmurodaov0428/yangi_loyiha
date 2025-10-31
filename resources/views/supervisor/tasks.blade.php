@extends('layouts.supervisor')

@section('title', 'Topshiriqlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Topshiriqlar</h1>
        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
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
                            <th>Guruh</th>
                            <th>Talabalar soni</th>
                            <th>Topshiriqlar</th>
                            <th>Harakatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $index => $group)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->students->count() }}</td>
                                <td>0</td>
                                <td>
                                    <a href="{{ route('supervisor.students.group', $group->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye me-1"></i> Ko'rish
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="py-4 text-muted">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Hech qanday guruh topilmadi</p>
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

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yangi topshiriq qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    @csrf
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Topshiriq nomi</label>
                        <input type="text" class="form-control" id="taskTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Tavsif</label>
                        <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskDeadline" class="form-label">Muddat</label>
                        <input type="date" class="form-control" id="taskDeadline" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskGroup" class="form-label">Guruh</label>
                        <select class="form-select" id="taskGroup" required>
                            <option value="" selected disabled>Guruhni tanlang</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" id="saveTaskBtn">Saqlash</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any necessary JavaScript here
        // For example, handling the modal and form submission
        
        // Example: Show add task modal
        const addTaskBtn = document.getElementById('addTaskBtn');
        if (addTaskBtn) {
            addTaskBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('addTaskModal'));
                modal.show();
            });
        }
        
        // Example: Handle form submission
        const saveTaskBtn = document.getElementById('saveTaskBtn');
        if (saveTaskBtn) {
            saveTaskBtn.addEventListener('click', function() {
                // Add your form submission logic here
                // You can use fetch API to submit the form data
                alert('Topshiriq qo\'shish funksiyasi ishga tushurildi');
            });
        }
    });
</script>
@endpush

@endsection
