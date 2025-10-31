@extends('layouts.admin')

@section('title', 'Tashkilotlar')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map, #editMap {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .leaflet-control-geocoder {
        border-radius: 4px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Tashkilotlar ro'yxati</h1>
            <p class="text-muted mb-0">Barcha tashkilotlarni ko'rish va boshqarish</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importOrgModal">
                <i class="fa fa-file-excel me-2"></i>Excel dan yuklash
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrgModal">
                <i class="fa fa-plus me-2"></i>Yangi tashkilot qo'shish
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
                            <th>Tashkilot nomi</th>
                            <th>Manzil</th>
                            <th>Radius (km)</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Talabalar</th>
                            <th class="text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($organizations as $org)
                            <tr>
                                <td>{{ $loop->iteration + ($organizations->currentPage() - 1) * $organizations->perPage() }}</td>
                                <td><strong>{{ $org->name }}</strong></td>
                                <td>
                                    @if($org->address)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($org->address) }}" 
                                           target="_blank" 
                                           class="text-primary text-decoration-none"
                                           title="Xaritada ko'rish">
                                            <i class="fa fa-map-marker-alt me-1"></i>{{ $org->address }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($org->radius)
                                        <span class="badge bg-success">
                                            <i class="fa fa-circle-dot me-1"></i>{{ $org->radius }} km
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $org->phone ?? '-' }}</td>
                                <td>{{ $org->email ?? '-' }}</td>
                                <td>
                                    @if ($org->students_count > 0)
                                        <a href="{{ route('admin.catalogs.organizations.students', $org->id) }}" 
                                           class="badge bg-info text-decoration-none"
                                           title="Talabalar ro'yxatini ko'rish">
                                            {{ $org->students_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editOrgModal"
                                            data-id="{{ $org->id }}"
                                            data-name="{{ $org->name }}"
                                            data-address="{{ $org->address }}"
                                            data-radius="{{ $org->radius }}"
                                            data-phone="{{ $org->phone }}"
                                            data-email="{{ $org->email }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.catalogs.organizations.delete', $org->id) }}" 
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
                                    Hozircha tashkilotlar yo'q
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $organizations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addOrgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.organizations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yangi tashkilot qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tashkilot nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <!-- Map Section -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-map-marker-alt me-1"></i>Manzil va Joylashuv
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" 
                                   id="addressSearch" 
                                   class="form-control" 
                                   placeholder="Manzilni qidiring (masalan: Toshkent IT Park)...">
                            <button type="button" 
                                    class="btn btn-outline-secondary" 
                                    onclick="searchAddress()">
                                <i class="fa fa-search"></i> Qidirish
                            </button>
                        </div>
                        <div id="map"></div>
                        <input type="hidden" name="address" id="addressInput">
                        <small class="text-muted">
                            <i class="fa fa-info-circle me-1"></i>
                            Xaritadan joyni tanlang yoki yuqorida manzilni qidiring
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-circle-dot me-1"></i>Radius (km)
                        </label>
                        <input type="number" 
                               name="radius" 
                               id="radiusInput"
                               class="form-control" 
                               step="0.01" 
                               min="0" 
                               max="100"
                               placeholder="Masalan: 0.5">
                        <small class="text-muted">Davomat uchun joylashuv radiusi (kilometrda)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" placeholder="+998901234567">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="info@example.com">
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
<div class="modal fade" id="editOrgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editOrgForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Tashkilotni tahrirlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tashkilot nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editOrgName" class="form-control" required>
                    </div>
                    
                    <!-- Map Section -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-map-marker-alt me-1"></i>Manzil va Joylashuv
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" 
                                   id="editAddressSearch" 
                                   class="form-control" 
                                   placeholder="Manzilni qidiring (masalan: Toshkent IT Park)...">
                            <button type="button" 
                                    class="btn btn-outline-secondary" 
                                    onclick="searchEditAddress()">
                                <i class="fa fa-search"></i> Qidirish
                            </button>
                        </div>
                        <div id="editMap"></div>
                        <input type="hidden" name="address" id="editOrgAddress">
                        <small class="text-muted">
                            <i class="fa fa-info-circle me-1"></i>
                            Xaritadan joyni tanlang yoki yuqorida manzilni qidiring
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-circle-dot me-1"></i>Radius (km)
                        </label>
                        <input type="number" 
                               name="radius" 
                               id="editOrgRadius"
                               class="form-control" 
                               step="0.01" 
                               min="0" 
                               max="100"
                               placeholder="Masalan: 0.5">
                        <small class="text-muted">Davomat uchun joylashuv radiusi (kilometrda)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" id="editOrgPhone" class="form-control" placeholder="+998901234567">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editOrgEmail" class="form-control" placeholder="info@example.com">
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
<div class="modal fade" id="importOrgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.catalogs.organizations.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Excel dan tashkilotlar yuklash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Format:</strong> Excel faylda quyidagi ustunlar bo'lishi kerak:
                        <ul class="mb-0 mt-2">
                            <li><strong>A ustun:</strong> Tashkilot nomi (majburiy)</li>
                            <li><strong>B ustun:</strong> Manzil (ixtiyoriy)</li>
                            <li><strong>C ustun:</strong> Telefon (ixtiyoriy)</li>
                            <li><strong>D ustun:</strong> Email (ixtiyoriy)</li>
                        </ul>
                        <small class="text-muted">Birinchi qator sarlavha sifatida o'tkazib yuboriladi.</small>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker, editMap, editMarker;
const defaultLat = 41.2995; // Tashkent
const defaultLng = 69.2401;

// Initialize Add Modal Map
function initMap() {
    if (map) {
        map.remove();
    }
    
    map = L.map('map').setView([defaultLat, defaultLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    console.log('Add map initialized');
    
    // Click to add marker
    map.on('click', function(e) {
        console.log('Map clicked at:', e.latlng);
        
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(map);
        
        // Reverse geocoding to get address
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                console.log('Reverse geocoded:', data.display_name);
                document.getElementById('addressInput').value = data.display_name;
                document.getElementById('addressSearch').value = data.display_name;
            })
            .catch(error => {
                console.error('Reverse geocoding error:', error);
            });
    });
}

// Initialize Edit Modal Map
function initEditMap(address = null) {
    if (editMap) {
        editMap.remove();
    }
    
    editMap = L.map('editMap').setView([defaultLat, defaultLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(editMap);
    
    // If address exists, geocode it
    if (address && address.trim() !== '') {
        // Add delay to respect Nominatim rate limit
        setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        editMap.setView([lat, lng], 15);
                        if (editMarker) {
                            editMap.removeLayer(editMarker);
                        }
                        editMarker = L.marker([lat, lng]).addTo(editMap);
                        console.log('Location found:', data[0].display_name);
                    } else {
                        console.log('Location not found for:', address);
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                });
        }, 300);
    }
    
    // Click to add/move marker
    editMap.on('click', function(e) {
        if (editMarker) {
            editMap.removeLayer(editMarker);
        }
        editMarker = L.marker(e.latlng).addTo(editMap);
        
        // Reverse geocoding to get address
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editOrgAddress').value = data.display_name;
                document.getElementById('editAddressSearch').value = data.display_name;
            });
    });
}

// Search address in Add modal
function searchAddress() {
    const query = document.getElementById('addressSearch').value;
    console.log('Searching for:', query);
    
    if (!query || query.trim() === '') {
        alert('Iltimos, manzilni kiriting');
        return;
    }
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
        .then(response => response.json())
        .then(data => {
            console.log('Search results:', data);
            
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                console.log('Moving map to:', lat, lng);
                map.setView([lat, lng], 15);
                
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);
                
                document.getElementById('addressInput').value = data[0].display_name;
                document.getElementById('addressSearch').value = data[0].display_name;
                console.log('Address set to:', data[0].display_name);
            } else {
                alert('Manzil topilmadi. Boshqa so\'z bilan qidiring.');
                console.log('No results found');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            alert('Xatolik yuz berdi. Iltimos qayta urinib ko\'ring.');
        });
}

// Search address in Edit modal
function searchEditAddress() {
    const query = document.getElementById('editAddressSearch').value;
    if (!query) {
        alert('Iltimos, manzilni kiriting');
        return;
    }
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                editMap.setView([lat, lng], 15);
                
                if (editMarker) {
                    editMap.removeLayer(editMarker);
                }
                editMarker = L.marker([lat, lng]).addTo(editMap);
                
                // Update both address fields
                document.getElementById('editOrgAddress').value = data[0].display_name;
                document.getElementById('editAddressSearch').value = data[0].display_name;
            } else {
                alert('Manzil topilmadi. Boshqa so\'z bilan qidiring.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xatolik yuz berdi');
        });
}

// Initialize maps when modals are shown
document.getElementById('addOrgModal').addEventListener('shown.bs.modal', function() {
    setTimeout(() => {
        initMap();
    }, 100);
});

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editOrgModal');
    
    // Populate form fields FIRST, then initialize map
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const address = button.getAttribute('data-address');
        const radius = button.getAttribute('data-radius');
        const phone = button.getAttribute('data-phone');
        const email = button.getAttribute('data-email');
        
        // Populate form fields
        document.getElementById('editOrgName').value = name;
        document.getElementById('editOrgAddress').value = address || '';
        document.getElementById('editOrgRadius').value = radius || '';
        document.getElementById('editOrgPhone').value = phone || '';
        document.getElementById('editOrgEmail').value = email || '';
        document.getElementById('editAddressSearch').value = address || '';
        document.getElementById('editOrgForm').action = `{{ url('admin/catalogs/organizations') }}/${id}`;
    });
    
    // Initialize map AFTER modal is shown and fields are populated
    editModal.addEventListener('shown.bs.modal', function() {
        setTimeout(() => {
            const address = document.getElementById('editOrgAddress').value;
            if (address && address.trim() !== '') {
                initEditMap(address);
            } else {
                initEditMap();
            }
        }, 150);
    });
});
</script>
@endsection
