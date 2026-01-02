@extends('layouts.app')

@section('title', 'Laporan & Masukan - Portal Medis MPK-BA')

@section('content')
    <div class="relative min-h-[calc(100vh-64px)] py-4 px-4 sm:px-6 lg:px-8">
        <!-- Background Gradients -->
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative w-full px-2 text-white">
            <!-- Header Section -->
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl mb-4 overflow-hidden"
                style="background: linear-gradient(135deg, rgba(7, 89, 133, 0.95) 0%, rgba(14, 116, 144, 0.9) 100%);">

                <!-- Decorative Elements -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-cyan-400/10 to-transparent rounded-full blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-sky-400/10 to-transparent rounded-full blur-2xl">
                </div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <!-- Left Section -->
                        <div class="flex items-start gap-5">
                            <!-- Icon Container -->
                            <div
                                class="hidden sm:flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-400 to-cyan-500 rounded-2xl shadow-lg border-2 border-white/20">
                                <i class="fas fa-comment-dots text-2xl text-white"></i>
                            </div>

                            <!-- Text Content -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                                        Laporan & Masukan
                                    </h1>
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-green-400 to-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                                        FEEDBACK
                                    </span>
                                </div>
                                <p class="text-sky-100 text-sm flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-xs"></i>
                                    Kelola feedback dan masukan dari pengguna secara anonim
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="backdrop-blur-xl bg-white/10 border border-sky-400/30 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-sky-400 to-cyan-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-inbox text-white text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ $totalFeedback }}</h3>
                    </div>
                    <p class="text-sky-200 text-sm font-medium">Total Feedback</p>
                </div>

                <div class="backdrop-blur-xl bg-white/10 border border-blue-400/30 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ $newFeedback }}</h3>
                    </div>
                    <p class="text-blue-200 text-sm font-medium">Feedback Baru</p>
                </div>

                <div class="backdrop-blur-xl bg-white/10 border border-red-400/30 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ $kritikCount }}</h3>
                    </div>
                    <p class="text-red-200 text-sm font-medium">Laporan</p>
                </div>

                <div class="backdrop-blur-xl bg-white/10 border border-green-400/30 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lightbulb text-white text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white">{{ $saranCount }}</h3>
                    </div>
                    <p class="text-green-200 text-sm font-medium">Masukan</p>
                </div>
            </div>

            <!-- Main Content -->
            <livewire:feedback-list />
        </div>
    </div>
@endsection