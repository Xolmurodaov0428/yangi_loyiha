@extends('layouts.supervisor')

@section('title', 'Kundaliklar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Talabalar Kundaliklari</h1>
        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0">
                    <i class="fa fa-book me-2 text-warning"></i>Kundalik Yozuvlar
                </h5>
                <div class="text-muted small">
                    Kutilayotgan: {{ $logbooks->where('status', 'pending')->count() }} ta
                </div>
            </div>

            @if($logbooks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Talaba</th>
                                <th>Sana</th>
                                <th>Mavzu</th>
                                <th>Holati</th>
                                <th>Harakatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logbooks as $logbook)
                                <tr>
                                    <td>{{ $logbook->student->full_name }}</td>
                                    <td>{{ $logbook->date->format('d.m.Y') }}</td>
                                    <td>{{ Str::limit($logbook->title, 30) }}</td>
                                    <td>
                                        @if($logbook->status === 'pending')
                                            <span class="badge bg-warning">Kutilmoqda</span>
                                        @elseif($logbook->status === 'approved')
                                            <span class="badge bg-success">Tasdiqlangan</span>
                                        @else
                                            <span class="badge bg-danger">Rad etilgan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#logbookModal{{ $logbook->id }}">
                                           <i class="fa fa-eye"></i>
                                        </a>
                                        <form action="{{ route('supervisor.logbooks.approve', $logbook->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('supervisor.logbooks.reject', $logbook->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal for viewing logbook details -->
                                <div class="modal fade" id="logbookModal{{ $logbook->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $logbook->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Talaba:</strong> {{ $logbook->student->full_name }}
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Sana:</strong> {{ $logbook->date->format('d.m.Y') }}
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Mazmuni:</strong>
                                                    <div class="border p-3 mt-2">{{ $logbook->content }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $logbooks->links() }}
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-book fs-1 mb-3 opacity-25"></i>
                    <p class="mb-0">Hozircha kundalik yozuvlar yo'q</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
