@extends('layouts.supervisor')

@section('title', 'Davomat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Talabalar Davomati</h1>
        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-2"></i>Dashboard
        </a>
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
                    <a href="{{ route('supervisor.attendance') }}" class="btn btn-outline-secondary">
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
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                                                        <i class="fa fa-user text-primary"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $student->full_name }}</strong>
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
                                                                    <button class="btn btn-sm btn-outline-primary" title="Batafsil ko'rish" onclick="viewStudentAttendance({{ $student->id }}, '{{ $date }}')">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-success" title="Davomat belgilash" onclick="markAttendanceModal({{ $student->id }}, '{{ $date }}')">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
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
    fetch('{{ route("supervisor.attendance.mark") }}', {
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

function viewStudentAttendance(studentId, date) {
    // Redirect to detailed attendance view for specific student
    window.location.href = '{{ route("supervisor.attendance") }}?student_id=' + studentId + '&date=' + date;
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
    // Show attendance marking modal or redirect to marking page
    if(confirm('Bu talaba uchun davomat belgilashni xohlaysizmi?')) {
        // You can implement attendance marking functionality here
        alert('Davomat belgilash funksiyasi ishlab chiqilmoqda...');
    }
}
</script>
@endsection
