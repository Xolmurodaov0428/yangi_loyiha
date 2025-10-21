@extends('layouts.supervisor')

@section('title', 'Muloqot - ' . $student->full_name)

@section('content')
<div class="container-fluid" style="width: 1000px">
    <div class="row g-0" style="height: calc(100vh - 120px);">
        <!-- Conversations Sidebar -->
        <div class="col-md-4 col-lg-3 border-end bg-white">
            <div class="p-3 border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fa fa-comments me-2 text-primary"></i>Muloqotlar
                </h5>
            </div>
            <div class="overflow-auto" style="height: calc(100vh - 200px);">
                @foreach($conversations as $conv)
                    <a href="{{ route('supervisor.messages.show', $conv->student_id) }}" 
                       class="d-block text-decoration-none conversation-item {{ $conv->student_id == $student->id ? 'active' : '' }}">
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:40px;height:40px;background:#e3f2fd;">
                                        <i class="fa fa-user text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-0 small fw-bold text-dark">{{ $conv->student->full_name }}</h6>
                                        @if($conv->unread_count > 0)
                                            <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;">{{ $conv->unread_count }}</span>
                                        @endif
                                    </div>
                                    @if($conv->lastMessage)
                                        <p class="mb-0 small text-muted text-truncate" style="max-width:200px;">
                                            {{ Str::limit($conv->lastMessage->message, 30) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9 d-flex flex-column bg-light">
            <!-- Chat Header -->
            <div class="bg-white border-bottom p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width:48px;height:48px;background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);">
                            <i class="fa fa-user text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-bold">{{ $student->full_name }}</h5>
                        <small class="text-muted">{{ $student->group_name ?? 'Guruh belgilanmagan' }}</small>
                    </div>
                    <a href="{{ route('supervisor.messages.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-grow-1 overflow-auto p-3" id="messagesContainer" style="height: 0;">
                <div id="messagesList">
                    @foreach($messages as $message)
                        <div class="message-item mb-3 {{ $message->sender_type === 'supervisor' ? 'text-end' : 'text-start' }}" id="message-{{ $message->id }}">
                            <div class="d-inline-block" style="max-width: 70%;">
                                <div class="p-3 rounded-3 {{ $message->sender_type === 'supervisor' ? 'bg-primary text-white' : 'bg-white' }}" 
                                     style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <p class="mb-1 flex-grow-1">{{ $message->message }}</p>
                                        @if($message->sender_type === 'supervisor')
                                            <button class="btn btn-sm btn-link text-white p-0 ms-2 delete-message-btn" 
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
                                    {{ $message->created_at->timezone('Asia/Tashkent')->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Message Input -->
            <div class="bg-white border-top p-3">
                <form id="messageForm" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" id="attachmentInput" class="d-none" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                        <button type="button" class="btn btn-outline-secondary" id="attachmentBtn" title="Fayl biriktirish">
                            <i class="fa fa-paperclip"></i>
                        </button>
                        <input type="text" class="form-control" id="messageInput" name="message" 
                               placeholder="Xabar yozing..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fa fa-paper-plane me-1"></i>Yuborish
                        </button>
                    </div>
                    <div id="attachmentPreview" class="mt-2 d-none">
                        <small class="text-muted">
                            <i class="fa fa-paperclip me-1"></i>
                            <span id="attachmentName"></span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" id="removeAttachment">
                                <i class="fa fa-times"></i>
                            </button>
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-item {
    transition: background 0.2s ease;
}
.conversation-item:hover {
    background: #f8f9fa;
}
.conversation-item.active {
    background: #e3f2fd;
    border-left: 3px solid #3b82f6;
}
#messagesContainer {
    background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
}
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
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const messagesList = document.getElementById('messagesList');
    const attachmentBtn = document.getElementById('attachmentBtn');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const removeAttachment = document.getElementById('removeAttachment');
    const studentId = {{ $student->id }};

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    scrollToBottom();

    // Attachment handling
    attachmentBtn.addEventListener('click', () => attachmentInput.click());
    
    attachmentInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            attachmentName.textContent = this.files[0].name;
            attachmentPreview.classList.remove('d-none');
        }
    });

    removeAttachment.addEventListener('click', function() {
        attachmentInput.value = '';
        attachmentPreview.classList.add('d-none');
    });

    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const sendBtn = document.getElementById('sendBtn');
        
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Yuborilmoqda...';

        fetch(`{{ route('supervisor.messages.send', $student->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Server xatosi');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data); // Debug
            
            if (data.success) {
                // Add message to list
                const messageHtml = `
                    <div class="message-item mb-3 text-end" id="message-${data.data.id}">
                        <div class="d-inline-block" style="max-width: 70%;">
                            <div class="p-3 rounded-3 bg-primary text-white" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div class="d-flex justify-content-between align-items-start">
                                    <p class="mb-1 flex-grow-1">${data.data.message}</p>
                                    <button class="btn btn-sm btn-link text-white p-0 ms-2 delete-message-btn" 
                                            data-message-id="${data.data.id}" 
                                            title="O'chirish"
                                            style="opacity: 0.7;">
                                        <i class="fa fa-trash" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                                ${data.data.has_attachment ? `
                                    <div class="mt-2 pt-2 border-top border-light">
                                        <span class="text-white">
                                            <i class="fa fa-paperclip me-1"></i>${data.data.attachment_name}
                                        </span>
                                    </div>
                                ` : ''}
                            </div>
                            <small class="d-block mt-1 text-muted">${data.data.created_at}</small>
                        </div>
                    </div>
                `;
                messagesList.insertAdjacentHTML('beforeend', messageHtml);
                
                // Clear form
                messageInput.value = '';
                attachmentInput.value = '';
                attachmentPreview.classList.add('d-none');
                
                // Scroll to bottom
                scrollToBottom();
            } else {
                alert('Xatolik: ' + (data.message || 'Noma\'lum xatolik'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Xabar yuborishda xatolik: ' + error.message);
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Yuborish';
        });
    });

    // Delete message
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-message-btn')) {
            const btn = e.target.closest('.delete-message-btn');
            const messageId = btn.dataset.messageId;
            
            if (confirm('Bu xabarni o\'chirmoqchimisiz?')) {
                fetch(`{{ url('supervisor/messages') }}/${messageId}`, {
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

    // Auto-refresh messages every 10 seconds
    setInterval(function() {
        fetch(`{{ route('supervisor.messages.get', $student->id) }}`, {
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
    }, 10000);
});
</script>
@endsection
