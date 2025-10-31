@extends('layouts.admin')

@section('title', 'Guruhlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Guruhlar ro'yxati</h1>
            <p class="text-muted mb-0">Barcha guruhlarni ko'rish va boshqarish</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importGroupModal">
                <i class="fa fa-file-excel me-2"></i>Excel dan yuklash
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                <i class="fa fa-plus me-2"></i>Yangi guruh qo'shish
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Guruh nomi</th>
                            <th>Fakultet</th>
                            <th>Talabalar soni</th>
                            <th>Holat</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $group)
                            <tr>
                                <td>{{ $loop->iteration + ($groups->currentPage() - 1) * $groups->perPage() }}</td>
                                <td><strong>{{ $group->name }}</strong></td>
                                <td>{{ $group->faculty ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $group->students_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if ($group->is_active)
                                        <span class="badge bg-success">Faol</span>
                                    @else
                                        <span class="badge bg-secondary">Nofaol</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-{{ $group->is_active ? 'warning' : 'success' }}" 
                                            onclick="toggleGroup({{ $group->id }}, '{{ $group->name }}', {{ $group->is_active ? 'true' : 'false' }})"
                                            title="{{ $group->is_active ? 'Nofaol qilish' : 'Faollashtirish' }}">
                                        <i class="fa fa-{{ $group->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editGroup({{ $group->id }}, '{{ $group->name }}', '{{ $group->faculty }}', {{ $group->is_active ? 'true' : 'false' }}, {{ $group->daily_sessions ?? 3 }})"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGroupModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteGroup({{ $group->id }}, '{{ $group->name }}')"
                                            title="O'chirish">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                    Hozircha guruhlar yo'q
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $groups->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.groups.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yangi guruh qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Guruh nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Masalan: 221-20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultet</label>
                        <input type="text" name="faculty" class="form-control" placeholder="Masalan: Informatika">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-calendar-check me-1"></i>Kuniga necha marta davomat olinadi <span class="text-danger">*</span>
                        </label>
                        <select name="daily_sessions" class="form-select" required>
                            <option value="1">1 marta</option>
                            <option value="2">2 marta</option>
                            <option value="3" selected>3 marta (tavsiya etiladi)</option>
                            <option value="4">4 marta</option>
                        </select>
                        <small class="text-muted">Bu guruhda kuniga necha marta davomat olinishini belgilaydi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editGroupForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Guruhni tahrirlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Xatolik:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Guruh nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editGroupName" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultet</label>
                        <input type="text" name="faculty" id="editGroupFaculty" class="form-control @error('faculty') is-invalid @enderror">
                        @error('faculty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-calendar-check me-1"></i>Kuniga necha marta davomat olinadi <span class="text-danger">*</span>
                        </label>
                        <select name="daily_sessions" id="editGroupDailySessions" class="form-select" required>
                            <option value="1">1 marta</option>
                            <option value="2">2 marta</option>
                            <option value="3">3 marta (tavsiya etiladi)</option>
                            <option value="4">4 marta</option>
                        </select>
                        <small class="text-muted">Bu guruhda kuniga necha marta davomat olinishini belgilaydi</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editGroupIsActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="editGroupIsActive">
                                <span id="editGroupStatusLabel">Faol</span>
                            </label>
                        </div>
                        <small class="text-muted">Nofaol guruhlar ro'yxatda ko'rsatiladi, lekin foydalanilmaydi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">O'zgarishlarni saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Excel Modal -->
<div class="modal fade" id="importGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.groups.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Excel dan guruhlar yuklash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Format:</strong> Excel faylda quyidagi ustunlar bo'lishi kerak:
                        <ul class="mb-0 mt-2">
                            <li><strong>A ustun:</strong> Guruh nomi (majburiy)</li>
                            <li><strong>B ustun:</strong> Fakultet (ixtiyoriy)</li>
                        </ul>
                        <small class="text-muted">Birinchi qator sarlavha sifatida o'tkazib yuboriladi.</small>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('admin.catalogs.groups.template') }}" class="btn btn-outline-info btn-sm w-100 mb-3">
                            <i class="fa fa-download me-2"></i>Namuna faylni yuklash
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Excel fayl <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Yuklash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editGroup(id, name, faculty, isActive, dailySessions) {
    document.getElementById('editGroupName').value = name;
    document.getElementById('editGroupFaculty').value = faculty || '';
    
    // Set daily sessions
    document.getElementById('editGroupDailySessions').value = dailySessions || 3;
    
    // Set active status checkbox
    const checkbox = document.getElementById('editGroupIsActive');
    const label = document.getElementById('editGroupStatusLabel');
    
    if (isActive) {
        checkbox.checked = true;
        label.textContent = 'Faol';
        label.className = 'text-success fw-bold';
    } else {
        checkbox.checked = false;
        label.textContent = 'Nofaol';
        label.className = 'text-secondary';
    }
    
    document.getElementById('editGroupForm').action = `{{ url('admin/catalogs/groups') }}/${id}`;
}

// Update label when checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('editGroupIsActive');
    const label = document.getElementById('editGroupStatusLabel');
    
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                label.textContent = 'Faol';
                label.className = 'text-success fw-bold';
            } else {
                label.textContent = 'Nofaol';
                label.className = 'text-secondary';
            }
        });
    }
});

function toggleGroup(id, name, isActive) {
    const action = isActive ? 'nofaol qilish' : 'faollashtirish';
    if (confirm(`"${name}" guruhini ${action}ga ishonchingiz komilmi?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('admin/catalogs/groups') }}/${id}/toggle`;
        form.innerHTML = `
            @csrf
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteGroup(id, name) {
    if (confirm(`"${name}" guruhini o'chirishga ishonchingiz komilmi?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('admin/catalogs/groups') }}/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-open edit modal if there are validation errors for update
@if ($errors->any() && old('_method') === 'PUT')
    document.addEventListener('DOMContentLoaded', function() {
        // Populate the form with old values
        document.getElementById('editGroupName').value = "{{ old('name', '') }}";
        document.getElementById('editGroupFaculty').value = "{{ old('faculty', '') }}";
        
        // Get the group ID from the URL (last segment)
        const pathSegments = window.location.pathname.split('/');
        const lastAction = "{{ session('last_group_action', '') }}";
        
        // Try to get group ID from session or URL
        @if(session('editing_group_id'))
            const groupId = {{ session('editing_group_id') }};
            document.getElementById('editGroupForm').action = `{{ url('admin/catalogs/groups') }}/${groupId}`;
        @endif
        
        // Open the modal
        const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
        modal.show();
    });
@endif
</script>
@endsection
