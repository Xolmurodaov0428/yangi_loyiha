@extends('layouts.admin')

@section('title', 'Xabarlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-envelope me-2 text-primary"></i>Barcha xabarlar
            </h1>
            <p class="text-muted mb-0">Rahbar va talabalar o'rtasidagi barcha suhbatlar</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if($conversations->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="min-width: 200px;">Talaba</th>
                                        <th style="min-width: 120px;">Guruh</th>
                                        <th style="min-width: 180px;">Rahbar</th>
                                        <th style="min-width: 200px;">Oxirgi xabar</th>
                                        <th class="text-end pe-3" style="width: 120px;">Harakatlar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($conversations as $conversation)
                                        <tr class="conversation-row">
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fa fa-user-graduate text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="text-truncate" style="max-width: 180px;">
                                                        <h6 class="mb-0 fw-semibold text-truncate">
                                                            {{ $conversation->student->full_name ?? 'Talaba' }}
                                                            @if($conversation->unread_count > 0)
                                                                <span class="badge bg-danger rounded-pill ms-1">{{ $conversation->unread_count }}</span>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted text-truncate d-block">{{ $conversation->student->username ?? 'Foydalanuvchi' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 150px;">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fa fa-layer-group me-1"></i>
                                                        {{ $conversation->student->group->name ?? $conversation->student->group_name ?? 'Noma\'lum' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fa fa-user-tie text-success"></i>
                                                        </div>
                                                    </div>
                                                    <div class="text-truncate" style="max-width: 150px;">
                                                        <span class="fw-medium">{{ $conversation->supervisor->name ?? 'Rahbar' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($conversation->lastMessage)
                                                    <div class="d-flex flex-column">
                                                        <div class="text-truncate" style="max-width: 250px;">
                                                            @if($conversation->lastMessage->sender_type === 'App\\Models\\User')
                                                                <i class="fa fa-user-tie text-success me-1"></i>
                                                            @else
                                                                <i class="fa fa-user-graduate text-primary me-1"></i>
                                                            @endif
                                                            {{ Str::limit($conversation->lastMessage->message, 35) }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Xabarlar yo'q</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('admin.messages.show', $conversation->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye me-1"></i> Ko'rish
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fa fa-envelope-open fa-4x text-muted opacity-50"></i>
                        </div>
                        <h4 class="text-muted mb-2">Suhbatlar topilmadi</h4>
                        <p class="text-muted mb-4">Hozircha hech qanday xabar almashuv yo'q</p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4">
                            <i class="fa fa-arrow-left me-2"></i>Bosh sahifaga qaytish
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.conversation-row {
    transition: all 0.2s ease;
}

.conversation-row:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.avatar-sm {
    width: 36px;
    height: 36px;
    line-height: 36px;
    font-size: 16px;
}

.table > :not(:first-child) {
    border-top: none;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.table > thead > tr > th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: inline-block;
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.8125rem;
}
</style>
@endsection
