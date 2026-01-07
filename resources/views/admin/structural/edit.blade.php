@extends('layouts.app')

@section('content')
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('admin.structural.index') }}"
                    class="inline-flex items-center text-gray-300 hover:text-white transition-colors duration-300 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Positions
                </a>
                <h1 class="text-3xl sm:text-4xl font-bold text-white">
                    <i class="fas fa-edit mr-3"></i>Edit Position
                </h1>
                <p class="text-gray-300 mt-2">Update position: <strong>{{ $structural->title }}</strong></p>
            </div>

            {{-- Form --}}
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-8">
                <form action="{{ route('admin.structural.update', $structural) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Level --}}
                    <div class="mb-6">
                        <label for="level" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-layer-group mr-2"></i>Hierarchy Level *
                        </label>
                        <select name="level" id="level" required
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                            <option value="">Select Level</option>
                            @foreach($levels as $levelValue => $levelLabel)
                                <option value="{{ $levelValue }}" {{ old('level', $structural->level) == $levelValue ? 'selected' : '' }}>
                                    {{ $levelLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('level')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Parent Position --}}
                    <div class="mb-6">
                        <label for="parent_id" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-level-up-alt mr-2"></i>Parent Position
                        </label>
                        <select name="parent_id" id="parent_id"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                            <option value="">No Parent (Root Level)</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('parent_id', $structural->parent_id) == $pos->id ? 'selected' : '' }}>
                                    {{ str_repeat('— ', $pos->level) }}{{ $pos->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-heading mr-2"></i>Position Title *
                        </label>
                        <input type="text" name="title" id="title" required value="{{ old('title', $structural->title) }}"
                            placeholder="e.g., CEO, Department Head, Manager"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                        @error('title')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Position Name --}}
                    <div class="mb-6">
                        <label for="position_name" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-tag mr-2"></i>Full Position Name / Department
                        </label>
                        <input type="text" name="position_name" id="position_name"
                            value="{{ old('position_name', $structural->position_name) }}"
                            placeholder="e.g., Chief Executive Officer, People & Development"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                        @error('position_name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Assign User --}}
                    <div class="mb-6">
                        <label for="user_id" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-user mr-2"></i>Assign User
                        </label>
                        <select name="user_id" id="user_id"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                            <option value="">Not Assigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $structural->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                    @if($user->role)({{ $user->role->display_name ?? $user->role->name }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Display Order --}}
                    <div class="mb-6">
                        <label for="display_order" class="block text-sm font-semibold text-gray-200 mb-2">
                            <i class="fas fa-sort-numeric-down mr-2"></i>Display Order
                        </label>
                        <input type="number" name="display_order" id="display_order"
                            value="{{ old('display_order', $structural->display_order) }}" min="0" placeholder="0"
                            class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-300">
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Is Active --}}
                    <div class="mb-8">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $structural->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-sky-500 bg-white/5 border-white/20 rounded focus:ring-sky-500 focus:ring-2">
                            <span class="ml-3 text-sm font-semibold text-gray-200">
                                <i class="fas fa-toggle-on mr-2"></i>Active Position
                            </span>
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('admin.structural.index') }}"
                            class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-lg font-medium transition-all duration-300 border border-white/20">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i>Update Position
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection