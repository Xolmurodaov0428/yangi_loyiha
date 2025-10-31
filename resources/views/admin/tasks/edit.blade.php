@extends('layouts.admin')

@section('title', 'Topshiriqni tahrirlash: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fas fa-edit me-2 text-primary"></i>Topshiriqni tahrirlash
            </h1>
            <p class="text-muted mb-0">Topshiriq ma'lumotlarini yangilash</p>
        </div>
        <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fa fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-primary text-white rounded-top-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Asosiy ma'lumotlar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="title" class="form-label fw-medium">Topshiriq nomi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-heading text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $task->title) }}" 
                                       placeholder="Masalan: 1-dars uchun vazifa" required>
                            </div>
                            @error('title')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">Tavsif</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-edit text-muted"></i></span>
                                <textarea class="form-control border-start-0 @error('description') is-invalid @enderror" 
                                         id="description" name="description" rows="4"
                                         placeholder="Topshiriq haqida qo'shimcha izohlar...">{{ old('description', $task->description) }}</textarea>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="due_date" class="form-label fw-medium">Muddat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="far fa-calendar-alt text-muted"></i></span>
                                        <input type="datetime-local" class="form-control border-start-0 @error('due_date') is-invalid @enderror" 
                                               id="due_date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                    @error('due_date')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="status" class="form-label fw-medium">Holat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-toggle-on text-muted"></i></span>
                                        <select class="form-select border-start-0 @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="active" {{ old('status', $task->status) == 'active' ? 'selected' : '' }}>Faol</option>
                                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                                            <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="supervisor_id" class="form-label fw-medium">Rahbar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                <select class="form-select border-start-0 @error('supervisor_id') is-invalid @enderror" 
                                        id="supervisor_id" name="supervisor_id" required>
                                    <option value="">Rahbarni tanlang</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $task->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
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
                            @if($task->file_path)
                                <div class="mt-2">
                                    <small class="text-muted">Hozirgi fayl: 
                                        <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file me-1"></i>{{ basename($task->file_path) }}
                                        </a>
                                    </small>
                                </div>
                            @endif
                            @error('file')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-info text-white rounded-top-3">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Talabalar</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>Topshiriqqa biriktirilgan talabalar ro'yxati.
                        </p>
                        <div class="border rounded-3 p-3 bg-light">
                            <ul class="list-unstyled mb-0">
                                @forelse($task->students as $student)
                                    <li class="mb-2 p-2 bg-white rounded-2 border">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <span class="flex-grow-1">{{ $student->full_name }}</span>
                                            <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted text-center py-3">
                                        <i class="fas fa-users-slash me-2"></i>Hozircha talabalar yo'q
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="mt-3">
                            <div class="alert alert-info py-2 rounded-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Talabalarni qo'shish funksiyasi keyingi versiyada qo'shiladi.</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fa fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fa fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
</style>
@endpush