@extends('layouts.admin')

@section('title', 'Log tafsilotlari')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.message-logs.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="fa fa-arrow-left me-1"></i>Orqaga
        </a>
        <h1 class="h3 mb-1 fw-bold">
            <i class="fa fa-info-circle me-2 text-primary"></i>Log tafsilotlari #{{ $log->id }}
        </h1>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">Asosiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width:200px;">Harakat:</th>
                            <td>
                                @if($log->action == 'created')
                                    <span class="badge bg-success fs-6">
                                        <i class="fa fa-plus me-1"></i>{{ $log->action_label }}
                                    </span>
                                @elseif($log->action == 'updated')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fa fa-edit me-1"></i>{{ $log->action_label }}
                                    </span>
                                @elseif($log->action == 'deleted')
                                    <span class="badge bg-danger fs-6">
                                        <i class="fa fa-trash me-1"></i>{{ $log->action_label }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Foydalanuvchi:</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;">
                                        <i class="fa fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $log->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $log->user_type_label }}</small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Vaqt:</th>
                            <td>
                                {{ $log->created_at->timezone('Asia/Tashkent')->format('d.m.Y H:i:s') }}
                                <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                            </td>
                        </tr>
                        <tr>
                            <th>IP Manzil:</th>
                            <td><code>{{ $log->ip_address }}</code></td>
                        </tr>
                        <tr>
                            <th>User Agent:</th>
                            <td><small class="text-muted">{{ $log->user_agent }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Message Content -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">Xabar matni</h5>
                </div>
                <div class="card-body">
                    @if($log->action == 'updated')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Eski matn:</label>
                            <div class="p-3 bg-light rounded border">
                                {{ $log->old_content }}
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold text-success">Yangi matn:</label>
                            <div class="p-3 bg-light rounded border">
                                {{ $log->new_content }}
                            </div>
                        </div>
                    @elseif($log->action == 'deleted')
                        <div>
                            <label class="form-label fw-bold text-danger">O'chirilgan matn:</label>
                            <div class="p-3 bg-light rounded border">
                                {{ $log->old_content }}
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="form-label fw-bold text-success">Yaratilgan matn:</label>
                            <div class="p-3 bg-light rounded border">
                                {{ $log->new_content }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            @if($log->message)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold">Suhbat ma'lumotlari</h6>
                    </div>
                    <div class="card-body">
                        @if($log->message->conversation)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Talaba:</small>
                                <div class="fw-bold">{{ $log->message->conversation->student->full_name ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Rahbar:</small>
                                <div class="fw-bold">{{ $log->message->conversation->supervisor->name ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Suhbat ID:</small>
                                <code>#{{ $log->message->conversation->id }}</code>
                            </div>
                        @else
                            <p class="text-muted mb-0">Suhbat topilmadi</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">Qo'shimcha</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Xabar ID:</small>
                        <code>{{ $log->message_id ?? 'N/A' }}</code>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Foydalanuvchi ID:</small>
                        <code>{{ $log->user_id }}</code>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Log ID:</small>
                        <code>{{ $log->id }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
