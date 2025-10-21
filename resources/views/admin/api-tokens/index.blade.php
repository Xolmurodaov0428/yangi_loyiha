@extends('layouts.admin')

@section('title', 'API Tokenlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="fa fa-key me-2 text-primary"></i>API Tokenlar
            </h1>
            <p class="text-muted mb-0">API uchun autentifikatsiya tokenlarini boshqarish</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTokenModal">
            <i class="fa fa-plus me-1"></i>Yangi Token
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('new_token'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>Yangi Token Yaratildi!</h5>
            <p class="mb-2">Bu tokenni xavfsiz joyda saqlang. Uni qayta ko'ra olmaysiz!</p>
            <div class="bg-dark text-white p-3 rounded mb-0">
                <code class="text-white">{{ session('new_token') }}</code>
                <button class="btn btn-sm btn-outline-light ms-2" onclick="copyToken('{{ session('new_token') }}')">
                    <i class="fa fa-copy"></i> Nusxa olish
                </button>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nomi</th>
                            <th>Yaratuvchi</th>
                            <th>Holati</th>
                            <th>Oxirgi ishlatilgan</th>
                            <th>Amal qilish muddati</th>
                            <th>Yaratilgan</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tokens as $token)
                            <tr>
                                <td><code>#{{ $token->id }}</code></td>
                                <td>
                                    <strong>{{ $token->name }}</strong>
                                </td>
                                <td>
                                    <i class="fa fa-user me-1"></i>{{ $token->creator->name }}
                                </td>
                                <td>
                                    @if($token->is_active)
                                        <span class="badge bg-success">Faol</span>
                                    @else
                                        <span class="badge bg-secondary">Nofaol</span>
                                    @endif
                                    @if($token->expires_at && $token->expires_at->isPast())
                                        <span class="badge bg-danger ms-1">Muddati o'tgan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($token->last_used_at)
                                        <small class="text-muted">{{ $token->last_used_at->diffForHumans() }}</small>
                                    @else
                                        <small class="text-muted fst-italic">Hech qachon</small>
                                    @endif
                                </td>
                                <td>
                                    @if($token->expires_at)
                                        <small class="text-muted">{{ $token->expires_at->format('d.m.Y H:i') }}</small>
                                    @else
                                        <small class="text-muted fst-italic">Cheksiz</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $token->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form method="POST" action="{{ route('admin.api-tokens.toggle', $token->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $token->is_active ? 'warning' : 'success' }}" title="{{ $token->is_active ? 'O\'chirish' : 'Yoqish' }}">
                                                <i class="fa fa-{{ $token->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.api-tokens.destroy', $token->id) }}" class="d-inline" onsubmit="return confirm('Tokenni o\'chirmoqchimisiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="O'chirish">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fa fa-key fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Hozircha tokenlar yo'q</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $tokens->links() }}
</div>

<!-- Create Token Modal -->
<div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.api-tokens.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Yangi API Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Token nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Masalan: Mobile App Token">
                        <small class="text-muted">Tokenni identifikatsiya qilish uchun</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amal qilish muddati</label>
                        <input type="datetime-local" name="expires_at" class="form-control">
                        <small class="text-muted">Bo'sh qoldiring agar cheksiz bo'lsin</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>Yaratish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToken(token) {
    navigator.clipboard.writeText(token).then(() => {
        alert('Token nusxalandi!');
    });
}
</script>
@endsection
