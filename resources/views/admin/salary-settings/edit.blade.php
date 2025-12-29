@extends('layouts.app')

@section('title', 'Edit Atur Gaji')

@push('styles')
<style>
    .form-select {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.12);
        color: #ffffff;
        appearance: none;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }

    .form-select:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        transform: translateY(-1px);
        background: rgba(255,255,255,0.18);
        color: #ffffff !important;
    }

    .form-select option {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }

    .form-select option:checked {
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
    }

    .select-arrow {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        pointer-events: none;
        z-index: 2;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .form-select:focus + .select-arrow {
        color: #0ea5e9;
        transform: translateY(-50%) rotate(180deg);
    }
</style>
@endpush

@section('content')
<div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>

    <div class="relative max-w-4xl w-full mx-auto">
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 lg:p-12">
            <div class="text-center mb-6 sm:mb-8 md:mb-10">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Edit Atur Gaji</h1>
                <p class="text-blue-100 text-sm sm:text-base font-medium">Ubah atur gaji untuk role {{ $salarySetting->role_name }}</p>
            </div>
            <form method="POST" action="{{ route('admin.salary-settings.update', $salarySetting) }}">
                @csrf
                @method('PUT')
                
                <div class="border-b border-white/10 pb-6 mb-8">
                    <h3 class="text-xl font-semibold text-white mb-6">Informasi Setting Gaji</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Role Name -->
                        <div>
                            <label for="role_name" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Nama Role <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <select id="role_name" name="role_name" required
                                        class="form-select @error('role_name') border-red-500 @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                                {{ old('role_name', $salarySetting->role_name) == $role->name ? 'selected' : '' }}>
                                            {{ $role->display_name ?: $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="select-arrow">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            @error('role_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weekly Salary -->
                        <div>
                            <label for="weekly_salary" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Gaji Per Minggu <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="weekly_salary" name="weekly_salary" required step="0.01" min="0"
                                       class="form-input @error('weekly_salary') border-red-500 @enderror"
                                       placeholder="Masukkan gaji per minggu"
                                       value="{{ old('weekly_salary', $salarySetting->weekly_salary) }}">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <span class="text-gray-300 font-medium">$</span>
                                </div>
                            </div>
                            @error('weekly_salary')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Deskripsi (Opsional)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="form-input @error('description') border-red-500 @enderror"
                                      placeholder="Tambahkan deskripsi untuk role ini">{{ old('description', $salarySetting->description) }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Active -->
                        <div class="sm:col-span-2 flex items-center space-x-3">
                            <input type="checkbox" name="is_active" value="1" id="is_active" 
                                   class="w-4 h-4 text-sky-600 border-white/20 rounded focus:ring-sky-500 bg-white/10"
                                   {{ old('is_active', $salarySetting->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="text-white font-medium">Aktifkan setting ini</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ route('admin.salary-settings.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-semibold text-sm sm:text-base">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i>Update Setting
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
