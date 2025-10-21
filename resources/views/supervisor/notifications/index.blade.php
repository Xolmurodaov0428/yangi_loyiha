@extends('layouts.supervisor')

@section('title', 'Bildirishnomalar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-bell me-2 text-primary"></i>Bildirishnomalar
            </h1>
            <p class="text-muted mb-0">Barcha bildirishnomalarni ko'rish va boshqarish</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="markAllReadBtn">
                <i class="fa fa-check-double me-1"></i>Barchasini o'qilgan deb belgilash
            </button>
            <form action="{{ route('supervisor.notifications.delete-read') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('O\'qilgan bildirishnomalarni o\'chirmoqchimisiz?')">
                    <i class="fa fa-trash me-1"></i>O'qilganlarni o'chirish
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-pills mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('supervisor.notifications.index', ['filter' => 'all']) }}">
                        <i class="fa fa-list me-1"></i>Barchasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('supervisor.notifications.index', ['filter' => 'unread']) }}">
                        <i class="fa fa-envelope me-1"></i>O'qilmaganlar
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter === 'read' ? 'active' : '' }}" href="{{ route('supervisor.notifications.index', ['filter' => 'read']) }}">
                        <i class="fa fa-envelope-open me-1"></i>O'qilganlar
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ $notification->is_read ? '' : 'bg-light' }}" 
                             data-notification-id="{{ $notification->id }}">
                            <div class="d-flex w-100 align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:48px;height:48px;background:linear-gradient(135deg,var(--bs-{{ $notification->color }}) 0%,var(--bs-{{ $notification->color }}) 100%);opacity:0.15;">
                                        <i class="fa {{ $notification->icon }} fs-5" style="color:var(--bs-{{ $notification->color }});"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold">{{ $notification->title }}</h6>
                                        <div class="d-flex gap-2">
                                            @if(!$notification->is_read)
                                                <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                                        data-id="{{ $notification->id }}"
                                                        title="O'qilgan deb belgilash">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-id="{{ $notification->id }}"
                                                    title="O'chirish">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fa fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-3">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-bell-slash fs-1 text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">
                        @if($filter === 'unread')
                            Hozircha o'qilmagan bildirishnomalar yo'q
                        @elseif($filter === 'read')
                            Hozircha o'qilgan bildirishnomalar yo'q
                        @else
                            Hozircha bildirishnomalar yo'q
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            markAsRead(id);
        });
    });

    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
        fetch('{{ route("supervisor.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Delete notification
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Bildirishnomani o\'chirmoqchimisiz?')) {
                const id = this.dataset.id;
                deleteNotification(id);
            }
        });
    });

    function markAsRead(id) {
        fetch(`/supervisor/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteNotification(id) {
        fetch(`/supervisor/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>
@endsection
