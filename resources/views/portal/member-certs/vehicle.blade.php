@extends('layouts.app')

@section('title', 'Pengajuan Sertifikat Kendaraan — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-lg shadow-amber-900/20">
                    <i class="fas fa-car-side text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">Pengajuan Sertifikat Kendaraan</h1>
                    <p class="text-white/60 text-sm mt-0.5">Permohonan sertifikasi kualifikasi operasional armada darat &amp; helikopter medis ke Divisi GA</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('formSection').scrollIntoView({behavior: 'smooth'})"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-900/30 transition-all">
                <i class="fas fa-paper-plane"></i> Buat Pengajuan Baru
            </button>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-3 shadow-lg">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm flex items-center gap-3 shadow-lg">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/40 rounded-xl text-rose-300 text-sm shadow-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Sertifikat yang Sudah Terbit --}}
        @php $myCerts = $myCertifications ?? $certificates ?? collect(); @endphp
        @if($myCerts->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                <i class="fas fa-award text-amber-400"></i> Sertifikat Kendaraan Aktif Saya
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($myCerts as $cert)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 hover:border-amber-500/40 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                {{ $cert->type === 'vehicle_land' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                <i class="fas {{ $cert->type === 'vehicle_land' ? 'fa-ambulance' : 'fa-helicopter' }}"></i>
                                {{ $cert->type === 'vehicle_land' ? 'Kendaraan Darat' : 'Helikopter Medis' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase
                                {{ $cert->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                {{ $cert->status === 'active' ? 'Resmi Aktif' : 'Dicabut' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white mb-1">{{ $cert->title }}</h3>
                        <p class="text-xs text-white/50 font-mono mb-2">No: {{ $cert->certificate_number ?? '-' }}</p>
                        <p class="text-xs text-white/60 mb-4">Diterbitkan: {{ $cert->issue_date ? \Carbon\Carbon::parse($cert->issue_date)->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                        <span class="text-xs text-white/40">Diterbitkan oleh GA</span>
                        <a href="{{ route('portal.cert.image', $cert->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-semibold transition-all">
                            <i class="fas fa-eye text-xs"></i> Pratinjau Sertifikat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Form Pengajuan Baru --}}
        <div id="formSection" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl mb-8">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-file-signature text-amber-400"></i> Formulir Pengajuan Sertifikat Kendaraan
            </h2>

            <form action="{{ route('portal.vehicle-cert.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Pilih Template Sertifikat Kendaraan <span class="text-rose-400">*</span></label>
                    <select id="vehicleTemplateSelect" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400">
                        <option value="Sertifikasi Izin Mengemudi Ambulans Medis" data-type="vehicle_land" data-cat="Ambulans Medis Operasional" class="bg-gray-800 text-white" selected>
                            🚑 Ambulans Medis Operasional (Izin Mengemudi Ambulans Cepat)
                        </option>
                        <option value="Sertifikasi Pengemudi Kendaraan Taktis & Rescue Medis" data-type="vehicle_land" data-cat="Kendaraan Taktis & SUV Darat" class="bg-gray-800 text-white">
                            🚙 Kendaraan Taktis &amp; Rescue Medis (SUV Operasional)
                        </option>
                        <option value="Sertifikasi Penerbang Helikopter Medis & Air Ambulance" data-type="vehicle_heli" data-cat="Helikopter & Evakuasi Udara" class="bg-gray-800 text-white">
                            🚁 Helikopter Medis &amp; Air Ambulance (Pilot Rescue EMS)
                        </option>
                        <option value="Sertifikasi Pengemudi Operasional Staf Medis" data-type="vehicle_land" data-cat="Mobil Dinas & Operasional Staf" class="bg-gray-800 text-white">
                            🚗 Transportasi Operasional Staf Medis (Mobil Dinas)
                        </option>
                    </select>
                    {{-- Hidden inputs to store type and title automatically --}}
                    <input type="hidden" name="type" id="vehicleTypeInput" value="vehicle_land">
                    <input type="hidden" name="title" id="vehicleTitleInput" value="Sertifikasi Izin Mengemudi Ambulans Medis">
                </div>

                {{-- Live Selected Template Preview --}}
                <div class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-amber-300 tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-certificate text-amber-400"></i> Judul Sertifikat Otomatis (Template Terpilih)
                        </div>
                        <div id="vehicleTitlePreview" class="text-white font-bold text-sm mt-0.5">
                            Sertifikasi Izin Mengemudi Ambulans Medis
                        </div>
                        <div id="vehicleCategoryPreview" class="text-xs text-slate-300 mt-0.5">
                            <i class="fas fa-tag mr-1 text-amber-400"></i> Kategori: Ambulans Medis Operasional (Divisi GA)
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/20 text-amber-300 text-xs font-semibold rounded-lg border border-amber-500/30 whitespace-nowrap self-start sm:self-center">
                        <i class="fas fa-magic text-xs text-amber-300"></i> Otomatis
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Alasan / Catatan Pengalaman / Keterangan Tambahan</label>
                    <textarea name="notes" rows="3" placeholder="Jelaskan pelatihan/tes drive yang telah dilalui atau pengalaman terkait kendaraan dinas..."
                              class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400"></textarea>
                </div>

                <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-200 text-xs flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-amber-400 mt-0.5 shrink-0"></i>
                    <span>Setelah diajukan, Divisi GA akan meninjau kualifikasi Anda. Jika disetujui, sertifikat resmi berstempel dan bertanda tangan digital akan otomatis di-generate dan tersimpan di profil Anda.</span>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-900/30 transition-all flex items-center gap-2">
                        <i class="fas fa-paper-plane text-xs"></i> Kirim Pengajuan ke GA
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Riwayat Pengajuan --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-5 border-b border-white/10">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-history text-white/60"></i> Riwayat Status Pengajuan Sertifikat Kendaraan
                </h2>
            </div>

            @if($applications->isEmpty())
            <div class="py-12 text-center text-white/40">
                <i class="fas fa-inbox text-4xl mb-2 opacity-50 block"></i>
                <p class="text-sm">Belum ada riwayat permohonan sertifikat kendaraan.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider bg-white/[0.02]">
                        <tr>
                            <th class="text-left px-5 py-3.5">Tanggal Ajuan</th>
                            <th class="text-left px-5 py-3.5">Tipe</th>
                            <th class="text-left px-5 py-3.5">Kualifikasi</th>
                            <th class="text-left px-5 py-3.5">Status Verifikasi</th>
                            <th class="text-left px-5 py-3.5">Catatan GA</th>
                            <th class="text-right px-5 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($applications as $app)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5 text-xs text-white/70">{{ $app->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $app->type === 'vehicle_land' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30' }}">
                                    <i class="fas {{ $app->type === 'vehicle_land' ? 'fa-ambulance' : 'fa-helicopter' }} text-[10px]"></i>
                                    {{ $app->type === 'vehicle_land' ? 'Darat' : 'Heli' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-white font-medium">{{ $app->title }}</div>
                                @if($app->notes)
                                    <div class="text-xs text-white/40 truncate max-w-xs" title="{{ $app->notes }}">{{ $app->notes }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($app->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fas fa-clock text-[10px]"></i> Menunggu GA
                                    </span>
                                @elseif($app->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fas fa-check-circle text-[10px]"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        <i class="fas fa-times-circle text-[10px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-white/60">
                                {{ $app->admin_notes ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($app->status === 'pending')
                                <form action="{{ route('portal.vehicle-cert.cancel', $app->id) }}" method="POST" onsubmit="return confirm('Batalkan permohonan ini?')" class="inline">
                                    @csrf
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 text-xs font-semibold underline underline-offset-2">
                                        Batalkan
                                    </button>
                                </form>
                                @elseif($app->status === 'approved' && $app->certification_id)
                                    <a href="{{ route('portal.cert.image', $app->certification_id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-semibold transition-all">
                                        <i class="fas fa-eye text-[10px]"></i> Sertifikat
                                    </a>
                                @else
                                    <span class="text-xs text-white/30">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('vehicleTemplateSelect');
    var typeInput = document.getElementById('vehicleTypeInput');
    var titleInput = document.getElementById('vehicleTitleInput');
    var titlePreview = document.getElementById('vehicleTitlePreview');
    var categoryPreview = document.getElementById('vehicleCategoryPreview');

    if (select) {
        select.addEventListener('change', function () {
            var opt = select.options[select.selectedIndex];
            var title = opt.value;
            var type = opt.getAttribute('data-type') || 'vehicle_land';
            var cat = opt.getAttribute('data-cat') || 'Kendaraan Operasional';

            if (typeInput) typeInput.value = type;
            if (titleInput) titleInput.value = title;
            if (titlePreview) titlePreview.textContent = title;
            if (categoryPreview) categoryPreview.innerHTML = '<i class="fas fa-tag mr-1 text-amber-400"></i> Kategori: ' + cat + ' (Divisi GA)';
        });
    }
});
</script>
@endsection
