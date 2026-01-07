@extends('layouts.app')

@section('title', 'Edit Struktur Organisasi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.organizational-structure.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Struktur Organisasi</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.organizational-structure.update', $structure->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama/Label (Opsional)
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $structure->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Struktur EMS 2026">
            </div>

            <div class="mb-4">
                <label for="hospital_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipe Hospital <span class="text-red-500">*</span>
                </label>
                <select name="hospital_type" id="hospital_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="ems" {{ old('hospital_type', $structure->hospital_type) === 'ems' ? 'selected' : '' }}>EMS (Emergency Medical Services)</option>
                    <option value="roxwood" {{ old('hospital_type', $structure->hospital_type) === 'roxwood' ? 'selected' : '' }}>Roxwood Hospital</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="structure_data" class="block text-sm font-medium text-gray-700 mb-2">
                    Data Struktur (JSON) <span class="text-red-500">*</span>
                </label>
                <textarea name="structure_data" id="structure_data" rows="15" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('structure_data', json_encode($structure->structure_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                <p class="text-sm text-gray-500 mt-1">
                    Format JSON untuk hierarki struktur organisasi.
                </p>
            </div>

            <div class="mb-4">
                <label for="required_names" class="block text-sm font-medium text-gray-700 mb-2">
                    Daftar Nama yang Wajib Ditampilkan (Opsional)
                </label>
                <textarea name="required_names" id="required_names" rows="10"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan satu nama per baris">{{ old('required_names', is_array($structure->required_names) ? implode("\n", $structure->required_names) : '') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">
                    Nama-nama yang harus muncul di chart meskipun role mereka adalah admin. Satu nama per baris.
                </p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $structure->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Aktifkan struktur ini (struktur lain dengan tipe yang sama akan dinonaktifkan)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.organizational-structure.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
