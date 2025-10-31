@extends('layouts.supervisor')

@section('title', $task->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">{{ $task->title }}</h1>
        <div>
            <a href="{{ route('supervisor.tasks.edit', $task) }}" class="btn btn-warning me-2">
                <i class="fa fa-edit me-2"></i>Tahrirlash
            </a>
            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Tavsif</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($task->description)) !!}
                    
                    @if($task->file_path)
                        <div class="mt-4">
                            <h6 class="fw-bold">Biriktirilgan fayl:</h6>
                            <a href="{{ Storage::url($task->file_path) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fa fa-download me-2"></i>Yuklab olish
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Topshiriq holati</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Talaba</th>
                                    <th>Holati</th>
                                    <th>Topshirilgan vaqti</th>
                                    <th>Fayl</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($task->students as $student)
                                    @php
                                        $pivot = $student->pivot;
                                        $statusClass = [
                                            'pending' => 'badge bg-warning',
                                            'submitted' => 'badge bg-info',
                                            'completed' => 'badge bg-success',
                                            'rejected' => 'badge bg-danger'
                                        ];
                                        $statusText = [
                                            'pending' => 'Kutilmoqda',
                                            'submitted' => 'Topshirildi',
                                            'completed' => 'Qabul qilindi',
                                            'rejected' => 'Qaytarildi'
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $student->full_name }}</td>
                                        <td>
                                            <span class="{{ $statusClass[$pivot->status] ?? 'badge bg-secondary' }}">
                                                {{ $statusText[$pivot->status] ?? $pivot->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($pivot->submitted_at)
                                                {{ \Carbon\Carbon::parse($pivot->submitted_at)->format('d.m.Y H:i') }}
                                            @else
                                                <span class="text-muted">Hali topshirmadi</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pivot->file_path)
                                                <a href="{{ Storage::url($pivot->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">Fayl yo'q</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            Talabalar topilmadi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Guruh:</span>
                            <span>{{ $task->group->name ?? 'Noma\'lum' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Yaratuvchi:</span>
                            <span>{{ $task->supervisor->name ?? 'Noma\'lum' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Yaratilgan sana:</span>
                            <span>{{ $task->created_at->format('d.m.Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Muddat:</span>
                            <span class="fw-bold {{ $task->due_date->isPast() ? 'text-danger' : '' }}">
                                {{ $task->due_date->format('d.m.Y H:i') }}
                                @if($task->due_date->isPast())
                                    <span class="badge bg-danger ms-2">Muddati o'tgan</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Holati:</span>
                            @php
                                $statusClass = [
                                    'active' => 'badge bg-primary',
                                    'completed' => 'badge bg-success',
                                    'cancelled' => 'badge bg-secondary'
                                ];
                                $statusText = [
                                    'active' => 'Faol',
                                    'completed' => 'Yakunlangan',
                                    'cancelled' => 'Bekor qilingan'
                                ];
                            @endphp
                            <span class="{{ $statusClass[$task->status] ?? 'badge bg-secondary' }}">
                                {{ $statusText[$task->status] ?? $task->status }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Talabalar ({{ $task->students_count }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($task->students as $student)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ $student->full_name }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $student->group->name ?? '' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning mb-0">Talabalar topilmadi</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
