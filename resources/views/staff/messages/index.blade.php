@extends('layouts.app')

@section('title', 'Pesan Pribadi - Portal Medis')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-comments mr-3 text-sky-400"></i>Pesan Pribadi
                </h1>
                <p class="text-sky-200">Komunikasi langsung dan real-time antar staf medis bertugas</p>
            </div>
            <a href="{{ route('staff.members.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-semibold rounded-xl border border-white border-opacity-20 text-xs transition-all shadow-md">
                <i class="fas fa-user-plus mr-2 text-sky-300"></i> Cari Staf di Direktori
            </a>
        </div>

        @livewire('member-messages')

    </div>
</div>
@endsection
