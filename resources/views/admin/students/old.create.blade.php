@extends('layouts.admin')

@section('title', 'Yangi talaba')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Yangi talaba qo'shish</h1>
        <p class="text-muted mb-0">Barcha maydonlarni to'ldiring</p>
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
          <form method="POST" action="{{ route('admin.students.store') }}" id="studentForm">
            @csrf

            <div class="row g-3">
              <!-- Full Name -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-user text-primary me-1"></i>F.I.Sh. <span class="text-danger">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control" required autofocus>
              </div>

              <!-- Username -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-at text-primary me-1"></i>Login <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
                <small class="text-muted">Kirish uchun foydalaniladi</small>
              </div>

              <!-- Password -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-lock text-primary me-1"></i>Parol <span class="text-danger">*</span>
                </label>
                <input type="password" name="password" class="form-control" required>
                <small class="text-muted">Kamida 6 ta belgi</small>
              </div>

              <!-- Group Selection -->
              <!-- <div class="col-md-12">
                <label class="form-label fw-semibold">
                  <i class="fa fa-layer-group text-primary me-1"></i>Guruhni tanlang <span class="text-danger">*</span>
                </label>
                <select id="group_select" class="form-select" required>
                  <option value="">Guruhni tanlang...</option>
                  @foreach($groups as $group)
                    <option value="{{ $group->name }}" data-faculty="{{ $group->faculty }}">
                      {{ $group->name }} - {{ $group->faculty }}
                    </option>
                  @endforeach
                </select>
              </div> -->
<select id="group_select" class="form-select" required>
  <option value="">Guruhni tanlang...</option>
  @foreach($groups as $group)
    <option value="{{ $group->name }}" data-faculty="{{ $group->faculty }}">
      {{ $group->name }} - {{ $group->faculty }}
    </option>
  @endforeach 
</select>
<!-- jQuery va Select2 kutubxonalarini ulanganligiga ishonch hosil qiling -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#group_select').select2({
      placeholder: 'Guruhni tanlang...',
      tags: true, // 🔹 yangi qiymat yozish imkonini beradi
      allowClear: true
    });
  });
</script>


              <!-- Group Name (Auto-filled) -->
              <!-- <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-layer-group text-primary me-1"></i>Guruh nomi <span class="text-danger">*</span>
                </label>
                <input type="text" id="group_name_input" name="group_name" value="{{ old('group_name') }}" class="form-control"   style="background-color: #e9ecef;">
              </div> -->

              <!-- Faculty (Auto-filled)
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building-columns text-primary me-1"></i>Fakultet <span class="text-danger">*</span>
                </label>
                <input type="text" id="faculty_input" name="faculty" value="{{ old('faculty') }}" class="form-control"   style="background-color: #e9ecef;">
              </div> -->

<div class="col-md-6">
  <label class="form-label fw-semibold">
    <i class="fa fa-building-columns text-primary me-1"></i>Fakultet <span class="text-danger">*</span>
  </label>
  <select id="faculty_input" name="faculty" class="form-select" required>
    <option value="">Fakultetni tanlang...</option>
    @foreach($groups as $faculty)
      <option value="{{ $faculty->faculty }}">{{ $faculty->faculty }}</option>
    @endforeach
  </select>
</div>

<!-- Kutubxonalar -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#faculty_input').select2({
      placeholder: 'Fakultetni tanlang yoki yozing...',
      tags: true, // ✳️ yangi fakultetni qo‘lda yozish mumkin
      allowClear: true
    });
  });
</script>

              <!-- Organization -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-building text-primary me-1"></i>Biriktirilgan tashkilot
                </label>
                <select name="organization_id" class="form-select">
                  <option value="">Tanlang...</option>
                  @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                      {{ $org->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Internship Start Date -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-check text-primary me-1"></i>Amaliyot boshlanishi
                </label>
                <input type="date" id="internship_start_date" name="internship_start_date" value="{{ old('internship_start_date') }}" class="form-control">
              </div>

              <!-- Internship End Date -->
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fa fa-calendar-xmark text-primary me-1"></i>Amaliyot tugashi
                </label>
                <input type="date" id="internship_end_date" name="internship_end_date" value="{{ old('internship_end_date') }}" class="form-control">
                <small class="text-muted">Boshlanish sanasidan keyin bo'lishi kerak</small>
              </div>

              <!-- Active Status -->
              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold" for="is_active">
                    <i class="fa fa-check-circle text-success me-1"></i>Faol
                  </label>
                </div>
              </div>

              <!-- Info Box -->
              <div class="col-12">
                <div class="alert alert-info d-flex align-items-start">
                  <i class="fa fa-info-circle fs-5 me-2 mt-1"></i>
                  <div>
                    <strong>Eslatma:</strong>
                    <ul class="mb-0 mt-1">
                      <li>Login noyob bo'lishi kerak</li>
                      <li>Amaliyot tugash sanasi boshlanish sanasidan keyin bo'lishi kerak</li>
                      <li>Tashkilot keyinchalik ham biriktirilishi mumkin</li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Buttons -->
              <div class="col-12">
                <hr class="my-3">
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-2"></i>Saqlash
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
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    // Initialize Select2 for group select
    $('#group_select').select2({
      placeholder: 'Guruhni tanlang...',
      allowClear: true
    });

    // Handle group selection
    $('#group_select').on('change', function() {
      const selectedOption = $(this).find('option:selected');
      const groupName = selectedOption.val();
      const faculty = selectedOption.data('faculty');
      
      // Update hidden inputs
      $('#group_name_input').val(groupName);
      $('#faculty_input').val(faculty);
      
      // Update faculty display
      $('#faculty_display').val(faculty);
    });

    // Initialize with old values if any
    @if(old('group_name'))
      const selectedGroup = $('#group_select option').filter(function() {
        return $(this).val() === '{{ old('group_name') }}';
      }).first();
      
      if (selectedGroup.length) {
        $('#group_select').val('{{ old('group_name') }}').trigger('change');
        $('#faculty_display').val('{{ old('faculty') }}');
      }
    @endif

    const form = document.getElementById('studentForm');
    const groupSelect = document.getElementById('group_select');
    const groupNameInput = document.getElementById('group_name_input');
    const facultyInput = document.getElementById('faculty_input');
    const startDateInput = document.getElementById('internship_start_date');
    const endDateInput = document.getElementById('internship_end_date');
    
    console.log('Elementlar:', {
      form: form ? 'topildi' : 'topilmadi',
      groupSelect: groupSelect ? 'topildi' : 'topilmadi',
      groupNameInput: groupNameInput ? 'topildi' : 'topilmadi',
      facultyInput: facultyInput ? 'topildi' : 'topilmadi'
    });
    
    if (!groupSelect || !groupNameInput || !facultyInput) {
      console.error('Kerakli elementlar topilmadi!');
      return;
    }
    
    // Function to update inputs when group is selected
    const updateInputs = function() {
      console.log('updateInputs chaqirildi');
      const selectedOption = groupSelect.options[groupSelect.selectedIndex];
      console.log('Tanlangan option:', selectedOption);

      if (selectedOption && selectedOption.value && selectedOption.value !== '') {
        const groupName = selectedOption.value;
        const faculty = selectedOption.getAttribute('data-faculty') || '';

        console.log('Yangilanmoqda:', { groupName, faculty });

        groupNameInput.value = groupName;
        facultyInput.value = faculty;

        console.log('Yangilandi:', {
          groupNameValue: groupNameInput.value,
          facultyValue: facultyInput.value
        });

        // Remove validation errors
        groupNameInput.setCustomValidity('');
        facultyInput.setCustomValidity('');
      } else {
        console.log('Guruh tanlanmagan, tozalanmoqda');
        groupNameInput.value = '';
        facultyInput.value = '';

        // Add validation errors
        groupNameInput.setCustomValidity('Guruhni tanlang');
        facultyInput.setCustomValidity('Fakultet majburiy');
      }
    };

    // Initialize on page load
    console.log('Dastlabki yangilanish...');

    // Check if there are old values
    const oldGroupName = '{{ old('group_name') }}';
    const oldFaculty = '{{ old('faculty') }}';

    if (oldGroupName && oldFaculty) {
      console.log('Old values found:', { oldGroupName, oldFaculty });

      // Find and select the matching option
      for (let i = 0; i < groupSelect.options.length; i++) {
        const option = groupSelect.options[i];
        if (option.value === oldGroupName && option.getAttribute('data-faculty') === oldFaculty) {
          groupSelect.selectedIndex = i;
          console.log('Option selected:', option.value);
          updateInputs();
          break;
        }
      }
    } else {
      updateInputs();
    }

    // Update when dropdown changes
    groupSelect.addEventListener('change', function() {
      console.log('Dropdown o\'zgardi');
      updateInputs();
    });
    form.addEventListener('submit', function(e) {
      console.log('Form submit...');

      // Ensure group_name and faculty are filled
      if (!groupNameInput.value || !facultyInput.value) {
        console.log('Group va faculty bo\'sh, to\'xtatildi');

        // Show error message
        let errorAlert = document.querySelector('.alert-danger');
        if (!errorAlert) {
          errorAlert = document.createElement('div');
          errorAlert.className = 'alert alert-danger alert-dismissible fade show';
          errorAlert.innerHTML = `
            <i class="fa fa-exclamation-circle me-2"></i>
            <strong>Guruh tanlang!</strong> Guruhni tanlab, keyin saqlang.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          `;
          form.insertBefore(errorAlert, form.firstChild);
        }

        // Trigger validation
        groupNameInput.reportValidity();
        facultyInput.reportValidity();

        e.preventDefault();
        return false;
      }

      console.log('Form yuborilmoqda:', {
        groupName: groupNameInput.value,
        faculty: facultyInput.value
      });
    });
    const validateDates = function() {
      if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (endDate < startDate) {
          endDateInput.setCustomValidity('Tugash sanasi boshlanish sanasidan keyin bo\'lishi kerak');
          return false;
        } else {
          endDateInput.setCustomValidity('');
          return true;
        }
      }
      if (endDateInput) {
        endDateInput.setCustomValidity('');
      }
      return true;
    };
    
    // Check dates on change
    if (startDateInput && endDateInput) {
      startDateInput.addEventListener('change', validateDates);
      endDateInput.addEventListener('change', validateDates);
    }
    
    console.log('Barcha event listenerlar o\'rnatildi');
  });
</script>
@endpush
