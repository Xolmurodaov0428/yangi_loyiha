@extends('layouts.admin')

@section('title', 'Foydalanuvchini tahrirlash')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Foydalanuvchini tahrirlash</h1>
        <p class="text-muted mb-0">{{ $user->name }}</p>
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

      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <!-- Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-user text-primary me-1"></i>To'liq ism <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
              </div>

              <!-- Username -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-at text-primary me-1"></i>Login (ixtiyoriy)
                </label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control">
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-envelope text-primary me-1"></i>Email <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
              </div>

              <!-- Password -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-lock text-primary me-1"></i>Yangi parol (ixtiyoriy)
                </label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Bo'sh qoldiring, agar o'zgartirmoqchi bo'lmasangiz</small>
              </div>

              <!-- Role -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-user-tag text-primary me-1"></i>Rol <span class="text-danger">*</span>
                </label>
                <select name="role" id="roleSelect" class="form-select" required>
                  <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Rahbar</option>
                  <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Talaba</option>
                </select>
              </div>

              <!-- Organization -->
              <div class="col-md-6" id="organizationSection" style="display: none;">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building text-warning me-1"></i>Tashkilot
                </label>
                <select name="organization_id" id="organizationSelect" class="form-select">
                  <option value="">Tanlang...</option>
                  @php
                    $organizations = \App\Models\Organization::where('is_active', true)->get();
                  @endphp
                  @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id', $user->organization_id) == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Groups (for supervisors) -->
              <div class="col-md-12" id="groupsSection" style="display: {{ old('role', $user->role) == 'supervisor' ? 'block' : 'none' }};">
                <label class="form-label fw-semibold mb-3">
                  <i class="fa fa-users text-primary me-1"></i>Guruhlar (Rahbar uchun)
                </label>
                  <div class="card-body">
                    @php
                      $groups = \App\Models\Group::withCount('supervisors')->where('is_active', true)->get();
                      $userGroupIds = $user->groups->pluck('id')->toArray();
                    @endphp
                    @if($groups->count() > 0)
                      <div class="row g-3">
                        @foreach($groups as $group)
                          <div class="col-md-6">
                            <div class="card border {{ $group->supervisors_count > 0 ? 'border-success' : 'border-secondary' }} mb-2">
                              <div class="card-body p-2">
                                <div class="form-check form-switch">
                                  <input class="form-check-input" type="checkbox" name="groups[]" value="{{ $group->id }}" id="group_{{ $group->id }}" {{ in_array($group->id, $userGroupIds) ? 'checked' : '' }}>
                                  <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="group_{{ $group->id }}">
                                    <div>
                                      <strong>{{ $group->name }}</strong> - {{ $group->faculty }}
                                      <br>
                                      <small class="text-muted">
                                        <i class="fa fa-user me-1"></i>{{ $group->students_count ?? 0 }} talaba
                                      </small>
                                    </div>
                                    <div>
                                      @if($group->supervisors_count > 0)
                                        <span class="badge bg-success">
                                          <i class="fa fa-user-tie me-1"></i>{{ $group->supervisors_count }} rahbar
                                        </span>
                                      @else
                                        <span class="badge bg-secondary">
                                          <i class="fa fa-user-slash me-1"></i>Rahbar yo'q
                                        </span>
                                      @endif
                                    </div>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                      <div class="alert alert-info mt-3 mb-0">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Eslatma:</strong>
                        <ul class="mb-0 mt-2 small">
                          <li><span class="badge bg-success">Yashil</span> - Guruhda rahbar mavjud</li>
                          <li><span class="badge bg-secondary">Kulrang</span> - Guruhda rahbar yo'q</li>
                        </ul>
                      </div>
                    @else
                      <div class="text-center text-muted py-3">
                        <i class="fa fa-inbox fs-4 mb-2 opacity-25"></i>
                        <p class="mb-0">Guruhlar topilmadi</p>
                      </div>
                    @endif
                  </div>
                </div>
                <small class="text-muted mt-2 d-block">Bir nechta guruh tanlashingiz mumkin</small>
              </div>

              <!-- Status Checkboxes -->
              <div class="col-md-6">
                <label class="form-label fw-semibold d-block mb-3">
                  <i class="fa fa-toggle-on text-primary me-1"></i>Holatlar
                </label>
                <div class="form-check form-switch mb-2">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    <i class="fa fa-check-circle text-success me-1"></i>Faol
                  </label>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="approved" id="approved" value="1" {{ old('approved', $user->approved) ? 'checked' : '' }}>
                  <label class="form-check-label" for="approved">
                    <i class="fa fa-check-double text-info me-1"></i>Tasdiqlangan
                  </label>
                </div>
              </div>

              <!-- Buttons -->
              <div class="col-12">
                <hr class="my-3">
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-2"></i>Saqlash
                  </button>
                  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
// Show/hide sections based on role
const roleSelect = document.getElementById('roleSelect');
const groupsSection = document.getElementById('groupsSection');
const organizationSection = document.getElementById('organizationSection');
const organizationSelect = document.getElementById('organizationSelect');

roleSelect.addEventListener('change', function() {
  const role = this.value;
  
  // Show groups section only for supervisor
  if (role === 'supervisor') {
    groupsSection.style.display = 'block';
  } else {
    groupsSection.style.display = 'none';
  }
  
  // Hide organization section for all roles
  organizationSection.style.display = 'none';
  organizationSelect.required = false;
});
</script>
@endsection
