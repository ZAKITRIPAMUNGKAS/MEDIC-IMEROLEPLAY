@extends('layouts.app')

@section('title', 'Management Struktural EMS - Admin')

@section('content')
    <main
        class="min-h-screen bg-gradient-to-br from-sky-900 via-blue-900 to-indigo-900 py-12 sm:py-16 relative overflow-hidden">
        {{-- Animated Background --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-40 right-10 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            {{-- Header --}}
            <header class="text-center mb-12 relative">
                <div class="relative z-10">
                    <h1 class="text-5xl font-black mb-6">
                        <span
                            class="bg-gradient-to-r from-cyan-300 via-blue-300 to-indigo-300 bg-clip-text text-transparent">
                            Management Struktural EMS
                        </span>
                    </h1>
                    <p
                        class="text-xl text-sky-100 backdrop-blur-sm bg-white/5 rounded-2xl py-4 px-8 border border-white/10 inline-block">
                        Edit nama staff dengan dropdown - Struktur tetap
                    </p>
                </div>
            </header>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div
                    class="max-w-2xl mx-auto mb-6 bg-green-500 bg-opacity-20 border-l-4 border-green-500 text-white p-4 rounded">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-2xl mx-auto mb-6 bg-red-500 bg-opacity-20 border-l-4 border-red-500 text-white p-4 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- Hospital Selector --}}
            <div class="max-w-md mx-auto mb-8">
                <label class="block text-white text-center font-bold mb-3">Pilih Hospital:</label>
                <select id="hospital-selector"
                    class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-10 text-white border-2 border-white border-opacity-20 focus:border-blue-500 focus:outline-none text-center font-bold text-lg">
                    <option value="ems" {{ request('hospital', 'ems') == 'ems' ? 'selected' : '' }}>EMS (Emergency Medical
                        Services)</option>
                    <option value="roxwood" {{ request('hospital') == 'roxwood' ? 'selected' : '' }}>Roxwood Hospital</option>
                </select>
            </div>

            {{-- Get active structure for selected hospital --}}
            @php
                $selectedHospital = request('hospital', 'ems');
                $activeStructure = $structures->where('hospital_type', $selectedHospital)->where('is_active', true)->first();
            @endphp

            @if($activeStructure)
                <form action="{{ route('admin.organizational-structure.update-names', $activeStructure->id) }}" method="POST"
                    id="edit-structure-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $activeStructure->name }}">

                    {{-- Visual Structure Display with Dropdowns --}}
                    <section class="relative">
                        @php
                            $structureData = $activeStructure->structure_data;
                            $nameIndex = 0;
                        @endphp

                        {{-- High Command --}}
                        @if(isset($structureData['high_command']))
                            <article class="relative mb-16">
                                <div class="text-center mb-8">
                                    <div
                                        class="inline-flex items-center space-x-5 bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 text-white px-8 py-5 rounded-2xl shadow-2xl">
                                        <i class="fas fa-crown text-4xl"></i>
                                        <h2 class="text-3xl font-black">High Command</h2>
                                    </div>
                                </div>
                                <div class="max-w-md mx-auto glass-effect rounded-2xl p-6">
                                    <select name="names[{{ $nameIndex }}][name]"
                                        class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white border-2 border-yellow-400 focus:border-yellow-300 focus:outline-none text-center font-bold text-xl">
                                        <option value="">-- Pilih High Command --</option>
                                        <option value="N/A" {{ ($structureData['high_command']['name'] ?? '') == 'N/A' ? 'selected' : '' }}>N/A (Kosong)</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->name }}" {{ ($structureData['high_command']['name'] ?? '') == $user->name ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="names[{{ $nameIndex }}][position]" value="high_command">
                                    @php $nameIndex++; @endphp
                                </div>
                            </article>
                        @endif

                        {{-- Departments --}}
                        @if(isset($structureData['departments']) && is_array($structureData['departments']))
                            @foreach($structureData['departments'] as $deptIndex => $dept)
                                @if($loop->first)
                                    <div
                                        class="absolute left-1/2 top-0 w-0.5 h-16 bg-gradient-to-b from-white/40 to-white/20 transform -translate-x-1/2 -translate-y-16">
                                    </div>
                                @endif

                                <article class="relative mb-16">
                                    {{-- Department Header --}}
                                    <div class="text-center mb-8">
                                        <div
                                            class="inline-flex items-center space-x-5 bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 text-white px-8 py-5 rounded-2xl shadow-2xl">
                                            <i class="fas fa-building text-3xl"></i>
                                            <h2 class="text-2xl font-black">{{ $dept['title'] ?? 'Department' }}</h2>
                                        </div>
                                    </div>

                                    {{-- Department Head --}}
                                <div class="max-w-md mx-auto mb-8 glass-effect rounded-2xl p-6">
                                    <label class="block text-blue-200 text-sm font-bold mb-2 text-center">Kepala Department</label>
                                    <select name="names[{{ $nameIndex }}][name]" 
                                            class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 text-white border-2 border-blue-400 focus:border-blue-300 focus:outline-none text-center font-bold">
                                        <option value="">-- Pilih Kepala {{ $dept['title'] ?? 'Department' }} --</option>
                                        <option value="N/A" {{ ($dept['name'] ?? '') == 'N/A' ? 'selected' : '' }}>N/A (Kosong)</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->name }}" {{ ($dept['name'] ?? '') == $user->name ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="names[{{ $nameIndex }}][position]" value="department_{{ $deptIndex }}">
                                    @php $nameIndex++; @endphp
                                </div>

                                    {{-- Department Members --}}
                                    @if(isset($dept['members']) && is_array($dept['members']))
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                                            @foreach($dept['members'] as $memberIndex => $member)
                                                <div class="glass-effect rounded-xl p-4">
                                                    <label
                                                        class="block text-green-200 text-xs font-bold mb-2 text-center">{{ $member['role'] ?? 'Staff' }}</label>
                                                    <select name="names[{{ $nameIndex }}][name]" 
                                                            class="w-full px-3 py-2 rounded-lg bg-white bg-opacity-20 text-white border border-green-400 focus:border-green-300 focus:outline-none text-center text-sm">
                                                        <option value="">-- Pilih --</option>
                                                        <option value="N/A" {{ ($member['name'] ?? '') == 'N/A' ? 'selected' : '' }}>N/A</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->name }}" {{ ($member['name'] ?? '') == $user->name ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="names[{{ $nameIndex }}][position]"
                                                        value="department_{{ $deptIndex }}_member_{{ $memberIndex }}">
                                                    @php $nameIndex++; @endphp
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        @endif
                    </section>

                    {{-- Save Button --}}
                    <div class="max-w-2xl mx-auto mt-12 flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Semua Perubahan
                        </button>
                        <a href="{{ route('public.struktural-ems') }}" target="_blank"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white font-bold py-4 rounded-lg transition-all text-center">
                            <i class="fas fa-eye mr-2"></i>Lihat Public
                        </a>
                    </div>
                </form>
            @else
                <div class="max-w-2xl mx-auto glass-effect rounded-2xl p-12 text-center">
                    <i class="fas fa-info-circle text-blue-300 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-white mb-4">Belum Ada Struktur Aktif</h3>
                    <p class="text-blue-200 mb-6">Belum ada struktur yang aktif untuk hospital
                        {{ strtoupper($selectedHospital) }}.</p>
                    <a href="{{ route('admin.organizational-structure.create') }}"
                        class="inline-block bg-gradient-to-r from-blue-500 to-cyan-600 text-white px-8 py-3 rounded-lg font-bold hover:scale-105 transform transition-all">
                        <i class="fas fa-plus mr-2"></i>Buat Struktur Baru
                    </a>
                </div>
            @endif

            {{-- Info Box --}}
            <div class="max-w-4xl mx-auto mt-12 glass-effect rounded-xl p-6 border border-blue-400 border-opacity-30">
                <div class="flex items-start gap-4">
                    <i class="fas fa-info-circle text-blue-300 text-2xl mt-1"></i>
                    <div class="text-blue-100">
                        <p class="font-bold mb-2">Tips:</p>
                        <ul class="text-sm space-y-1 list-disc list-inside">
                            <li>Tinggal isi nama di setiap field, struktur tidak berubah</li>
                            <li>Klik "Simpan Semua Perubahan" untuk menyimpan</li>
                            <li>Klik "Lihat Public" untuk preview tampilan publik</li>
                            <li>Struktur hierarki tetap terjaga, cuma nama yang berubah</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('hospital-selector').addEventListener('change', function () {
            window.location.href = '{{ route("admin.organizational-structure.index") }}?hospital=' + this.value;
        });
    </script>

    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endsection