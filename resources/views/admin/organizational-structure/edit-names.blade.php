@extends('layouts.app')

@section('title', 'Edit Nama - Struktur Organisasi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-2xl p-8">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Edit Nama Staff</h1>
                        <p class="text-blue-200 mt-2">
                            {{ $structure->name ?? 'Struktur ' . ucfirst($structure->hospital_type) }}</p>
                    </div>
                    <a href="{{ route('admin.organizational-structure.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>

                @if($errors->any())
                    <div class="bg-red-500 bg-opacity-20 border-l-4 border-red-500 text-white p-4 rounded mb-6">
                        <p class="font-bold">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.organizational-structure.update-names', $structure->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Hospital Type (readonly) --}}
                    <div class="mb-6">
                        <label class="block text-white font-medium mb-2">
                            <i class="fas fa-hospital mr-2"></i>Hospital
                        </label>
                        <input type="text" value="{{ ucfirst($structure->hospital_type) }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-700 bg-opacity-50 text-gray-400 cursor-not-allowed">
                    </div>

                    {{-- Structure Name --}}
                    <div class="mb-6">
                        <label class="block text-white font-medium mb-2">
                            <i class="fas fa-tag mr-2"></i>Nama Struktur (Optional)
                        </label>
                        <input type="text" name="name" value="{{ old('name', $structure->name) }}"
                            class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-10 text-white border-2 border-white border-opacity-20 focus:border-blue-500 focus:outline-none"
                            placeholder="Contoh: Struktur 2024">
                    </div>

                    {{-- Names Section --}}
                    <div class="bg-white bg-opacity-5 rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-4">
                            <i class="fas fa-users mr-2"></i>Edit Nama Staff
                        </h2>
                        <p class="text-blue-200 text-sm mb-6">Isi nama staff untuk setiap posisi. Struktur hierarki tidak
                            dapat diubah.</p>

                        <div id="names-container" class="space-y-4">
                            @php
                                $structureData = $structure->structure_data;
                                $index = 0;
                            @endphp

                            @if(isset($structureData['high_command']))
                                <div class="border-l-4 border-yellow-500 bg-yellow-500 bg-opacity-10 p-4 rounded">
                                    <label class="block text-yellow-300 font-bold mb-2">
                                        <i class="fas fa-star mr-2"></i>High Command
                                    </label>
                                    <input type="text" name="names[{{ $index }}][name]"
                                        value="{{ old('names.' . $index . '.name', $structureData['high_command']['name'] ?? '') }}"
                                        class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 text-white border border-yellow-500 border-opacity-30 focus:border-yellow-500 focus:outline-none"
                                        placeholder="Nama High Command">
                                    <input type="hidden" name="names[{{ $index }}][position]" value="high_command">
                                    @php $index++; @endphp
                                </div>
                            @endif

                            @if(isset($structureData['departments']) && is_array($structureData['departments']))
                                @foreach($structureData['departments'] as $deptIndex => $dept)
                                    <div class="border-l-4 border-blue-500 bg-blue-500 bg-opacity-10 p-4 rounded">
                                        <label class="block text-blue-300 font-bold mb-2">
                                            <i
                                                class="fas fa-building mr-2"></i>{{ $dept['title'] ?? 'Department ' . ($deptIndex + 1) }}
                                        </label>
                                        <input type="text" name="names[{{ $index }}][name]"
                                            value="{{ old('names.' . $index . '.name', $dept['name'] ?? '') }}"
                                            class="w-full px-4 py-2 rounded-lg bg-white bg-opacity-10 text-white border border-blue-500 border-opacity-30 focus:border-blue-500 focus:outline-none"
                                            placeholder="Nama Kepala Department">
                                        <input type="hidden" name="names[{{ $index }}][position]"
                                            value="department_{{ $deptIndex }}">
                                        @php $index++; @endphp

                                        @if(isset($dept['members']) && is_array($dept['members']))
                                            @foreach($dept['members'] as $memberIndex => $member)
                                                <div class="mt-3 ml-6 border-l-2 border-green-500 bg-green-500 bg-opacity-5 p-3 rounded">
                                                    <label class="block text-green-300 text-sm font-medium mb-2">
                                                        <i
                                                            class="fas fa-user mr-2"></i>{{ $member['role'] ?? 'Staff ' . ($memberIndex + 1) }}
                                                    </label>
                                                    <input type="text" name="names[{{ $index }}][name]"
                                                        value="{{ old('names.' . $index . '.name', $member['name'] ?? '') }}"
                                                        class="w-full px-3 py-2 rounded-lg bg-white bg-opacity-10 text-white border border-green-500 border-opacity-30 focus:border-green-500 focus:outline-none text-sm"
                                                        placeholder="Nama {{ $member['role'] ?? 'Staff' }}">
                                                    <input type="hidden" name="names[{{ $index }}][position]"
                                                        value="department_{{ $deptIndex }}_member_{{ $memberIndex }}">
                                                    @php $index++; @endphp
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.organizational-structure.index') }}"
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-4 rounded-lg transition-colors text-center">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection