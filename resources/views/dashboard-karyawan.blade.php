@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Karyawan</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalPermintaan }}</h3>
                        <p>Total Permintaan Barang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <a href="{{ route('permintaan-barang.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalPenggunaanKendaraan }}</h3>
                        <p>Peminjaman Kendaraan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <a href="{{ route('penggunaan-kendaraan.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $permintaanPending }}</h3>
                        <p>Permintaan Pending</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('permintaan-barang.index') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}</h3>
                        <p>Pengeluaran Bulan Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Riwayat Pengeluaran -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Pengeluaran Saya (Bulan Ini)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Total Harga</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatPengeluaran as $pengeluaran)
                                        <tr>
                                            <td>{{ $pengeluaran->barang->nama }}</td>
                                            <td class="text-center">{{ $pengeluaran->jumlah }}</td>
                                            <td class="text-right">Rp
                                                {{ number_format($pengeluaran->total_harga, 0, ',', '.') }}</td>
                                            <td>{{ $pengeluaran->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada riwayat pengeluaran bulan ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($riwayatPengeluaran->count() > 0)
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="2"><strong>Total Pengeluaran</strong></td>
                                            <td class="text-right">
                                                <strong>Rp
                                                    {{ number_format($riwayatPengeluaran->sum('total_harga'), 0, ',', '.') }}</strong>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permintaan Barang Terbaru -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Permintaan Barang Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->permintaanBarang()->latest()->take(5)->get() as $request)
                                        <tr>
                                            <td>{{ $request->barang->nama }}</td>
                                            <td class="text-center">{{ $request->jumlah }}</td>
                                            <td>
                                                @if ($request->status == 'menunggu')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                @elseif($request->status == 'disetujui')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @else
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada permintaan barang</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Peminjaman Kendaraan Terbaru -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Peminjaman Kendaraan Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Kendaraan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->penggunaanKendaraan()->latest()->take(5)->get() as $usage)
                                        <tr>
                                            <td>{{ $usage->kendaraan->nama }} ({{ $usage->kendaraan->plat_nomor }})</td>
                                            <td>{{ $usage->tanggal_mulai->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($usage->status == 'menunggu')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                @elseif($usage->status == 'disetujui')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @elseif($usage->status == 'ditolak')
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @else
                                                    <span class="badge badge-info">Dikembalikan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada peminjaman kendaraan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Cepat -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Statistik Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Disetujui</span>
                                        <span class="info-box-number">
                                            {{ $user->permintaanBarang()->where('status', 'disetujui')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Menunggu</span>
                                        <span class="info-box-number">
                                            {{ $user->permintaanBarang()->where('status', 'menunggu')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-car"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Kendaraan Aktif</span>
                                        <span class="info-box-number">
                                            {{ $user->penggunaanKendaraan()->where('status', 'disetujui')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Item</span>
                                        <span class="info-box-number">
                                            {{ $user->permintaanBarang()->where('status', 'disetujui')->sum('jumlah') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('styles')
        <style>
            .small-box {
                border-radius: 0.25rem;
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
                display: block;
                margin-bottom: 20px;
                position: relative;
            }

            .small-box>.inner {
                padding: 10px;
            }

            .small-box h3 {
                font-size: 2.2rem;
                font-weight: bold;
                margin: 0 0 10px;
                padding: 0;
                white-space: nowrap;
            }

            .small-box p {
                font-size: 1rem;
            }

            .small-box .icon {
                color: rgba(0, 0, 0, .15);
                z-index: 0;
                position: absolute;
                right: 10px;
                top: 10px;
                font-size: 70px;
                transition: all .3s linear;
            }

            .small-box:hover .icon {
                font-size: 75px;
            }

            .info-box {
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
                border-radius: 0.25rem;
                background: #fff;
                display: flex;
                margin-bottom: 1rem;
                min-height: 80px;
                padding: 0.5rem;
                position: relative;
            }

            .info-box .info-box-icon {
                border-radius: 0.25rem;
                align-items: center;
                display: flex;
                font-size: 1.875rem;
                justify-content: center;
                text-align: center;
                width: 70px;
            }

            .info-box .info-box-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                line-height: 1.8;
                flex: 1;
                padding: 0 10px;
            }

            .info-box .info-box-text {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .info-box .info-box-number {
                display: block;
                margin-top: 0.25rem;
                font-weight: 700;
            }

            .table td.text-right {
                text-align: right;
            }

            .table td.text-center {
                text-align: center;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                // Grafik Pengeluaran Pribadi
                const pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
                new Chart(pengeluaranCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartPengeluaranBulanan['labels']),
                        datasets: [{
                            label: 'Pengeluaran Saya (Rp)',
                            data: @json($chartPengeluaranBulanan['data']),
                            backgroundColor: 'rgba(75, 192, 192, 0.8)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ".");
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toString().replace(
                                            /\B(?=(\d{3})+(?!\d))/g, ".");
                                    }
                                }
                            }
                        }
                    }
                });

                // Diagram Status Permintaan Saya
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($chartStatusPermintaan['labels']),
                        datasets: [{
                            data: @json($chartStatusPermintaan['data']),
                            backgroundColor: @json($chartStatusPermintaan['colors']),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

    {{-- KONTEN YANG SUDAH ADA TETAP DI SINI --}}

    <!-- BAGIAN BARU: GRAFIK UNTUK KARYAWAN -->
    <div class="row mt-4">
        <!-- Grafik Pengeluaran Pribadi -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Pengeluaran Saya {{ date('Y') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="pengeluaranChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Diagram Status Permintaan Saya -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Permintaan Saya</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN BARU: STATISTIK LANJUTAN -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Items Disetujui</span>
                    <span class="info-box-number">
                        {{ $totalItemsDisetujui }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Success Rate</span>
                    <span class="info-box-number">
                        {{ number_format($successRate, 1) }}%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avg Processing Time</span>
                    <span class="info-box-number">
                        {{ number_format($avgProcessingTime, 1) }} hari
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
