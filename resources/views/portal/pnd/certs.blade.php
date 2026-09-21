@extends('layouts.app')

@section('title', 'PND: Sertifikat Operasi — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-award text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-wide">PND: Sertifikat Operasi</h1>
                        <p class="text-white/50 text-sm mt-0.5">Penerbitan & arsip sertifikat keahlian / kompetensi operasi anggota medis</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openCertModal()"
                        class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-900/30 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Terbitkan Sertifikat
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Permohonan Masuk dari Anggota --}}
        @if(isset($pendingApplications) && $pendingApplications->isNotEmpty())
        <div class="mb-8 bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 shadow-2xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <h2 class="text-base font-bold text-white tracking-wide">Pengajuan Sertifikat Masuk (Menunggu Verifikasi PND)</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    {{ $pendingApplications->count() }} Permohonan
                </span>
            </div>

            <div class="space-y-3">
                @foreach($pendingApplications as $app)
                <div class="bg-gray-900/60 border border-white/10 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-white font-bold">{{ $app->user?->name }}</span>
                            <span class="text-xs text-emerald-400 font-mono">({{ $app->user?->staff_id ?? 'No ID' }})</span>
                            <span class="text-xs text-white/40">• {{ $app->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-sm text-emerald-300 font-semibold mt-1 flex items-center gap-1.5">
                            <i class="fas fa-certificate text-xs"></i> {{ $app->title }}
                        </div>
                        @if($app->notes)
                            <div class="text-xs text-white/60 mt-1 italic bg-white/5 px-2.5 py-1.5 rounded-lg border border-white/5">
                                "{{ $app->notes }}"
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('portal.pnd.cert-application.approve', $app->id) }}" method="POST" onsubmit="return confirm('Setujui pengajuan ini? Sertifikat resmi berstempel dan bertanda tangan digital akan otomatis di-generate untuk anggota ini.')">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-900/30 flex items-center gap-1.5 transition-all">
                                <i class="fas fa-check"></i> Setujui &amp; Terbitkan Foto
                            </button>
                        </form>
                        <form action="{{ route('portal.pnd.cert-application.reject', $app->id) }}" method="POST" onsubmit="return confirm('Tolak permohonan sertifikat ini?')">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            @if($certifications->isEmpty())
            <div class="py-16 text-center text-white/40">
                <i class="fas fa-certificate text-5xl mb-3 block opacity-40"></i>
                <p class="text-base font-medium">Belum ada sertifikat operasi yang diterbitkan.</p>
                <p class="text-xs text-white/30 mt-1">Klik tombol "Terbitkan Sertifikat" di atas untuk menambahkan sertifikat pertama.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/[0.02]">
                        <tr>
                            <th class="text-left px-5 py-3.5">Anggota</th>
                            <th class="text-left px-5 py-3.5">Judul Sertifikat</th>
                            <th class="text-left px-5 py-3.5">No. Sertifikat</th>
                            <th class="text-left px-5 py-3.5">Tanggal Terbit</th>
                            <th class="text-left px-5 py-3.5">Diterbitkan Oleh</th>
                            <th class="text-right px-5 py-3.5">Aksi &amp; Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($certifications as $cert)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4">
                                <div class="text-white font-semibold">{{ $cert->user?->name ?? 'N/A' }}</div>
                                <div class="text-xs text-emerald-400 font-mono">{{ $cert->user?->staff_id ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-white font-medium flex items-center gap-2">
                                    <i class="fas fa-file-contract text-emerald-400 text-xs"></i>
                                    {{ $cert->title }}
                                </div>
                                @if($cert->notes)
                                    <div class="text-xs text-white/40 mt-0.5 max-w-xs truncate" title="{{ $cert->notes }}">
                                        {{ $cert->notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs font-mono text-white/80">
                                {{ $cert->certificate_number ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-xs text-white/70">
                                {{ $cert->issue_date ? \Carbon\Carbon::parse($cert->issue_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-xs text-white/60">
                                {{ $cert->issuedBy?->name ?? 'Sistem' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($cert->file_path)
                                        <a href="{{ route('portal.cert.image', $cert->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-semibold transition-all">
                                            <i class="fas fa-eye text-[10px]"></i> Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-xs text-white/30 italic mr-1">Tanpa berkas</span>
                                    @endif

                                    <form action="{{ route('portal.pnd.cert-destroy', $cert->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus sertifikat operasi {{ addslashes($cert->title) }} milik {{ addslashes($cert->user?->name) }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 rounded-lg text-xs font-semibold transition-all" title="Hapus Sertifikat">
                                            <i class="fas fa-trash-alt text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($certifications->hasPages())
            <div class="p-4 border-t border-white/10 bg-white/[0.02]">
                {{ $certifications->links() }}
            </div>
            @endif
            @endif
        </div>

    </div>
</div>

{{-- Modal Terbitkan Sertifikat --}}
<div id="certModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm hidden">
    <div class="bg-gray-900 border border-white/15 rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-award text-emerald-400"></i> Terbitkan Sertifikat Operasi
            </h3>
            <button type="button" onclick="closeCertModal()" class="text-white/40 hover:text-white text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('portal.pnd.cert-store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-white/70 uppercase mb-1">Pilih Anggota Medis <span class="text-rose-400">*</span></label>
                <select name="user_id" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                    <option value="" class="bg-gray-800 text-white">-- Pilih Anggota --</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id }}" class="bg-gray-800 text-white">
                            {{ $staff->name }} ({{ $staff->staff_id ?? 'No ID' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/70 uppercase mb-1">Nama / Judul Sertifikat <span class="text-rose-400">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Sertifikat Asistensi Bedah Mayor / Mandiri"
                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-1">Nomor Sertifikat</label>
                    <input type="text" name="certificate_number" placeholder="PND/OP/2026/..."
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-1">Tanggal Terbit <span class="text-rose-400">*</span></label>
                    <input type="date" name="issue_date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400">
                </div>
            </div>

            <div class="p-3 bg-emerald-500/10 border border-emerald-500/25 rounded-xl text-emerald-200 text-xs flex items-start gap-2.5">
                <i class="fas fa-magic text-emerald-400 mt-0.5 shrink-0"></i>
                <div class="leading-relaxed text-[11px]">
                    <strong class="text-white">Cetak Foto Otomatis:</strong> Berkas foto sertifikat beresolusi tinggi akan otomatis di-generate oleh sistem menggunakan template resmi Alta Hospital (dilengkapi Nomor Registrasi, Cap Stempel Resmi &amp; Tanda Tangan Digital) dan langsung tampil di profil anggota.
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/70 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan atau rincian kualifikasi bedah/operasi..."
                          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-400"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                <button type="button" onclick="closeCertModal()" class="px-4 py-2 bg-white/10 text-white/70 hover:text-white rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-emerald-900/30 flex items-center gap-1.5">
                    <i class="fas fa-magic text-xs"></i> Terbitkan &amp; Generate Foto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCertModal() {
    document.getElementById('certModal').classList.remove('hidden');
}
function closeCertModal() {
    document.getElementById('certModal').classList.add('hidden');
}
</script>
@endsection
