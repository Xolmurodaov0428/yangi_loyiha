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

    <div class="row g-4">
        @if($conversations->count() > 0)
            @foreach($conversations as $conversation)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('admin.messages.show', $conversation->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 conversation-card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 fw-bold text-primary">
                                            <i class="fa fa-user-graduate me-1"></i>{{ $conversation->student->full_name }}
                                        </h6>
                                        @if($conversation->unread_count > 0)
                                            <span class="badge bg-danger rounded-pill">{{ $conversation->unread_count }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa fa-layer-group me-1"></i>
                                        {{ $conversation->student->group->name ?? $conversation->student->group_name ?? 'Guruh belgilanmagan' }}
                                    </div>
                                </div>

                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="small text-muted mb-1">Rahbar:</div>
                                    <div class="fw-bold text-dark">
                                        <i class="fa fa-user-tie me-1"></i>{{ $conversation->supervisor->name }}
                                    </div>
                                </div>

                                @if($conversation->lastMessage)
                                    <p class="mb-2 small text-muted text-truncate">
                                        @if($conversation->lastMessage->sender_type === 'supervisor')
                                            <span class="badge bg-success bg-opacity-10 text-success">Rahbar:</span>
                                        @else
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Talaba:</span>
                                        @endif
                                        {{ Str::limit($conversation->lastMessage->message, 60) }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fa fa-clock me-1"></i>{{ $conversation->lastMessage->created_at->diffForHumans() }}
                                    </small>
                                @else
                                    <p class="mb-0 small text-muted fst-italic">Xabarlar yo'q</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-inbox fa-4x text-muted opacity-25 mb-3"></i>
                        <h5 class="text-muted">Suhbatlar topilmadi</h5>
                        <p class="text-muted mb-0">Hozircha hech qanday xabar almashuv yo'q</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.conversation-card {
    transition: all 0.3s ease;
}
.conversation-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}
</style>
@endsection
