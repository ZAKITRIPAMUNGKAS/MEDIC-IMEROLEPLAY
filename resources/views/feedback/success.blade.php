@extends('layouts.app')

@section('title', 'Laporan Terkirim - Portal Medis MPK-BA')

@section('content')
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <div class="relative max-w-lg w-full mx-auto">
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8 text-center animate-fade-in-up">

                <div
                    class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border border-green-400/30">
                    <i class="fas fa-check text-4xl text-green-400"></i>
                </div>

                <h2 class="text-3xl font-bold text-white mb-4">Terima Kasih!</h2>

                <p class="text-blue-100 text-lg mb-8 leading-relaxed">
                    Laporan & Masukan Anda telah berhasil dikirim. <br>
                    Tim kami akan segera meninjau pesan Anda.
                </p>

                <div class="space-y-4">
                    <a href="{{ route('public.index') }}"
                        class="block w-full px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sky-500/30 transform hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>

                    <a href="{{ route('feedback.form') }}"
                        class="block w-full px-6 py-3 bg-white/10 border border-white/20 text-white font-bold rounded-xl hover:bg-white/20 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i> Buat Laporan Baru
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }
    </style>
@endpush