@extends('layouts.app')

@section('title', 'Laporan & Analisis - Portal Medis MPK-BA')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);"></div>
    
    <div class="relative z-10 px-4 py-8 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <div class="h-20 w-20 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl animate-float">
                    <i class="fas fa-chart-line text-white text-3xl"></i>
                </div>
            </div>
            <h1 class="text-5xl md:text-6xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">Laporan & Analisis</h1>
            <p class="text-xl text-sky-200 max-w-3xl mx-auto">Statistik dan analisis performa sistem medis yang komprehensif</p>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Weekly Leaderboard Chart -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8">
                <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <i class="fas fa-trophy mr-3 text-yellow-400"></i>
                    Leaderboard Absensi Mingguan
                </h3>
                <div class="bg-white/20 rounded-xl p-4 border border-white/30">
                    <canvas id="leaderboardChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Form Status Distribution -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8">
                <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <i class="fas fa-pie-chart mr-3 text-pink-400"></i>
                    Distribusi Status Formulir
                </h3>
                <div class="bg-white/20 rounded-xl p-4 border border-white/30">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Forms Chart -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-chart-area mr-3 text-green-400"></i>
                Trend Formulir Harian (7 Hari Terakhir)
            </h3>
            <div class="bg-white/5 rounded-xl p-4">
                <canvas id="dailyChart" width="800" height="400"></canvas>
            </div>
        </div>

        <!-- Form Types Chart -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center">
                <i class="fas fa-chart-bar mr-3 text-purple-400"></i>
                Distribusi Jenis Formulir
            </h3>
            <div class="bg-white/5 rounded-xl p-4">
                <canvas id="formTypesChart" width="800" height="400"></canvas>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 border-2 border-white/70">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-file-alt text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-sky-200">Total Formulir</p>
                        <p class="text-3xl font-bold text-white">{{ $formStats['by_status']->sum('count') ?? 0 }}</p>
                        <!-- Debug: {{ $formStats['by_status'] }} -->
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 border-2 border-white/70">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-sky-200">Menunggu Review</p>
                        <p class="text-3xl font-bold text-white">{{ $formStats['by_status']->where('status', 'pending')->first()->count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 border-2 border-white/70">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-sky-200">Disetujui</p>
                        <p class="text-3xl font-bold text-white">{{ $formStats['by_status']->where('status', 'approved')->first()->count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 border-2 border-white/70">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-times-circle text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-sky-200">Ditolak</p>
                        <p class="text-3xl font-bold text-white">{{ $formStats['by_status']->where('status', 'rejected')->first()->count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.glass-effect {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(24px);
    border: 2px solid rgba(255, 255, 255, 0.9);
}

.elegant-shadow-lg {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}
</style>
@endpush

@push('scripts')
<script>
// Wait for Chart.js to load
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }
    
    console.log('Chart.js loaded successfully');

// Chart.js configuration
const colors = {
    primary: '#0EA5E9',
    secondary: '#06B6D4',
    success: '#10B981',
    warning: '#F59E0B',
    danger: '#EF4444',
    info: '#8B5CF6',
    light: '#F3F4F6',
    dark: '#1E293B'
};

// Weekly Leaderboard Chart
const leaderboardCtx = document.getElementById('leaderboardChart').getContext('2d');
const leaderboardData = @json($leaderboardData);
console.log('Leaderboard Data:', leaderboardData);
if (leaderboardData && leaderboardData.length > 0) {
    new Chart(leaderboardCtx, {
        type: 'bar',
        data: {
            labels: leaderboardData.map(staff => staff.name),
            datasets: [{
                label: 'Total Jam Kerja',
                data: leaderboardData.map(staff => staff.total_hours),
                backgroundColor: colors.primary,
                borderColor: colors.primary,
                borderWidth: 1
            }]
        },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jam Kerja',
                    color: '#E0F2FE'
                },
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            },
            x: {
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
    });
} else {
    console.log('No leaderboard data available');
}

// Form Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = @json($formStats['by_status']);
console.log('Status Data:', statusData);
if (statusData && statusData.length > 0) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => {
                const statusMap = {
                    'pending': 'Menunggu',
                    'approved': 'Disetujui',
                    'rejected': 'Ditolak'
                };
                return statusMap[item.status] || item.status;
            }),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [colors.warning, colors.success, colors.danger],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#E0F2FE'
                }
            }
        }
    }
    });
} else {
    console.log('No status data available');
}

// Daily Forms Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyData = @json($formStats['daily']);
console.log('Daily Data:', dailyData);
const last7Days = [];
for (let i = 6; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    last7Days.push(date.toISOString().split('T')[0]);
}

const dailyChartData = last7Days.map(date => {
    const found = dailyData.find(item => item.date === date);
    return found ? found.count : 0;
});

new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: last7Days.map(date => {
            const d = new Date(date);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: 'Jumlah Formulir',
            data: dailyChartData,
            borderColor: colors.primary,
            backgroundColor: colors.primary + '20',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Formulir',
                    color: '#E0F2FE'
                },
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            },
            x: {
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Form Types Chart
const formTypesCtx = document.getElementById('formTypesChart').getContext('2d');
const formTypesData = @json($formStats['by_type']);
new Chart(formTypesCtx, {
    type: 'bar',
    data: {
        labels: formTypesData.map(item => {
            const typeMap = {
                'surat_kesehatan': 'Surat Kesehatan',
                'konsultasi_medis': 'Konsultasi Medis',
                'laporan_kecelakaan': 'Laporan Kecelakaan',
                'permintaan_ambulans': 'Permintaan Ambulans'
            };
            return typeMap[item.form_type] || item.form_type;
        }),
        datasets: [{
            label: 'Jumlah Formulir',
            data: formTypesData.map(item => item.count),
            backgroundColor: [colors.primary, colors.secondary, colors.warning, colors.info],
            borderColor: [colors.primary, colors.secondary, colors.warning, colors.info],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Formulir',
                    color: '#E0F2FE'
                },
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            },
            x: {
                ticks: {
                    color: '#E0F2FE'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

}); // End DOMContentLoaded
</script>
@endpush
@endsection
