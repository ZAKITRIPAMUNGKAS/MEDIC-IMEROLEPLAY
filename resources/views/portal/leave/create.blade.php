@extends('layouts.app')

@section('title', 'Pengajuan Cuti Baru — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('portal.leave.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-calendar-plus text-rose-400"></i> Formulir Pengajuan Cuti
            </h1>
            <p class="text-white/50 text-sm mt-1">Isi formulir di bawah untuk mengajukan permohonan cuti (Maksimal 30 hari).</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
            <ul class="space-y-1 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl space-y-5">

            {{-- Info Pemohon --}}
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-white/10">
                <div>
                    <label class="block text-xs text-white/50 uppercase tracking-wider mb-1">Nama Pemohon</label>
                    <p class="text-white font-semibold">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-xs text-white/50 uppercase tracking-wider mb-1">Jabatan</label>
                    <p class="text-white/80">{{ $position }}</p>
                </div>
                <div>
                    <label class="block text-xs text-white/50 uppercase tracking-wider mb-1">ID Staf</label>
                    <p class="text-white/80">{{ $user->staff_id ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs text-white/50 uppercase tracking-wider mb-1">Tanggal Surat</label>
                    <p class="text-white/80">{{ $letterDate }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('portal.leave.store') }}" id="leaveForm" class="space-y-5">
                @csrf

                {{-- Periode Cuti --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm text-white/70 font-medium mb-1.5">Tanggal Mulai Cuti <span class="text-rose-400">*</span></label>
                        <input type="date" id="start_date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" required
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition-all">
                        <p class="text-[11px] text-white/40 mt-1">Tidak dapat dimulai sebelum hari ini.</p>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm text-white/70 font-medium mb-1.5">Tanggal Selesai Cuti <span class="text-rose-400">*</span></label>
                        <input type="date" id="end_date" name="end_date" min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" required
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition-all">
                        <p class="text-[11px] text-amber-300/80 mt-1"><i class="fas fa-info-circle mr-1"></i>Batas maksimal cuti: 30 hari.</p>
                    </div>
                </div>

                {{-- Live Duration Info & Warning Box --}}
                <div id="durationInfoBox" class="hidden p-3.5 rounded-xl border transition-all text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 font-medium" id="durationText">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Durasi cuti: <strong id="durationDays" class="text-white">0</strong> hari</span>
                        </span>
                        <span id="durationStatusBadge" class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase"></span>
                    </div>
                    <p id="durationWarningMsg" class="hidden mt-2 text-rose-300 text-[11px] font-semibold flex items-center gap-1.5">
                        <i class="fas fa-exclamation-triangle text-rose-400"></i>
                        <span>Cuti lebih dari 30 hari tidak diizinkan. Tombol kirim pengajuan dinonaktifkan.</span>
                    </p>
                </div>

                {{-- Alasan IC --}}
                <div>
                    <label for="reason_ic" class="block text-sm text-white/70 font-medium mb-1.5">
                        Alasan Cuti (In-Character / IC) <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="reason_ic" name="reason_ic" rows="3" required maxlength="1000"
                              placeholder="Alasan cuti dalam konteks roleplay (IC)..."
                              class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition-all resize-none">{{ old('reason_ic') }}</textarea>
                </div>

                {{-- Alasan OOC --}}
                <div>
                    <label for="reason_ooc" class="block text-sm text-white/70 font-medium mb-1.5">
                        Alasan Cuti (Out-of-Character / OOC) <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="reason_ooc" name="reason_ooc" rows="3" required maxlength="1000"
                              placeholder="Alasan sebenarnya di luar konteks roleplay (OOC)..."
                              class="w-full px-3 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition-all resize-none">{{ old('reason_ooc') }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="pt-2 flex gap-3">
                    <button type="submit" id="btnSubmitLeave"
                            class="flex-1 py-3 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-400 hover:to-pink-500 text-white font-semibold rounded-xl shadow-lg shadow-rose-900/30 transition-all duration-200 text-sm flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none">
                        <i class="fas fa-paper-plane mr-1.5"></i> <span id="submitBtnText">Kirim Pengajuan Cuti</span>
                    </button>
                    <a href="{{ route('portal.leave.index') }}"
                       class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const btnSubmit = document.getElementById('btnSubmitLeave');
    const leaveForm = document.getElementById('leaveForm');
    const durationBox = document.getElementById('durationInfoBox');
    const durationDaysEl = document.getElementById('durationDays');
    const durationStatusBadge = document.getElementById('durationStatusBadge');
    const durationWarningMsg = document.getElementById('durationWarningMsg');

    function calculateDuration() {
        const startVal = startDateInput.value;
        const endVal = endDateInput.value;

        if (!startVal) {
            endDateInput.min = '{{ date("Y-m-d") }}';
            endDateInput.removeAttribute('max');
            durationBox.classList.add('hidden');
            btnSubmit.disabled = false;
            return;
        }

        const startDate = new Date(startVal + 'T00:00:00');
        endDateInput.min = startVal;

        // Hitung batas maksimal 30 hari (start + 29 hari)
        const maxDate = new Date(startDate);
        maxDate.setDate(maxDate.getDate() + 29);
        const maxYear = maxDate.getFullYear();
        const maxMonth = String(maxDate.getMonth() + 1).padStart(2, '0');
        const maxDay = String(maxDate.getDate()).padStart(2, '0');
        const maxIso = `${maxYear}-${maxMonth}-${maxDay}`;
        endDateInput.max = maxIso;

        if (!endVal) {
            durationBox.classList.add('hidden');
            btnSubmit.disabled = false;
            return;
        }

        const endDate = new Date(endVal + 'T00:00:00');
        const diffTime = endDate - startDate;
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

        durationBox.classList.remove('hidden');

        if (diffDays < 1) {
            durationDaysEl.textContent = 'Tidak valid';
            durationBox.className = 'p-3.5 rounded-xl border transition-all text-xs bg-red-500/20 border-red-500/40 text-red-300';
            durationStatusBadge.className = 'px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase bg-red-500/30 text-red-200';
            durationStatusBadge.textContent = 'Tanggal Tidak Valid';
            durationWarningMsg.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Tanggal selesai harus sama atau setelah tanggal mulai.';
            durationWarningMsg.classList.remove('hidden');
            btnSubmit.disabled = true;
        } else if (diffDays > 30) {
            durationDaysEl.textContent = diffDays;
            durationBox.className = 'p-3.5 rounded-xl border transition-all text-xs bg-rose-500/20 border-rose-500/40 text-rose-200';
            durationStatusBadge.className = 'px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase bg-rose-600 text-white';
            durationStatusBadge.textContent = 'Melebihi Batas';
            durationWarningMsg.innerHTML = '<i class="fas fa-ban mr-1 text-rose-400"></i> Durasi ' + diffDays + ' hari melebihi batas maksimal 30 hari. Pengajuan tidak dapat dikirim.';
            durationWarningMsg.classList.remove('hidden');
            btnSubmit.disabled = true;
        } else {
            durationDaysEl.textContent = diffDays;
            durationBox.className = 'p-3.5 rounded-xl border transition-all text-xs bg-emerald-500/10 border-emerald-500/30 text-emerald-200';
            durationStatusBadge.className = 'px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase bg-emerald-500/30 text-emerald-300';
            durationStatusBadge.textContent = 'Memenuhi Syarat (Maks 30 Hari)';
            durationWarningMsg.classList.add('hidden');
            btnSubmit.disabled = false;
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
    startDateInput.addEventListener('input', calculateDuration);
    endDateInput.addEventListener('input', calculateDuration);

    leaveForm.addEventListener('submit', function (e) {
        const startVal = startDateInput.value;
        const endVal = endDateInput.value;
        if (!startVal || !endVal) return;

        const startDate = new Date(startVal + 'T00:00:00');
        const endDate = new Date(endVal + 'T00:00:00');
        const diffDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays > 30) {
            e.preventDefault();
            alert('Pengajuan cuti tidak dapat dikirim karena durasi (' + diffDays + ' hari) melebihi batas maksimal 30 hari.');
        } else if (diffDays < 1) {
            e.preventDefault();
            alert('Tanggal selesai cuti harus sama atau setelah tanggal mulai cuti.');
        }
    });

    calculateDuration();
});
</script>
@endsection
