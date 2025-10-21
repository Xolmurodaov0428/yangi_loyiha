@extends('layouts.admin')

@section('title', 'Xabarlar tarixi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-history me-2 text-primary"></i>Xabarlar tarixi
            </h1>
            <p class="text-muted mb-0">Barcha xabarlar faoliyati va o'zgarishlar</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.message-logs.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Harakat</label>
                    <select name="action" class="form-select">
                        <option value="all" {{ request('action') == 'all' ? 'selected' : '' }}>Barchasi</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Yaratildi</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Tahrirlandi</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>O'chirildi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Foydalanuvchi turi</label>
                    <select name="user_type" class="form-select">
                        <option value="all" {{ request('user_type') == 'all' ? 'selected' : '' }}>Barchasi</option>
                        <option value="supervisor" {{ request('user_type') == 'supervisor' ? 'selected' : '' }}>Rahbar</option>
                        <option value="student" {{ request('user_type') == 'student' ? 'selected' : '' }}>Talaba</option>
                        <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Qidiruv</label>
                    <input type="text" name="search" class="form-control" placeholder="Ism yoki xabar matni..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-search me-1"></i>Qidirish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th>Foydalanuvchi</th>
                            <th>Harakat</th>
                            <th>Xabar matni</th>
                            <th>IP Manzil</th>
                            <th>Vaqt</th>
                            <th class="text-center">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary">#{{ $log->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                <i class="fa fa-user text-primary" style="font-size:0.8rem;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $log->user->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $log->user_type_label }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($log->action == 'created')
                                        <span class="badge bg-success">
                                            <i class="fa fa-plus me-1"></i>{{ $log->action_label }}
                                        </span>
                                    @elseif($log->action == 'updated')
                                        <span class="badge bg-warning">
                                            <i class="fa fa-edit me-1"></i>{{ $log->action_label }}
                                        </span>
                                    @elseif($log->action == 'deleted')
                                        <span class="badge bg-danger">
                                            <i class="fa fa-trash me-1"></i>{{ $log->action_label }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="max-width:300px;">
                                        @if($log->action == 'updated')
                                            <div class="small">
                                                <span class="text-muted">Eski:</span> {{ Str::limit($log->old_content, 50) }}<br>
                                                <span class="text-muted">Yangi:</span> {{ Str::limit($log->new_content, 50) }}
                                            </div>
                                        @else
                                            {{ Str::limit($log->new_content ?? $log->old_content, 80) }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->ip_address }}</small>
                                </td>
                                <td>
                                    <div class="small">
                                        {{ $log->created_at->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}
                                    </div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.message-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Hech qanday log topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
