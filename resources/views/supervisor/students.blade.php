@extends('layouts.supervisor')

@section('title', 'Biriktirilgan Talabalar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">
            @if($selectedGroup)
                {{ $selectedGroup->name }} guruhi talabalari
            @else
                Biriktirilgan Talabalar
            @endif
        </h1>
        <div class="d-flex gap-2">
            @if($selectedGroup)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#groupMessageModal">
                    <i class="fa fa-paper-plane me-2"></i>Guruhga xabar yuborish
                </button>
                <a href="{{ route('supervisor.students') }}" class="btn btn-outline-success">
                    <i class="fa fa-layer-group me-2"></i>Hamma guruhlar
                </a>
            @endif
            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Groups Overview -->
    <div class="row g-3 mb-4">
        @php $selectedGroupId = $selectedGroup->id ?? null; @endphp
        @forelse($groups as $group)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 {{ $selectedGroupId === $group->id ? 'border border-success' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="fa fa-layer-group text-success fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $group->name }}</h5>
                                <small class="text-muted">{{ $group->faculty ?? 'Fakultet belgilanmagan' }}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success">
                                    <i class="fa fa-users me-1"></i>{{ $group->students_count ?? 0 }} talaba
                                </span>
                            </div>
                            <a href="{{ route('supervisor.students.group', $group->id) }}" class="btn btn-sm {{ $selectedGroupId === $group->id ? 'btn-success text-white' : 'btn-outline-success' }}">
                                <i class="fa fa-arrow-right me-1"></i>{{ $selectedGroupId === $group->id ? 'Tanlangan' : "Ko'rish" }}
                            </a>
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

    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="card-title fw-bold mb-0">
                        <i class="fa fa-users me-2 text-success"></i>
                        @if($selectedGroup)
                            {{ $selectedGroup->name }} guruhi ro'yxati
                        @else
                            Talabalar Ro'yxati
                        @endif
                    </h5>
                    @if($selectedGroup && ($selectedGroup->students_count ?? null) !== null)
                        <small class="text-muted">Jami: {{ $selectedGroup->students_count }} talaba</small>
                    @endif
                </div>
                <div class="text-muted small">
                    Jami: @php try { echo $students->total(); } catch (\Exception $e) { echo 0; } @endphp ta
                </div>
            </div>

            @php $studentCount = 0; try { $studentCount = $students->count(); } catch (\Exception $e) {} @endphp
            @if($studentCount > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Ism</th>
                                <th>Guruh</th>
                                <th>Tashkilot</th>
                                <th>Amaliyot Mudati</th>
                                <th>Holati</th>
                                <th>Harakatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $studentList = []; try { $studentList = $students; } catch (\Exception $e) {} @endphp
                            @foreach($studentList as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                                                <i class="fa fa-user text-success"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $student->full_name }}</strong>
                                                <div class="text-muted small">{{ $student->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->group_name }}</td>
                                    <td>{{ $student->organization->name ?? 'Belgilanmagan' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $student->internship_start_date ? $student->internship_start_date->format('d.m.Y') : 'Boshlanmagan' }}
                                            - {{ $student->internship_end_date ? $student->internship_end_date->format('d.m.Y') : 'Tugallanmagan' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('supervisor.messages.show', $student->id) }}" class="btn btn-sm btn-outline-primary" title="Chat">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                            <a href="{{ route('supervisor.attendance') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-info" title="Davomat">
                                                <i class="fa fa-clipboard-check"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $students->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-users fs-1 mb-3 opacity-25"></i>
                    @if($selectedGroup)
                        <p class="mb-0">Bu guruh uchun talabalar topilmadi</p>
                    @else
                        <p class="mb-0">Hozircha biriktirilgan talabalar yo'q</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
</div>

<!-- Group Message Modal -->
@if($selectedGroup)
<div class="modal fade" id="groupMessageModal" tabindex="-1" aria-labelledby="groupMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="groupMessageModalLabel">
                    <i class="fa fa-paper-plane me-2"></i>{{ $selectedGroup->name }} guruhiga xabar yuborish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="groupMessageForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>{{ $selectedGroup->students_count ?? 0 }} ta talabaga</strong> xabar yuboriladi
                    </div>
                    <div class="mb-3">
                        <label for="groupMessage" class="form-label">Xabar matni <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="groupMessage" name="message" rows="5" 
                                  placeholder="Guruhga yubormoqchi bo'lgan xabaringizni yozing..." required></textarea>
                        <small class="text-muted">Maksimal: 5000 belgi</small>
                    </div>
                    <div class="mb-3">
                        <label for="groupAttachment" class="form-label">Fayl biriktirish (ixtiyoriy)</label>
                        <input type="file" class="form-control" id="groupAttachment" name="attachment" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                        <small class="text-muted">PDF, DOC, rasm, ZIP (max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Bekor qilish
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendGroupMessageBtn">
                        <i class="fa fa-paper-plane me-1"></i>Yuborish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
@if($selectedGroup)
document.addEventListener('DOMContentLoaded', function() {
    const groupMessageForm = document.getElementById('groupMessageForm');
    const sendBtn = document.getElementById('sendGroupMessageBtn');
    const modal = document.getElementById('groupMessageModal');
    const bsModal = new bootstrap.Modal(modal);

    groupMessageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Yuborilmoqda...';

        fetch('{{ route("supervisor.messages.send-group", $selectedGroup->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fa fa-check-circle me-2"></i>${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
                
                // Reset form and close modal
                groupMessageForm.reset();
                bsModal.hide();
                
                // Auto-hide alert after 5 seconds
                setTimeout(() => alertDiv.remove(), 5000);
            } else {
                alert('Xatolik: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xabar yuborishda xatolik yuz berdi');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Yuborish';
        });
    });
});
@endif
</script>
@endsection
