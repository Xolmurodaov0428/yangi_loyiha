@extends('layouts.supervisor')

@section('title', 'Davomat')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Talabalar Davomati</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Date Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Sana tanlang</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $date }}">
                </div>
                @if($selectedGroup)
                    <div class="col-md-4">
                        <label class="form-label">Guruh</label>
                        <select class="form-control" name="group_id">
                            <option value="">Barcha guruhlar</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $selectedGroup->id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-search me-1"></i>Filtrlash
                    </button>
                    <a href="{{ route("supervisor.attendance") }}" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh me-1"></i>Tozalash
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Groups and Students in side-by-side layout -->
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column - Groups and Students -->
            <div class="col-12">
                <!-- Groups Overview -->
                @if($groups->count() > 0)
                    <div class="row g-3 mb-4">
                        @forelse($groups as $group)
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm {{ $selectedGroup && $selectedGroup->id === $group->id ? 'border border-success border-2' : '' }}" style="transition: all 0.3s ease; cursor: pointer; min-height: 140px;" onclick="window.location.href='{{ route('supervisor.attendance', ['group_id' => $group->id, 'date' => $date]) }}'">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3 flex-shrink-0">
                                                <i class="fa fa-layer-group text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="fw-bold mb-1">{{ $group->name }}</h6>
                                                <small class="text-muted d-block">{{ $group->faculty ?? 'Informatika' }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2 mt-auto">
                                            <span class="badge bg-success flex-shrink-0">
                                                <i class="fa fa-users me-1"></i>{{ $group->students_count ?? 0 }} talaba
                                            </span>
                                            <button class="btn btn-sm {{ $selectedGroup && $selectedGroup->id === $group->id ? 'btn-success text-white' : 'btn-outline-success' }} flex-shrink-0" style="pointer-events: none; white-space: nowrap;">
                                                {{ $selectedGroup && $selectedGroup->id === $group->id ? 'Tanlangan' : "Tanlash" }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <i class="fa fa-info-circle me-2"></i>Sizga hali guruhlar biriktirilmagan.
                                </div>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- Students Table and Location in same row -->
                @if($selectedGroup)
                    <div class="row">
                        <!-- Students Table - Full Width -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="card-title fw-bold mb-0">
                                                <i class="fa fa-users me-2 text-success"></i>
                                                {{ $selectedGroup->name }} guruhi davomati
                                            </h5>
                                            <small class="text-muted">Jami: {{ $students->count() }} talaba</small>
                                        </div>
                                        <div class="text-muted small">
                                            Sana: {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
                                        </div>
                                    </div>

                                    @if($students->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Talaba</th>
                                                        <th>Guruh</th>
                                                        <th>1-Seans<br><small class="text-muted">09:00</small></th>
                                                        <th>2-Seans<br><small class="text-muted">13:00</small></th>
                                                        <th>3-Seans<br><small class="text-muted">17:00</small></th>
                                                        <th>Harakatlar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($students as $student)
                                                        <tr data-student-id="{{ $student->id }}">
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                                                        <i class="fa fa-user text-primary"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong class="fw-semibold">{{ $student->full_name }}</strong>
                                                                        <div class="text-muted small">{{ $student->username }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $student->group_name }}</td>

                                                            @for($i = 1; $i <= 3; $i++)
                                                                @php
                                                                    $sessionKey = 'session_' . $i;
                                                                    $session = $student->sessions[$sessionKey] ?? null;
                                                                    $status = $session['status'] ?? 'absent';
                                                                @endphp
                                                                <td class="position-relative text-center">
                                                                    @if($session && $session['attendance'])
                                                                        @if($status === 'present')
                                                                            <div class="d-flex flex-column align-items-center">
                                                                                <span class="badge bg-success mb-1">✓ Keldi</span>
                                                                                <small class="text-muted">{{ $session['check_in_time'] ? $session['check_in_time']->format('H:i') : '' }}</small>
                                                                                @if($session['latitude'] && $session['longitude'])
                                                                                    <button class="btn btn-xs btn-outline-info mt-1" onclick="showLocationDetails({{ $session['latitude'] }}, {{ $session['longitude'] }}, '{{ $session['location_address'] ?? 'Aniqlanmagan' }}')" title="Joylashuvni ko'rish">
                                                                                        <i class="fa fa-map-marker"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        @elseif($status === 'absent')
                                                                            <span class="badge bg-danger">✗ Kelmadi</span>
                                                                        @elseif($status === 'late')
                                                                            <div class="d-flex flex-column align-items-center">
                                                                                <span class="badge bg-warning mb-1">⏰ Kech</span>
                                                                                <small class="text-muted">{{ $session['check_in_time'] ? $session['check_in_time']->format('H:i') : '' }}</small>
                                                                                @if($session['latitude'] && $session['longitude'])
                                                                                    <button class="btn btn-xs btn-outline-info mt-1" onclick="showLocationDetails({{ $session['latitude'] }}, {{ $session['longitude'] }}, '{{ $session['location_address'] ?? 'Aniqlanmagan' }}')" title="Joylashuvni ko'rish">
                                                                                        <i class="fa fa-map-marker"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <span class="badge bg-info">? Noma'lum</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge bg-secondary">- Ma'lumot yo'q</span>
                                                                    @endif
                                                                </td>
                                                            @endfor

                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <button class="btn btn-sm btn-outline-primary" title="Batafsil ko'rish" onclick="viewStudentAttendance({{ $student->id }}, '{{ $date }}', '{{ addslashes($student->full_name) }}')">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button>
                                                                    @if(auth()->user()->can_mark_attendance)
                                                                        <button class="btn btn-sm btn-outline-success" title="Davomat belgilash" onclick="markAttendanceModal({{ $student->id }}, '{{ $date }}')">
                                                                            <i class="fa fa-check"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            <i class="fa fa-users fs-1 mb-3 opacity-25"></i>
                                            <p class="mb-0">Bu guruhda talabalar topilmadi</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- Show message when no group is selected -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-layer-group fs-1 mb-3 opacity-25 text-primary"></i>
                            <h5 class="mb-3">Guruh tanlang</h5>
                            <p class="text-muted">Davomat ko'rish uchun yuqoridagi guruh kartochkalaridan birini bosing.</p>
                            <div class="mt-4">
                                <i class="fa fa-arrow-up text-primary fs-2"></i>
                                <p class="text-primary mt-2"><strong>Yuqoridagi guruhlardan birini tanlang</strong></p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

<!-- Davomat belgilash Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="attendanceForm" method="POST" action="{{ route('supervisor.attendance.mark') }}">
                @csrf
                <input type="hidden" name="student_id" id="modal_student_id">
                <input type="hidden" name="date" id="modal_date">
                <input type="hidden" name="session" id="modal_session">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">
                        <i class="fa fa-check-circle me-2 text-success"></i>
                        <span id="modal_student_name"></span> - Davomat belgilash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Seans</label>
                        <select name="session" class="form-select" id="session_select" required>
                            <option value="session_1">1-Seans (09:00)</option>
                            <option value="session_2">2-Seans (13:00)</option>
                            <option value="session_3">3-Seans (17:00)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Holat</label>
                        <select name="status" class="form-select" required>
                            <option value="present">✓ Keldi</option>
                            <option value="absent">✗ Kelmadi</option>
                            <option value="late">⏰ Kech keldi</option>
                            <option value="excused">📋 Sababli</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Kelish vaqti</label>
                            <input type="time" name="check_in_time" class="form-control">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Ketish vaqti</label>
                            <input type="time" name="check_out_time" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-building me-1 text-success"></i>Amaliyot joyi
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="organization_name" class="form-control" readonly style="background-color: #f8f9fa;">
                            <button type="button" class="btn btn-outline-primary" id="viewLocationBtn" style="white-space: nowrap;" disabled>
                                <i class="fa fa-map-marker-alt me-1"></i>Lokatsiya
                            </button>
                        </div>
                        <small class="text-muted">Talaba amaliyot o'tayotgan tashkilot</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fa fa-map-marker-alt me-1 text-primary"></i>Lokatsiya
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="openMapPicker()">
                                <i class="fa fa-map me-1"></i>Xaritadan tanlash
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="latitude" id="latitude_input" class="form-control" placeholder="Kenglik (latitude)" step="any">
                            </div>
                            <div class="col-6">
                                <input type="text" name="longitude" id="longitude_input" class="form-control" placeholder="Uzunlik (longitude)" step="any">
                            </div>
                        </div>
                        <small class="text-muted">Masalan: 41.2995, 69.2401</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Izoh</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Qo'shimcha ma'lumot..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Yopish
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batafsil ko'rish Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailModalLabel">
                    <i class="fa fa-user-graduate me-2 text-primary"></i>
                    <span id="detail_student_name"></span> - Davomat tarixi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="attendanceHistoryContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yuklanmoqda...</span>
                        </div>
                        <p class="mt-3 text-muted">Ma'lumotlar yuklanmoqda...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Yopish
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Map Picker Modal -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-labelledby="mapPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapPickerModalLabel">
                    <i class="fa fa-map-marked-alt me-2 text-success"></i>Xaritadan lokatsiya tanlash
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i>
                    Xaritada kerakli joyni bosing yoki qidiruv orqali toping
                </div>
                <div id="map" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanlangan kenglik:</label>
                            <input type="text" id="selected_latitude" class="form-control" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanlangan uzunlik:</label>
                            <input type="text" id="selected_longitude" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Bekor qilish
                </button>
                <button type="button" class="btn btn-success" onclick="confirmLocation()">
                    <i class="fa fa-check me-1"></i>Tasdiqlash
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">📍 Joylashuv ma'lumotlari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h6>Koordinatalar:</h6>
                    <p class="mb-2"><strong>Kenglik:</strong> <span id="modalLatitude">-</span></p>
                    <p class="mb-2"><strong>Uzunlik:</strong> <span id="modalLongitude">-</span></p>
                    <p class="mb-0"><strong>Manzil:</strong> <span id="modalAddress">Aniqlanmagan</span></p>
                </div>
                <div id="mapContainer" style="height: 300px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <p class="text-muted mb-0">🗺️ Xarita ko'rinishi</p>
                </div>
                <button class="btn btn-primary" id="openInMapsBtn">
                    <i class="fa fa-map-marker me-2"></i>Xaritada ochish
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function markAttendance(studentId, session, status, date) {
    // Get user's location first
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Location obtained successfully
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Get address using reverse geocoding (optional)
                getLocationAddress(latitude, longitude).then(address => {
                    sendAttendanceRequest(studentId, session, status, date, latitude, longitude, address);
                }).catch(() => {
                    // If address lookup fails, still send request with coordinates only
                    sendAttendanceRequest(studentId, session, status, date, latitude, longitude, null);
                });
            },
            function(error) {
                // Location access denied or error
                console.log('Location access denied or error:', error);
                // Still allow attendance marking without location
                sendAttendanceRequest(studentId, session, status, date, null, null, null);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            }
        );
    } else {
        // Geolocation not supported
        alert('Sizning qurilmangizda joylashuv aniqlash imkoni mavjud emas!');
        sendAttendanceRequest(studentId, session, status, date, null, null, null);
    }
}

function sendAttendanceRequest(studentId, session, status, date, latitude, longitude, address) {
    // AJAX call to update attendance for specific session
    fetch('{{ route("admin.students.attendance.mark") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            student_id: studentId,
            session: session,
            status: status,
            date: date,
            latitude: latitude,
            longitude: longitude,
            location_address: address
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh page to show updated data
        } else {
            alert('Xatolik: ' + data.message);
        }
    })
    .catch(error => {
        alert('Tarmoq xatoligi!');
    });
}

function getLocationAddress(latitude, longitude) {
    // Use a geocoding service to get address from coordinates
    return fetch(`https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=YOUR_API_KEY&pretty=1`)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                return data.results[0].formatted;
            }
            return null;
        })
        .catch(error => {
            console.log('Address lookup failed:', error);
            return null;
        });
}

function showLocationModal(session) {
    // Ma'lumotlarni yig'ish
    const locations = [];
    @php
        $sessionLocations = [];
        if(isset($sessionsWithLocation)) {
            foreach($sessionsWithLocation as $location) {
                $sessionLocations[] = $location;
            }
        }
    @endphp

    @foreach($sessionLocations as $location)
        locations.push({
            student: '{{ $location['student'] }}',
            session: '{{ $location['session'] }}',
            lat: {{ $location['lat'] }},
            lng: {{ $location['lng'] }},
            address: '{{ $location['address'] ?? 'Aniqlanmagan' }}'
        });
    @endforeach

    // Modal elementlariga ma'lumotlarni yozish
    const modalTitle = document.getElementById('locationModalLabel');
    const modalBody = document.querySelector('#locationModal .modal-body');

    if (locations.length > 0) {
        let content = `<div class="mb-3">
            <h6>${session.charAt(0).toUpperCase() + session.slice(1)} seansi joylashuvlari:</h6>
            <div class="list-group">`;

        locations.forEach((location, index) => {
            if (location.session === session.replace('session_', '')) {
                content += `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${location.student}</h6>
                            <small class="text-muted">${location.session}-seans</small>
                        </div>
                        <p class="mb-1"><strong>Koordinatalar:</strong> ${location.lat}, ${location.lng}</p>
                        <p class="mb-1"><strong>Manzil:</strong> ${location.address}</p>
                        <button class="btn btn-sm btn-outline-primary mt-1" onclick="openInMaps(${location.lat}, ${location.lng})">
                            <i class="fa fa-map-marker me-1"></i>Xaritada ko'rish
                        </button>
                    </div>
                `;
            }
        });

        content += `</div></div>`;

        if (content.includes('list-group-item')) {
            modalBody.innerHTML = content;
            modalTitle.textContent = `📍 ${session.charAt(0).toUpperCase() + session.slice(1)} seansi joylashuvlari`;

            const modal = new bootstrap.Modal(document.getElementById('locationModal'));
            modal.show();
        } else {
            alert('Bu seans uchun joylashuv ma\'lumotlari topilmadi!');
        }
    } else {
        alert('Joylashuv ma\'lumotlari topilmadi!');
    }
}

function openInMaps(latitude, longitude) {
    // Open location in Google Maps or other map service
    const url = `https://www.google.com/maps?q=${latitude},${longitude}`;
    window.open(url, '_blank');
}

function viewStudentAttendance(studentId, date, studentNameFromBtn) {
    console.log('Viewing attendance for student:', studentId, 'on date:', date);

    // Talaba ma'lumotlarini olish
    const studentRow = document.querySelector(`tr[data-student-id="${studentId}"]`);
    console.log('Found student row:', studentRow);

    const fallbackName = studentRow ? (studentRow.querySelector('.fw-semibold')?.textContent || 'Talaba') : 'Talaba';
    const studentName = studentNameFromBtn || fallbackName;
    console.log('Student name:', studentName);

    // Modal nomini o'rnatish
    document.getElementById('detail_student_name').textContent = studentName;
    
    // Modalni ochish
    const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
    modal.show();
    
    // AJAX orqali davomat tarixini yuklash
    const historyUrlTemplate = "{{ route('supervisor.students.attendance.history', ['studentId' => 'STUDENT_ID_PLACEHOLDER']) }}";
    const historyUrl = historyUrlTemplate.replace('STUDENT_ID_PLACEHOLDER', encodeURIComponent(studentId));
    fetch(historyUrl, {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Ma\'lumot yuklanmadi');
            }
            let html = '';
            
            if (data.attendances && data.attendances.length > 0) {
                html = `
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sana</th>
                                    <th>Seans</th>
                                    <th>Holat</th>
                                    <th>Kelish vaqti</th>
                                    <th>Ketish vaqti</th>
                                    <th>Joylashuv</th>
                                    <th>Izoh</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.attendances.forEach(att => {
                    const statusBadge = att.status === 'present' ? 
                        '<span class="badge bg-success"><i class="fa fa-check me-1"></i>Keldi</span>' :
                        att.status === 'late' ?
                        '<span class="badge bg-warning"><i class="fa fa-clock me-1"></i>Kech</span>' :
                        att.status === 'excused' ?
                        '<span class="badge bg-info"><i class="fa fa-file-medical me-1"></i>Sababli</span>' :
                        '<span class="badge bg-danger"><i class="fa fa-times me-1"></i>Kelmadi</span>';
                    
                    const sessionName = att.session === 'session_1' ? '1-Seans (09:00)' :
                                       att.session === 'session_2' ? '2-Seans (13:00)' :
                                       '3-Seans (17:00)';
                    
                    const locationBtn = att.latitude && att.longitude ?
                        `<button class="btn btn-sm btn-outline-primary" onclick="showLocationDetails(${att.latitude}, ${att.longitude}, '${att.location_address || 'Aniqlanmagan'}')">
                            <i class="fa fa-map-marker-alt"></i>
                        </button>` :
                        '<span class="text-muted">-</span>';
                    
                    const checkInTime = att.check_in_time ? new Date(att.check_in_time).toLocaleTimeString('uz-UZ', {hour: '2-digit', minute: '2-digit'}) : '<span class="text-muted">-</span>';
                    const checkOutTime = att.check_out_time ? new Date(att.check_out_time).toLocaleTimeString('uz-UZ', {hour: '2-digit', minute: '2-digit'}) : '<span class="text-muted">-</span>';
                    
                    html += `
                        <tr>
                            <td>${att.date_formatted}</td>
                            <td>${sessionName}</td>
                            <td>${statusBadge}</td>
                            <td>${checkInTime}</td>
                            <td>${checkOutTime}</td>
                            <td>${locationBtn}</td>
                            <td>${att.notes || '<span class="text-muted">-</span>'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="card border-0 bg-success bg-opacity-10">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-success">${data.stats.present}</h5>
                                        <small class="text-muted">Keldi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card border-0 bg-danger bg-opacity-10">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-danger">${data.stats.absent}</h5>
                                        <small class="text-muted">Kelmadi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card border-0 bg-warning bg-opacity-10">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-warning">${data.stats.late}</h5>
                                        <small class="text-muted">Kech</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card border-0 bg-info bg-opacity-10">
                                    <div class="card-body py-2">
                                        <h5 class="mb-0 text-info">${data.stats.percentage}%</h5>
                                        <small class="text-muted">Davomat</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="text-center py-5">
                        <i class="fa fa-clipboard-list fs-1 text-muted opacity-25 mb-3"></i>
                        <h5 class="text-muted">Davomat tarixi topilmadi</h5>
                        <p class="text-muted mb-0">Bu talaba uchun hali davomat belgilanmagan</p>
                    </div>
                `;
            }
            
            document.getElementById('attendanceHistoryContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('attendanceHistoryContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fa fa-exclamation-triangle fs-1 text-danger opacity-25 mb-3"></i>
                    <h5 class="text-danger">Xatolik yuz berdi</h5>
                    <p class="text-muted mb-0">Ma'lumotlarni yuklashda xatolik yuz berdi</p>
                    <small class="text-danger">${error.message || 'Noma\'lum xatolik'}</small>
                </div>
            `;
        });
}

function showLocationDetails(latitude, longitude, address) {
    // Show location details in modal
    document.getElementById('modalLatitude').textContent = latitude;
    document.getElementById('modalLongitude').textContent = longitude;
    document.getElementById('modalAddress').textContent = address;
    
    const modal = new bootstrap.Modal(document.getElementById('locationModal'));
    modal.show();
    
    // Set up the "Open in Maps" button
    document.getElementById('openInMapsBtn').onclick = function() {
        openInMaps(latitude, longitude);
    };
}

function markAttendanceModal(studentId, date) {
    // Talaba ma'lumotlarini olish
    const studentRow = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const studentName = studentRow ? studentRow.querySelector('.fw-semibold').textContent : 'Talaba';
    
    // Modal ma'lumotlarini to'ldirish
    document.getElementById('modal_student_id').value = studentId;
    document.getElementById('modal_date').value = date;
    document.getElementById('modal_student_name').textContent = studentName;
    
    // Talabaning tashkilot ma'lumotlarini yuklash
    fetch(`/supervisor/students/${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.student) {
                // Tashkilot nomini ko'rsatish
                const orgName = data.student.organization ? data.student.organization.name : 'Tashkilot belgilanmagan';
                document.getElementById('organization_name').value = orgName;
                
                // Agar tashkilotda lokatsiya bo'lsa
                if (data.student.organization && data.student.organization.latitude && data.student.organization.longitude) {
                    const lat = data.student.organization.latitude;
                    const lon = data.student.organization.longitude;
                    
                    // Lokatsiya inputlariga qo'yish
                    document.getElementById('latitude_input').value = lat;
                    document.getElementById('longitude_input').value = lon;
                    
                    // Lokatsiya tugmasini faollashtirish
                    const viewBtn = document.getElementById('viewLocationBtn');
                    viewBtn.disabled = false;
                    viewBtn.onclick = function() {
                        const address = data.student.organization.address || 'Manzil ko\'rsatilmagan';
                        showLocationDetails(lat, lon, address);
                    };
                } else {
                    // Lokatsiya yo'q bo'lsa
                    document.getElementById('latitude_input').value = '';
                    document.getElementById('longitude_input').value = '';
                    document.getElementById('viewLocationBtn').disabled = true;
                }
            }
        })
        .catch(error => {
            console.error('Talaba ma\'lumotlarini yuklashda xatolik:', error);
            document.getElementById('organization_name').value = 'Ma\'lumot yuklanmadi';
        });
    
    // Modalni ochish
    const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
    modal.show();
}

// Xarita o'zgaruvchilari
let map;
let marker;
let selectedLat;
let selectedLng;

// Xarita modalini ochish
function openMapPicker() {
    const mapModal = new bootstrap.Modal(document.getElementById('mapPickerModal'));
    mapModal.show();
    
    // Modal to'liq ochilgandan keyin xaritani yuklash
    setTimeout(() => {
        if (!map) {
            // Toshkent koordinatalari
            const defaultLat = 41.2995;
            const defaultLng = 69.2401;
            
            // Xaritani yaratish
            map = L.map('map').setView([defaultLat, defaultLng], 13);
            
            // OpenStreetMap qatlami
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Marker qo'shish
            marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            
            // Boshlang'ich koordinatalar
            selectedLat = defaultLat;
            selectedLng = defaultLng;
            document.getElementById('selected_latitude').value = defaultLat;
            document.getElementById('selected_longitude').value = defaultLng;
            
            // Marker tortilganda
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                selectedLat = position.lat.toFixed(6);
                selectedLng = position.lng.toFixed(6);
                document.getElementById('selected_latitude').value = selectedLat;
                document.getElementById('selected_longitude').value = selectedLng;
            });
            
            // Xaritani bosganda
            map.on('click', function(e) {
                selectedLat = e.latlng.lat.toFixed(6);
                selectedLng = e.latlng.lng.toFixed(6);
                marker.setLatLng([selectedLat, selectedLng]);
                document.getElementById('selected_latitude').value = selectedLat;
                document.getElementById('selected_longitude').value = selectedLng;
            });
        } else {
            // Xaritani yangilash
            setTimeout(() => map.invalidateSize(), 100);
        }
    }, 300);
}

// Lokatsiyani tasdiqlash
function confirmLocation() {
    if (selectedLat && selectedLng) {
        document.getElementById('latitude_input').value = selectedLat;
        document.getElementById('longitude_input').value = selectedLng;
        
        // Modalni yopish
        const mapModal = bootstrap.Modal.getInstance(document.getElementById('mapPickerModal'));
        mapModal.hide();
    }
}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection
