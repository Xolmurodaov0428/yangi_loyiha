@extends('layouts.supervisor')

@section('title', 'Muloqot')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-comments me-2 text-primary"></i>Muloqot
            </h1>
            <p class="text-muted mb-0">Talabalar bilan xabar almashuv</p>
        </div>
    </div>

    <div class="row g-4" style="width: 1000px">
        @if($conversations->count() > 0)
            @foreach($conversations as $conversation)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('supervisor.messages.show', $conversation->student_id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 conversation-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width:56px;height:56px;background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);">
                                            <i class="fa fa-user text-white fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h5 class="mb-0 fw-bold text-dark">{{ $conversation->student->full_name }}</h5>
                                            @if($conversation->unread_count > 0)
                                                <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                                            @endif
                                        </div>
                                        <div class="mb-2 small text-muted">
                                            <i class="fa fa-layer-group me-1"></i>
                                            {{ $conversation->student->group->name ?? $conversation->student->group_name ?? 'Guruh belgilanmagan' }}
                                        </div>
                                        @if($conversation->lastMessage)
                                            <p class="mb-1 small text-muted text-truncate">
                                                @if($conversation->lastMessage->sender_type === 'supervisor')
                                                    <strong>Siz:</strong>
                                                @endif
                                                {{ Str::limit($conversation->lastMessage->message, 50) }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fa fa-clock me-1"></i>{{ $conversation->lastMessage->created_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <p class="mb-0 small text-muted">Hali xabar yo'q</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-comments fs-1 text-muted mb-3 opacity-25"></i>
                        <p class="text-muted mb-0">Hozircha muloqotlar yo'q</p>
                        <small class="text-muted">Talabalar bilan xabar almashuv boshlash uchun talabalar ro'yxatiga o'ting</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.conversation-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.conversation-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
