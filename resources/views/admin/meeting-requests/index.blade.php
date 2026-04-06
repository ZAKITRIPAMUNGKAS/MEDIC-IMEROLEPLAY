@extends('layouts.app')

@section('title', 'Meeting Requests — Admin - Portal Medis')

@section('content')
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                        <div class="w-12 h-12 bg-sky-500/20 text-sky-400 rounded-xl flex items-center justify-center shadow-lg border border-sky-500/30">
                            <i class="fas fa-calendar-check text-2xl"></i>
                        </div>
                        Pengajuan Meeting
                    </h1>
                    <p class="text-sky-200 mt-2 text-sm ml-15">Kelola dan tinjau permintaan jadwal meeting dari staf Anda.</p>
                </div>

                <!-- Search -->
                <form method="GET" class="flex items-center gap-3 w-full md:w-auto">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative w-full md:w-80">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama staf..."
                            class="block w-full pl-11 pr-4 py-3 border border-white/20 rounded-xl text-sm placeholder-gray-400 focus:ring-2 focus:ring-sky-400 bg-white/10 text-white shadow-sm transition-all backdrop-blur-sm">
                    </div>
                    <button type="submit" class="px-5 py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm border border-sky-500/50">
                        Cari
                    </button>
                    @if(request('q'))
                        <a href="?status={{ $status }}" title="Hapus Pencarian"
                            class="px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-xl text-sm transition-colors border border-red-500/30 flex items-center justify-center">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Stats / Tabs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
                <!-- Pending Tab -->
                <a href="?status=pending" class="group glass-effect rounded-2xl p-6 border shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden {{ $status === 'pending' ? 'border-sky-400 ring-1 ring-sky-400/50' : 'border-white/10 hover:border-white/30 bg-white/5' }}">
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-xs font-bold text-gray-300 uppercase tracking-widest mb-1">Menunggu</p>
                            <p class="text-4xl font-black text-white">{{ $pendingCount }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center transform transition-transform group-hover:rotate-6 shadow-inner border border-amber-500/30">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Approved Tab -->
                <a href="?status=approved" class="group glass-effect rounded-2xl p-6 border shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden {{ $status === 'approved' ? 'border-sky-400 ring-1 ring-sky-400/50' : 'border-white/10 hover:border-white/30 bg-white/5' }}">
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-xs font-bold text-gray-300 uppercase tracking-widest mb-1">Disetujui</p>
                            <p class="text-4xl font-black text-white">{{ $approvedCount }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center transform transition-transform group-hover:rotate-6 shadow-inner border border-emerald-500/30">
                            <i class="fas fa-check-double text-2xl"></i>
                        </div>
                    </div>
                </a>

                <!-- Rejected Tab -->
                <a href="?status=rejected" class="group glass-effect rounded-2xl p-6 border shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden {{ $status === 'rejected' ? 'border-sky-400 ring-1 ring-sky-400/50' : 'border-white/10 hover:border-white/30 bg-white/5' }}">
                    <div class="flex items-start justify-between relative z-10">
                        <div>
                            <p class="text-xs font-bold text-gray-300 uppercase tracking-widest mb-1">Ditolak</p>
                            <p class="text-4xl font-black text-white">{{ $rejectedCount }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center transform transition-transform group-hover:rotate-6 shadow-inner border border-rose-500/30">
                            <i class="fas fa-times text-2xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Requests List -->
            <div class="space-y-5">
                @if($requests->isEmpty())
                    <div class="glass-effect border border-white/20 rounded-3xl p-16 text-center shadow-lg">
                        <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-5 text-sky-400 shadow-inner border border-white/10">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Tidak ada pengajuan</h3>
                        <p class="text-sky-200/80 mt-2 max-w-sm mx-auto">Belum ada pengajuan meeting {{ $status === 'pending' ? 'yang menunggu persetujuan' : '' }} saat ini. Semuanya sudah tertangani!</p>
                    </div>
                @else
                    @foreach($requests as $req)
                        @php
                            $badge = $req->getStatusBadge();
                            $colorMap = [
                                'yellow' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                'green' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                'red' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                'gray' => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                            ];
                            $statusStyle = $colorMap[$badge['color']] ?? $colorMap['gray'];
                        @endphp

                        <div class="bg-white/5 backdrop-blur-md rounded-3xl border border-white/10 shadow-lg hover:border-white/30 transition-all duration-300 p-6 sm:p-8" id="request-{{ $req->id }}">
                            <div class="flex flex-col lg:flex-row justify-between gap-8">
                                
                                <!-- Content Info -->
                                <div class="flex-1">
                                    <!-- User Header -->
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 text-white font-bold flex items-center justify-center text-xl shadow-md border border-white/20">
                                            {{ strtoupper(substr($req->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-white leading-tight">{{ $req->user->name }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                <span class="text-xs font-semibold bg-white/10 text-gray-300 border border-white/10 px-2.5 py-1 rounded-md">
                                                    {{ $req->user->hospital === 'roxwood' ? '🏥 Roxwood Hospital' : '🏥 Alta Hospital' }}
                                                </span>
                                                <span class="text-gray-500">•</span>
                                                <span class="text-xs font-medium text-gray-400">
                                                    Diajukan {{ $req->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-auto flex items-start self-start">
                                            <span class="{{ $statusStyle }} border px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center shadow-sm">
                                                <i class="fas {{ $badge['icon'] }} mr-1.5"></i>{{ $badge['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- DateTime Details -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                        <div class="flex items-center gap-3 bg-white/5 p-3.5 rounded-2xl border border-white/10">
                                            <div class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-sky-400">
                                                <i class="fas fa-calendar-day text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tanggal</p>
                                                <p class="font-semibold text-white mt-0.5">{{ $req->requested_date->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 bg-white/5 p-3.5 rounded-2xl border border-white/10">
                                            <div class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-sky-400">
                                                <i class="fas fa-clock text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Waktu & Durasi</p>
                                                <p class="font-semibold text-white mt-0.5">
                                                    {{ Carbon\Carbon::parse($req->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($req->end_time)->format('H:i') }}
                                                    <span class="text-sky-300 font-normal ml-1">({{ $req->getFormattedDuration() }})</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agenda Box -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="md:col-span-2 bg-blue-500/10 rounded-2xl p-5 border border-blue-500/20">
                                            <span class="text-xs font-extrabold text-sky-300 uppercase tracking-widest block mb-2">Agenda / Alasan</span>
                                            <p class="text-gray-200 text-sm leading-relaxed">{{ $req->reason }}</p>
                                        </div>
                                        
                                        @if($req->photo)
                                            <div class="bg-white/5 rounded-2xl p-3 border border-white/10 flex flex-col items-center justify-center gap-2">
                                                <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Bukti Foto</span>
                                                <a href="{{ $req->photo_url }}" target="_blank" class="relative group cursor-zoom-in">
                                                    <img src="{{ $req->photo_url }}" alt="Bukti" class="h-24 w-full object-cover rounded-xl shadow-lg group-hover:opacity-75 transition-opacity">
                                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <i class="fas fa-search-plus text-white text-xl"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        @else
                                            <div class="bg-white/5 rounded-2xl p-3 border border-white/10 flex flex-col items-center justify-center text-center opacity-50">
                                                <i class="fas fa-image text-gray-500 mb-1"></i>
                                                <span class="text-[10px] font-bold text-gray-400">Tidak ada foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($req->review_notes)
                                        <div class="mt-4 text-sm flex items-start gap-3 p-4 bg-rose-500/10 rounded-2xl border border-rose-500/20">
                                            <div class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-rose-200 block mb-0.5">Catatan Admin ({{ $req->reviewer->name ?? 'Sistem' }})</span>
                                                <p class="text-rose-100/80">{{ $req->review_notes }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                @if($req->isPending())
                                    <div class="flex flex-col justify-center gap-3 lg:w-48 lg:border-l border-white/10 lg:pl-8 pt-6 lg:pt-0 border-t lg:border-t-0 mt-6 lg:mt-0">
                                        <button onclick="approveRequest({{ $req->id }})"
                                            class="w-full inline-flex items-center justify-center px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white rounded-xl text-sm font-bold transition-all shadow-lg border border-emerald-500/50">
                                            <i class="fas fa-check mr-2"></i> Setujui
                                        </button>
                                        <button onclick="showRejectModal({{ $req->id }})"
                                            class="w-full inline-flex items-center justify-center px-5 py-3.5 bg-white/10 hover:bg-white/20 text-rose-400 hover:text-rose-300 rounded-xl text-sm font-bold transition-all border border-rose-500/30 hover:border-rose-500/50">
                                            <i class="fas fa-times mr-2"></i> Tolak
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col justify-center gap-3 lg:w-48 lg:border-l border-white/10 lg:pl-8 pt-6 lg:pt-0 border-t lg:border-t-0 mt-6 lg:mt-0">
                                        @if($req->reviewed_at && $req->reviewed_at->diffInMinutes(now()) <= 60)
                                            <button onclick="undoRequest({{ $req->id }})"
                                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-xl text-xs font-bold transition-all border border-amber-500/30 hover:border-amber-500/50">
                                                <i class="fas fa-undo mr-2"></i> Batalkan Aksi
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs text-center italic">Sudah diproses</span>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity">
        <div class="glass-effect rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all border border-white/20">
            <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        Tolak Pengajuan
                    </h3>
                </div>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-white hover:bg-white/10 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-200 mb-3">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea id="rejectReason" rows="4" placeholder="Mohon sertakan alasan yang jelas agar staf dapat memahaminya..."
                        class="w-full border border-white/20 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm resize-none bg-white/10 text-white placeholder-gray-400 transition-colors"
                        required minlength="5"></textarea>
                    <p id="rejectError" class="text-rose-400 text-xs mt-2 hidden font-medium"><i class="fas fa-exclamation-circle mr-1"></i> Alasan penolakan minimal 5 karakter.</p>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button onclick="closeRejectModal()" class="px-5 py-2.5 text-gray-300 bg-white/5 border border-white/10 rounded-xl text-sm font-bold hover:bg-white/10 transition-colors">
                        Batal
                    </button>
                    <button onclick="confirmReject()" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-sm font-bold transition-colors shadow-lg border border-rose-500/50">
                        Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentRejectId = null;

        async function approveRequest(id) {
            const result = await window.confirmAction({
                title: 'Setujui Pengajuan',
                text: 'Apakah Anda yakin ingin menyetujui pengajuan meeting ini?',
                icon: 'question',
                confirmText: 'Ya, Setujui'
            });

            if (!result.isConfirmed) return;

            const card = document.getElementById('request-' + id);
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';

            fetch(`/admin/meeting-requests/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    card.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-check text-3xl"></i>
                            </div>
                            <h4 class="text-emerald-400 font-extrabold text-xl">Berhasil Disetujui!</h4>
                            <p class="text-gray-300 text-sm mt-2">${data.message}</p>
                        </div>
                    `;
                    card.style.opacity = '1';
                    card.classList.replace('border-white/10', 'border-emerald-500/50');
                    card.classList.add('bg-emerald-500/10');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan: ' + err.message);
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            });
        }

        function showRejectModal(id) {
            currentRejectId = id;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectReason').value = '';
            setTimeout(() => document.getElementById('rejectReason').focus(), 100);
            document.getElementById('rejectError').classList.add('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            currentRejectId = null;
        }

        function confirmReject() {
            const reason = document.getElementById('rejectReason').value.trim();
            if (reason.length < 5) {
                document.getElementById('rejectError').classList.remove('hidden');
                document.getElementById('rejectReason').focus();
                return;
            }

            const card = document.getElementById('request-' + currentRejectId);
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
            closeRejectModal();

            fetch(`/admin/meeting-requests/${currentRejectId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ review_notes: reason })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    card.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 bg-rose-500/20 border border-rose-500/30 text-rose-400 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-times text-3xl"></i>
                            </div>
                            <h4 class="text-rose-400 font-extrabold text-xl">Pengajuan Ditolak</h4>
                            <p class="text-gray-300 text-sm mt-2">${data.message}</p>
                        </div>
                    `;
                    card.style.opacity = '1';
                    card.classList.replace('border-white/10', 'border-rose-500/50');
                    card.classList.add('bg-rose-500/10');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan: ' + err.message);
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            });
        }

        async function undoRequest(id) {
            const result = await window.confirmAction({
                title: 'Batalkan Aksi',
                text: 'Yakin ingin membatalkan aksi dan mengembalikan pengajuan ke status Pending?',
                icon: 'info',
                confirmText: 'Ya, Batalkan'
            });

            if (!result.isConfirmed) return;

            const card = document.getElementById('request-' + id);
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';

            fetch(`/admin/meeting-requests/${id}/undo`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(async r => {
                const data = await r.json();
                if (r.ok) {
                    if (typeof showToast === 'function') {
                        showToast('success', 'Berhasil!', data.message || 'Status pengajuan berhasil dikembalikan ke Pending');
                    } else {
                        alert(data.message || 'Berhasil dikembalikan ke Pending');
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                console.error(err);
                location.reload(); 
            });
        }

        // Close modal on outside click
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
        
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('rejectModal').classList.contains('hidden')) {
                closeRejectModal();
            }
        });
    </script>
    @endpush
@endsection