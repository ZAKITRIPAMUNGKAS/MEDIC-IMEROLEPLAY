@extends('layouts.app')

@section('title', 'Edit Sesi Voting - Portal Medis iMe Roleplay')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.voting.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Manajemen Voting
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Edit Sesi Voting: {{ $voting->title }}
                </h1>
            </div>
        </div>

        <!-- Alert Notification -->
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-rose-400 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.voting.update', $voting->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Form Card 1: Informasi Sesi Voting -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl">
                <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fas fa-edit text-indigo-400"></i> Edit Informasi Voting
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul Voting -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Judul Voting / Pemilihan <span class="text-rose-400">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $voting->title) }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Posisi Tujuan -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Posisi / Jabatan yang Dipilih</label>
                        <input type="text" name="target_position" value="{{ old('target_position', $voting->target_position) }}"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Kategori Rumah Sakit (Alta vs Roxwood) -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Target Rumah Sakit / Divisi <span class="text-rose-400">*</span></label>
                        <select name="hospital" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="roxwood" {{ old('hospital', $voting->hospital) === 'roxwood' ? 'selected' : '' }}>🏥 Roxwood Hospital</option>
                            <option value="alta" {{ old('hospital', $voting->hospital) === 'alta' ? 'selected' : '' }}>🏥 Alta Hospital</option>
                            <option value="all" {{ old('hospital', $voting->hospital) === 'all' ? 'selected' : '' }}>🌐 Terbuka Untuk Semua Staf</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Status Sesi <span class="text-rose-400">*</span></label>
                        <select name="status" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $voting->status) === 'active' ? 'selected' : '' }}>▶ Aktif (Sedang Berlangsung)</option>
                            <option value="draft" {{ old('status', $voting->status) === 'draft' ? 'selected' : '' }}>📝 Draft</option>
                            <option value="closed" {{ old('status', $voting->status) === 'closed' ? 'selected' : '' }}>🔒 Ditutup</option>
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Deskripsi / Keterangan Tambahan</label>
                        <textarea name="description" rows="3"
                                  class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $voting->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Card 2: Kandidat Pemilihan -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-users text-indigo-400"></i> Daftar Kandidat
                        </h2>
                    </div>
                    <button type="button" id="add-candidate-btn" class="px-3 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 text-xs font-bold border border-indigo-500/30 transition-all flex items-center gap-1">
                        <i class="fas fa-plus"></i> Tambah Kandidat
                    </button>
                </div>

                <div id="candidates-container" class="space-y-6">
                    @foreach($voting->candidates as $index => $candidate)
                        <div class="candidate-row p-5 rounded-xl bg-slate-950 border border-slate-800 relative space-y-4">
                            <div class="flex items-center justify-between text-xs font-bold text-indigo-400 border-b border-slate-800/80 pb-2">
                                <span>Kandidat #{{ $index + 1 }}</span>
                                @if($index >= 2)
                                    <button type="button" class="remove-candidate-btn text-rose-400 hover:text-rose-300 font-normal">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pilih Dari Akun Terdaftar</label>
                                    <select name="candidates[{{ $index }}][user_id]" class="user-select-dropdown w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- Pilih Akun Staf (Opsional) --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $candidate->user_id == $user->id ? 'selected' : '' }} data-name="{{ $user->name }}" data-role="{{ $user->role->display_name ?? 'Staf' }}">
                                                {{ $user->name }} ({{ $user->role->display_name ?? 'Staf' }} - {{ ucfirst($user->hospital ?? 'All') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Nama Tampilan Kandidat <span class="text-rose-400">*</span></label>
                                    <input type="text" name="candidates[{{ $index }}][name]" value="{{ old("candidates.$index.name", $candidate->name) }}" required
                                           class="candidate-name-input w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Catatan / Jabatan Saat Ini</label>
                                    <input type="text" name="candidates[{{ $index }}][custom_role]" value="{{ old("candidates.$index.custom_role", $candidate->custom_role) }}"
                                           class="candidate-role-input w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Visi & Misi / Program Kerja</label>
                                    <textarea name="candidates[{{ $index }}][vision_mission]" rows="3"
                                              class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old("candidates.$index.vision_mission", $candidate->vision_mission) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.voting.index') }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm shadow-lg shadow-indigo-900/30 transition-all">
                    <i class="fas fa-save mr-1.5"></i> Perbarui Sesi Voting
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let candidateIndex = {{ count($voting->candidates) }};

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('user-select-dropdown')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('.candidate-row');
                const nameInput = row.querySelector('.candidate-name-input');
                const roleInput = row.querySelector('.candidate-role-input');

                if (selectedOption && selectedOption.dataset.name) {
                    nameInput.value = selectedOption.dataset.name;
                    roleInput.value = selectedOption.dataset.role || '';
                }
            }
        });

        document.getElementById('add-candidate-btn').addEventListener('click', function() {
            const container = document.getElementById('candidates-container');
            const newRow = document.createElement('div');
            newRow.className = 'candidate-row p-5 rounded-xl bg-slate-950 border border-slate-800 relative space-y-4';
            
            let userOptionsHtml = '<option value="">-- Pilih Akun Staf (Opsional) --</option>';
            @foreach($users as $user)
                userOptionsHtml += `<option value="{{ $user->id }}" data-name="{{ addslashes($user->name) }}" data-role="{{ addslashes($user->role->display_name ?? 'Staf') }}">{{ addslashes($user->name) }} ({{ addslashes($user->role->display_name ?? 'Staf') }} - {{ ucfirst($user->hospital ?? 'All') }})</option>`;
            @endforeach

            newRow.innerHTML = `
                <div class="flex items-center justify-between text-xs font-bold text-indigo-400 border-b border-slate-800/80 pb-2">
                    <span>Kandidat #${candidateIndex + 1}</span>
                    <button type="button" class="remove-candidate-btn text-rose-400 hover:text-rose-300 font-normal">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pilih Dari Akun Terdaftar</label>
                        <select name="candidates[${candidateIndex}][user_id]" class="user-select-dropdown w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            ${userOptionsHtml}
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Nama Tampilan Kandidat <span class="text-rose-400">*</span></label>
                        <input type="text" name="candidates[${candidateIndex}][name]" required placeholder="Nama Lengkap / Gelar Staf"
                               class="candidate-name-input w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Catatan / Jabatan Saat Ini</label>
                        <input type="text" name="candidates[${candidateIndex}][custom_role]" placeholder="Contoh: Dokter Spesialis Bedah - Roxwood"
                               class="candidate-role-input w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Visi & Misi / Program Kerja</label>
                        <textarea name="candidates[${candidateIndex}][vision_mission]" rows="3" placeholder="Tuliskan visi misi singkat kandidat..."
                                  class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
            candidateIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('remove-candidate-btn') || e.target.closest('.remove-candidate-btn'))) {
                const btn = e.target.classList.contains('remove-candidate-btn') ? e.target : e.target.closest('.remove-candidate-btn');
                const row = btn.closest('.candidate-row');
                const allRows = document.querySelectorAll('.candidate-row');
                if (allRows.length > 2) {
                    row.remove();
                } else {
                    alert('Sesi voting minimal membutuhkan 2 kandidat.');
                }
            }
        });
    });
</script>
@endpush
@endsection
