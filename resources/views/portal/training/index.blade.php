@extends('layouts.app')

@section('title', 'Pendaftaran Pelatihan — IME Medical Center')

@section('content')
<div class="min-h-screen pt-20 pb-12" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs text-white/50 mb-4">
            <a href="{{ route('public.index') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-white/80">Portal Medis</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-emerald-400 font-medium">Pendaftaran Pelatihan</span>
        </div>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-wide">Pendaftaran Pelatihan Medis</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Divisi PND
                            </span>
                        </div>
                        <p class="text-white/60 text-sm mt-1">Program pelatihan resmi peningkatan kompetensi staf medis IME Medical Center yang dikelola oleh Divisi People & Development</p>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('pnd'))
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.pnd.training.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-semibold shadow-lg shadow-emerald-600/30 transition-all">
                    <i class="fas fa-tasks-alt text-base"></i>
                    <span>Kelola Pendaftaran (PND)</span>
                </a>
            </div>
            @endif
        </div>

        {{-- Alerts --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-2xl text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/20">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-sm flex items-center gap-3 shadow-lg shadow-rose-950/20">
            <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-triangle text-rose-400 text-base"></i>
            </div>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Program Pelatihan Cards --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-emerald-400"></i>
                    <span>Program Pelatihan Tersedia</span>
                </h2>
                <span class="text-xs text-white/50">Pilih formulir pelatihan sesuai kualifikasi</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($trainings as $t)
                <div class="group relative rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] border border-white/10 hover:border-white/20 p-6 flex flex-col justify-between transition-all duration-300 backdrop-blur-sm hover:-translate-y-1 hover:shadow-xl hover:shadow-black/40">
                    <div>
                        {{-- Badges & Icon --}}
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl 
                                @if($t['key'] === 'operasi') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 
                                @elseif($t['key'] === 'surat-menyurat') bg-blue-500/20 text-blue-400 border border-blue-500/30 
                                @elseif($t['key'] === 'rekam-medis') bg-cyan-500/20 text-cyan-400 border border-cyan-500/30
                                @elseif($t['key'] === 'pemulsaran-jenazah') bg-amber-500/20 text-amber-400 border border-amber-500/30
                                @else bg-purple-500/20 text-purple-400 border border-purple-500/30 @endif
                                flex items-center justify-center text-xl shadow-inner">
                                <i class="fas {{ $t['icon'] }}"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($canManagePrograms)
                                <button type="button" onclick="openEditProgramModal({{ json_encode($t) }})"
                                        class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[10px] font-bold flex items-center gap-1 transition-all shadow-sm"
                                        title="Ubah Jadwal & Tanggal Pelatihan">
                                    <i class="fas fa-calendar-alt text-amber-400"></i> Edit Jadwal
                                </button>
                                @endif
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($t['key'] === 'operasi') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                    @elseif($t['key'] === 'surat-menyurat') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                    @elseif($t['key'] === 'rekam-medis') bg-cyan-500/20 text-cyan-300 border border-cyan-500/30
                                    @elseif($t['key'] === 'pemulsaran-jenazah') bg-amber-500/20 text-amber-300 border border-amber-500/30
                                    @else bg-purple-500/20 text-purple-300 border border-purple-500/30 @endif">
                                    {{ $t['badge'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Title & Organizer --}}
                        <h3 class="text-lg font-bold text-white group-hover:text-emerald-300 transition-colors line-clamp-2 mb-1">
                            {{ $t['title'] }}
                        </h3>
                        <p class="text-[11px] font-medium text-white/40 mb-3 flex items-center gap-1.5">
                            <i class="fas fa-building text-[10px]"></i>
                            <span>{{ $t['organizer'] }}</span>
                        </p>

                        {{-- Description --}}
                        <p class="text-xs text-white/70 leading-relaxed line-clamp-4 mb-4">
                            {{ $t['desc'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-white/10 mt-auto">
                        @if(!isset($t['is_active']) || $t['is_active'])
                        <a href="{{ $t['route'] }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs tracking-wide transition-all shadow-md
                            @if($t['key'] === 'operasi') bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/20
                            @elseif($t['key'] === 'surat-menyurat') bg-blue-600 hover:bg-blue-500 text-white shadow-blue-600/20
                            @elseif($t['key'] === 'rekam-medis') bg-cyan-600 hover:bg-cyan-500 text-white shadow-cyan-600/20
                            @elseif($t['key'] === 'pemulsaran-jenazah') bg-amber-600 hover:bg-amber-500 text-white shadow-amber-600/20
                            @else bg-purple-600 hover:bg-purple-500 text-white shadow-purple-600/20 @endif">
                            <i class="fas fa-edit"></i>
                            <span>Isi Formulir Pendaftaran</span>
                            <i class="fas fa-arrow-right text-[10px] ml-1 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        @else
                        <div class="w-full py-2.5 px-4 rounded-xl bg-white/5 border border-white/10 text-center text-xs font-semibold text-slate-400">
                            <i class="fas fa-lock mr-1.5 text-amber-400"></i> Pendaftaran Sedang Ditutup
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Riwayat Pendaftaran Pelatihan Saya --}}
        <div class="rounded-2xl bg-white/[0.03] border border-white/10 p-6 backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-white/10">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-history text-emerald-400"></i>
                        <span>Riwayat Pendaftaran Pelatihan Saya</span>
                    </h2>
                    <p class="text-xs text-white/50 mt-0.5">Status verifikasi dan catatan dari Divisi PND terkait formulir yang telah Anda kirimkan</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-white/5 border border-white/10 text-white/60 font-medium">
                    Total: {{ $myApplications->total() }} Pengajuan
                </span>
            </div>

            @if($myApplications->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto text-white/30 text-2xl mb-3">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-base font-bold text-white/80">Belum Ada Pendaftaran Pelatihan</h3>
                <p class="text-xs text-white/40 mt-1 max-w-sm mx-auto">Anda belum pernah mengirimkan formulir pendaftaran pelatihan. Pilih salah satu pelatihan di atas untuk mendaftar.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-white/80">
                    <thead class="text-[11px] uppercase tracking-wider text-white/40 bg-white/[0.02] border-b border-white/10">
                        <tr>
                            <th class="py-3 px-4">Pelatihan</th>
                            <th class="py-3 px-4">Nama IC</th>
                            <th class="py-3 px-4">Gender</th>
                            <th class="py-3 px-4">Jabatan / Kontak</th>
                            <th class="py-3 px-4">Batch</th>
                            <th class="py-3 px-4">Tanggal Daftar</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Catatan PND</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($myApplications as $app)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                <span class="inline-flex items-center gap-1.5">
                                    @if($app->training_type === 'operasi')
                                        <i class="fas fa-procedures text-emerald-400"></i>
                                    @elseif($app->training_type === 'surat_menyurat')
                                        <i class="fas fa-envelope-open-text text-blue-400"></i>
                                    @elseif($app->training_type === 'rekam_medis')
                                        <i class="fas fa-file-medical-alt text-cyan-400"></i>
                                    @elseif($app->training_type === 'pemulsaran_jenazah')
                                        <i class="fas fa-ribbon text-amber-400"></i>
                                    @else
                                        <i class="fas fa-notes-medical text-purple-400"></i>
                                    @endif
                                    <span>{{ $app->type_label }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-white font-medium">
                                {{ $app->nama_ic }}
                            </td>
                            <td class="py-3.5 px-4 text-white/70 text-xs">
                                {{ $app->gender }}
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($app->jabatan)
                                    <div class="font-medium text-white/90">{{ $app->jabatan }}</div>
                                @endif
                                @if($app->phone_ic)
                                    <div class="text-white/50 font-mono text-[11px]">{{ $app->phone_ic }}</div>
                                @elseif(!$app->jabatan)
                                    <span class="text-white/30">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-white/10 border border-white/15 text-white/90">
                                    {{ $app->batch }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-white/60">
                                {{ $app->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="py-3.5 px-4">
                                {!! $app->status_badge !!}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-white/70 max-w-xs">
                                @if($app->admin_notes)
                                    <span class="italic text-white/90">"{{ $app->admin_notes }}"</span>
                                    @if($app->reviewer)
                                        <span class="block text-[10px] text-white/40 mt-0.5">— {{ $app->reviewer->name }}</span>
                                    @endif
                                @else
                                    <span class="text-white/30 italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-4 border-t border-white/5">
                {{ $myApplications->links() }}
            </div>
            @endif
        </div>

        {{-- MODAL EDIT JADWAL & PROGRAM PELATIHAN (PND / ADMIN) --}}
        @if($canManagePrograms)
        <div id="editProgramModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md hidden animate-fade-in">
            <div class="relative w-full max-w-2xl bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-white/10 bg-gradient-to-r from-amber-900/40 via-yellow-900/20 to-slate-900 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-lg">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                Edit Jadwal &amp; Info Pelatihan
                            </h3>
                            <p class="text-xs text-amber-200/70" id="modalProgramSubTitle">Sesuaikan tanggal pelaksanaan dan deskripsi kegiatan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEditProgramModal()" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form Edit --}}
                <form id="editProgramForm" method="POST" action="" class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                    @csrf

                    <div>
                        <label class="block text-slate-200 font-bold mb-1.5 uppercase tracking-wider">
                            Judul Formulir / Program Pelatihan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="title" id="formProgramTitle" required
                               class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-200 font-bold mb-1.5 uppercase tracking-wider">
                                Divisi Penyelenggara <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="organizer" id="formProgramOrganizer" required
                                   class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="block text-slate-200 font-bold mb-1.5 uppercase tracking-wider">
                                Badge / Kategori Tag
                            </label>
                            <input type="text" name="badge" id="formProgramBadge"
                                   placeholder="Contoh: PND - MOT"
                                   class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-200 font-bold mb-1.5 uppercase tracking-wider">
                            Jadwal &amp; Deskripsi Pelatihan (Tanggal Kegiatan) <span class="text-rose-400">*</span>
                        </label>
                        <p class="text-[11px] text-amber-300/80 mb-1.5">
                            *Ubah tanggal pendaftaran, tanggal pelaksanaan, atau keterangan gelombang/fase di kolom ini:
                        </p>
                        <textarea name="desc" id="formProgramDesc" rows="4" required
                                  class="w-full bg-slate-800 text-white border border-white/20 rounded-xl p-3 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400 leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-200 font-bold mb-1.5 uppercase tracking-wider">
                                Persyaratan Minimal Jabatan
                            </label>
                            <input type="text" name="requirement" id="formProgramRequirement"
                                   placeholder="Contoh: Minimal Co-Ass / Semua Staf"
                                   class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-200 font-semibold select-none">
                                <input type="checkbox" name="is_active" id="formProgramIsActive" value="1"
                                       class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 bg-slate-800 border-white/30">
                                <span>Buka Pendaftaran (Formulir Aktif)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="pt-4 border-t border-white/10 flex items-center justify-end gap-3 mt-6">
                        <button type="button" onclick="closeEditProgramModal()"
                                class="px-4 py-2 bg-white/10 hover:bg-white/15 text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-500 hover:to-yellow-500 text-white rounded-xl text-xs font-bold shadow-lg transition-all flex items-center gap-1.5">
                            <i class="fas fa-check"></i>
                            <span>Simpan Perubahan Jadwal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditProgramModal(program) {
                const modal = document.getElementById('editProgramModal');
                const form = document.getElementById('editProgramForm');
                if (!modal || !form) return;

                form.action = "{{ url('portal/pelatihan/program') }}/" + program.key;
                document.getElementById('modalProgramSubTitle').innerText = program.title;
                document.getElementById('formProgramTitle').value = program.title || '';
                document.getElementById('formProgramOrganizer').value = program.organizer || '';
                document.getElementById('formProgramBadge').value = program.badge || '';
                document.getElementById('formProgramDesc').value = program.desc || '';
                document.getElementById('formProgramRequirement').value = program.requirement || '';
                document.getElementById('formProgramIsActive').checked = program.is_active !== false;

                modal.classList.remove('hidden');
            }

            function closeEditProgramModal() {
                const modal = document.getElementById('editProgramModal');
                if (modal) modal.classList.add('hidden');
            }
        </script>
        @endif

    </div>
</div>
@endsection
