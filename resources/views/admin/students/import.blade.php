@extends('layouts.admin')

@section('title', 'Guruh qo\'shish (Import)')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Guruh qo'shish (Import)</h1>
        <p class="text-muted mb-0">Excel yoki CSV fayl orqali bir nechta talabani qo'shing</p>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle me-2"></i>
          <strong>Xatoliklar:</strong>
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <!-- Instructions -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title fw-bold mb-3">
            <i class="fa fa-info-circle text-primary me-2"></i>Ko'rsatmalar
          </h5>
          <ol class="mb-3">
            <li class="mb-2">Excel yoki CSV faylni tayyorlang</li>
            <li class="mb-2">Birinchi qator sarlavha bo'lishi kerak</li>
            <li class="mb-2">Ustunlar tartibi:
              <ul class="mt-2">
                <li><strong>F.I.Sh.</strong> (majburiy)</li>
                <li><strong>Login</strong> (majburiy, unique)</li>
                <li><strong>Parol</strong> (ixtiyoriy, bo'sh bo'lsa: student123)</li>
              </ul>
            </li>
            <li class="mb-2">Guruh nomi, fakultet, tashkilot va amaliyot sanalarini formada kiriting</li>
            <li class="mb-2">Barcha talabalar bir xil guruhga biriktiriladi</li>
          </ol>

          <div class="alert alert-info mb-0">
            <i class="fa fa-download me-2"></i>
            <strong>Namuna fayl:</strong>
            <a href="#" class="alert-link" onclick="downloadSample(); return false;">Namuna faylni yuklab olish</a>
          </div>
        </div>
      </div>

      <!-- Upload Form -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('admin.students.import.process') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
              <!-- File Upload -->
              <div class="col-12">
                <label class="form-label fw-semibold">
                  <i class="fa fa-file-excel text-success me-1"></i>Excel/CSV fayl <span class="text-danger">*</span>
                </label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="text-muted">Maksimal hajm: 2MB. Format: XLSX, XLS, CSV</small>
              </div>

              <!-- Group Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-users text-primary me-1"></i>Guruh nomi <span class="text-danger">*</span>
                </label>
                <input type="text" name="group_name" class="form-control" placeholder="IT-21" value="{{ old('group_name') }}" required>
                <small class="text-muted">Masalan: IT-21, KI-22</small>
              </div>

              <!-- Faculty -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-graduation-cap text-info me-1"></i>Fakultet <span class="text-danger">*</span>
                </label>
                <input type="text" name="faculty" class="form-control" placeholder="Informatika" value="{{ old('faculty') }}" required>
                <small class="text-muted">Masalan: Informatika, Kompyuter injiniringi</small>
              </div>

              <!-- Organization -->
              <div class="col-md-12">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building text-warning me-1"></i>Tashkilot <span class="text-danger">*</span>
                </label>
                <select name="organization_id" class="form-select" required>
                  <option value="">Tanlang...</option>
                  @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Internship Dates -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-check text-primary me-1"></i>Amaliyot boshlanishi <span class="text-danger">*</span>
                </label>
                <input type="date" name="internship_start_date" class="form-control" value="{{ old('internship_start_date') }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-xmark text-danger me-1"></i>Amaliyot tugashi <span class="text-danger">*</span>
                </label>
                <input type="date" name="internship_end_date" class="form-control" value="{{ old('internship_end_date') }}" required>
              </div>

              <!-- Preview Section -->
              <div class="col-12">
                <div class="alert alert-warning">
                  <i class="fa fa-exclamation-triangle me-2"></i>
                  <strong>Diqqat:</strong> Import jarayoni qaytarilmaydi. Faylni tekshirib ko'ring!
                </div>
              </div>

              <!-- Buttons -->
              <div class="col-12">
                <hr class="my-3">
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-success">
                    <i class="fa fa-file-import me-2"></i>Import qilish
                  </button>
                  <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="fa fa-times me-2"></i>Bekor qilish
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function downloadSample() {
  // Create CSV content
  const csvContent = "F.I.Sh.,Login,Parol,Guruh,Fakultet\n" +
                     "Aliyev Vali Akbarovich,aliyev_vali,parol123,IT-21,Informatika\n" +
                     "Karimova Nodira Shavkatovna,karimova_nodira,parol456,IT-21,Informatika\n" +
                     "Toshmatov Jasur Olimovich,toshmatov_jasur,parol789,IT-22,Informatika";
  
  // Create blob and download
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'talabalar_namuna.csv';
  link.click();
}
</script>
@endsection
