@extends('layouts.admin')

@section('title', 'Suhbat - ' . $conversation->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i>Orqaga
        </a>
    </div>

    <div class="row g-0" style="height: calc(100vh - 180px);">
        <!-- Chat Area -->
        <div class="col-12 d-flex flex-column bg-light rounded shadow-sm">
            <!-- Chat Header -->
            <div class="bg-white border-bottom p-3 rounded-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:48px;height:48px;background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);">
                                    <i class="fa fa-user-graduate text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $conversation->student->full_name }}</h5>
                                <small class="text-muted">{{ $conversation->student->group_name ?? 'Guruh belgilanmagan' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:48px;height:48px;background:linear-gradient(135deg,#059669 0%,#047857 100%);">
                                    <i class="fa fa-user-tie text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $conversation->supervisor->name }}</h6>
                                <small class="text-muted">Rahbar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-grow-1 overflow-auto p-4" id="messagesContainer" style="height: 0; background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);">
                <div id="messagesList">
                    @forelse($messages as $message)
                        <div class="message-item mb-3 {{ $message->sender_type === 'supervisor' ? 'text-end' : 'text-start' }} {{ $message->is_deleted ? 'opacity-75' : '' }}" id="message-{{ $message->id }}">
                            <div class="d-inline-block" style="max-width: 70%;">
                                <div class="small text-muted mb-1">
                                    @if($message->sender_type === 'supervisor')
                                        <i class="fa fa-user-tie me-1"></i>{{ $conversation->supervisor->name }}
                                    @else
                                        <i class="fa fa-user-graduate me-1"></i>{{ $conversation->student->full_name }}
                                    @endif
                                    @if($message->is_deleted)
                                        <span class="badge bg-danger ms-2">O'chirildi</span>
                                    @endif
                                    @if($message->is_edited)
                                        <span class="badge bg-warning ms-2">Tahrirlandi</span>
                                    @endif
                                </div>
                                <div class="p-3 rounded-3 {{ $message->is_deleted ? 'bg-secondary text-white' : ($message->sender_type === 'supervisor' ? 'bg-success text-white' : 'bg-white') }}" 
                                     style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); {{ $message->is_deleted ? 'text-decoration: line-through;' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <p class="mb-1 flex-grow-1">{{ $message->message }}</p>
                                        @if(!$message->is_deleted)
                                            <button class="btn btn-sm btn-link p-0 ms-2 delete-message-btn {{ $message->sender_type === 'supervisor' ? 'text-white' : 'text-danger' }}" 
                                                    data-message-id="{{ $message->id }}" 
                                                    title="O'chirish"
                                                    style="opacity: 0.7;">
                                                <i class="fa fa-trash" style="font-size: 0.8rem;"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($message->hasAttachment())
                                        <div class="mt-2 pt-2 border-top {{ $message->sender_type === 'supervisor' ? 'border-light' : '' }}">
                                            <a href="{{ asset('storage/' . $message->attachment_path) }}" 
                                               class="{{ $message->sender_type === 'supervisor' ? 'text-white' : 'text-primary' }}" 
                                               target="_blank" download>
                                                <i class="fa fa-paperclip me-1"></i>{{ $message->attachment_name }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <small class="d-block mt-1 text-muted">
                                    {{ $message->created_at->timezone('Asia/Tashkent')->format('d.m.Y H:i') }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Xabarlar yo'q</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Info Footer -->
            <div class="bg-white border-top p-3 rounded-bottom">
                <div class="text-center text-muted small">
                    <i class="fa fa-info-circle me-1"></i>
                    Admin sifatida siz faqat xabarlarni ko'rishingiz va o'chirishingiz mumkin
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-item {
    animation: fadeIn 0.3s ease;
    transition: opacity 0.3s ease, transform 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.delete-message-btn:hover {
    opacity: 1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messagesList = document.getElementById('messagesList');

    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Delete message
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-message-btn')) {
            const btn = e.target.closest('.delete-message-btn');
            const messageId = btn.dataset.messageId;
            
            if (confirm('Bu xabarni o\'chirmoqchimisiz?')) {
                fetch(`{{ url('admin/messages') }}/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove message from DOM
                        const messageEl = document.getElementById(`message-${messageId}`);
                        if (messageEl) {
                            messageEl.style.opacity = '0';
                            messageEl.style.transform = 'translateX(20px)';
                            setTimeout(() => messageEl.remove(), 300);
                        }
                    } else {
                        alert('Xatolik: ' + (data.message || 'Xabarni o\'chirib bo\'lmadi'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Xabarni o\'chirishda xatolik yuz berdi');
                });
            }
        }
    });

    // Auto-refresh messages every 30 seconds
    setInterval(function() {
        fetch(`{{ route('admin.messages.get', $conversation->id) }}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    // Check if there are new messages
                    const currentCount = messagesList.children.length;
                    if (data.messages.length > currentCount) {
                        // Reload page to show new messages
                        location.reload();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
});
</script>
@endsection
