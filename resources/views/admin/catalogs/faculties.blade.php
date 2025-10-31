@extends('layouts.admin')

@section('title', 'Fakultetlar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Fakultetlar ro'yxati</h1>
            <p class="text-muted mb-0">Barcha fakultetlarni ko'rish va boshqarish</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importFacultyModal">
                <i class="fa fa-file-excel me-2"></i>Excel dan yuklash
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFacultyModal">
                <i class="fa fa-plus me-2"></i>Yangi fakultet qo'shish
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
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
                            <th>Fakultet nomi</th>
                            <th>Kod</th>
                            <th>Tavsif</th>
                            <th>Guruhlar</th>
                            <th>Talabalar</th>
                            <th>Holat</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faculties as $faculty)
                            <tr>
                                <td>{{ $loop->iteration + ($faculties->currentPage() - 1) * $faculties->perPage() }}</td>
                                <td><strong>{{ $faculty->name }}</strong></td>
                                <td><code>{{ $faculty->code ?? '-' }}</code></td>
                                <td>{{ Str::limit($faculty->description ?? '-', 50) }}</td>
                                <td><span class="badge bg-primary">{{ $faculty->groups_count ?? 0 }}</span></td>
                                <td><span class="badge bg-info">{{ $faculty->students_count ?? 0 }}</span></td>
                                <td>
                                    @if ($faculty->is_active)
                                        <span class="badge bg-success">Faol</span>
                                    @else
                                        <span class="badge bg-secondary">Nofaol</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFacultyModal"
                                            data-id="{{ $faculty->id }}"
                                            data-name="{{ $faculty->name }}"
                                            data-code="{{ $faculty->code }}"
                                            data-description="{{ $faculty->description }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.catalogs.faculties.delete', $faculty->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('O\'chirishga ishonchingiz komilmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                    Hozircha fakultetlar yo'q
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $faculties->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addFacultyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.faculties.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yangi fakultet qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fakultet nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Masalan: Informatika">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kod</label>
                        <input type="text" name="code" class="form-control" placeholder="Masalan: IT">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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

<!-- Edit Modal -->
<div class="modal fade" id="editFacultyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editFacultyForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Fakultetni tahrirlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fakultet nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editFacultyName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kod</label>
                        <input type="text" name="code" id="editFacultyCode" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" id="editFacultyDescription" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="importFacultyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.faculties.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Excel dan fakultetlar yuklash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Format:</strong> Excel faylda quyidagi ustunlar bo'lishi kerak:
                        <ul class="mb-0 mt-2">
                            <li><strong>A ustun:</strong> Fakultet nomi (majburiy)</li>
                            <li><strong>B ustun:</strong> Kod (ixtiyoriy)</li>
                            <li><strong>C ustun:</strong> Tavsif (ixtiyoriy)</li>
                        </ul>
                        <small class="text-muted">Birinchi qator sarlavha sifatida o'tkazib yuboriladi.</small>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('admin.catalogs.faculties.template') }}" class="btn btn-outline-info btn-sm w-100 mb-3">
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
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editFacultyModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const code = button.getAttribute('data-code');
        const description = button.getAttribute('data-description');
        
        document.getElementById('editFacultyName').value = name;
        document.getElementById('editFacultyCode').value = code || '';
        document.getElementById('editFacultyDescription').value = description || '';
        document.getElementById('editFacultyForm').action = `{{ url('admin/catalogs/faculties') }}/${id}`;
    });
});
</script>
@endsection
