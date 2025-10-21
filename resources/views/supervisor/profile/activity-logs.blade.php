@extends('layouts.supervisor')

@section('title', 'Faoliyat tarixi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-history me-2 text-info"></i>Faoliyat tarixi
            </h1>
            <p class="text-muted mb-0">Tizimda amalga oshirilgan harakatlar tarixi</p>
        </div>
        <a href="{{ route('supervisor.profile.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i>Profilga qaytish
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(isset($logs) && $logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sana va vaqt</th>
                                <th>Harakat</th>
                                <th>Tavsif</th>
                                <th>IP manzil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fa fa-clock me-1"></i>{{ $log->created_at->format('d.m.Y H:i:s') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->action === 'login' ? 'success' : ($log->action === 'logout' ? 'secondary' : 'primary') }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-history fs-1 text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">Hozircha faoliyat tarixi mavjud emas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
