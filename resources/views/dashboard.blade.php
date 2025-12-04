@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Sistem Inventaris</h3>
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

        <!-- Statistik Utama -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $departmentCount }}</h3>
                        <p>Departemen</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <a href="{{ route('departments.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $employeeCount }}</h3>
                        <p>Karyawan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('karyawan.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $itemCount }}</h3>
                        <p>Barang</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="{{ route('barang.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}</h3>
                        <p>Total Pengeluaran Bulan Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <a href="{{ route('reports.index') }}" class="small-box-footer">
                        Lihat Laporan <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Baris Kedua -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $vehicleCount }}</h3>
                        <p>Kendaraan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <a href="{{ route('kendaraan.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $requestCount }}</h3>
                        <p>Total Permintaan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <a href="{{ route('permintaan-barang.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3>{{ $transactionOutCount }}</h3>
                        <p>Barang Keluar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <a href="{{ route('permintaan-barang.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-light">
                    <div class="inner text-dark">
                        <h3>{{ $transactionOutThisMonth }}</h3>
                        <p>Barang Keluar Bulan Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt text-dark"></i>
                    </div>
                    <a href="{{ route('permintaan-barang.index') }}" class="small-box-footer">
                        Info lebih <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Pengeluaran per Karyawan -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pengeluaran per Karyawan (Bulan Ini)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Karyawan</th>
                                        <th>Total Pengeluaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengeluaranPerKaryawan as $pengeluaran)
                                        <tr>
                                            <td>{{ $pengeluaran->user->name }}</td>
                                            <td class="text-right">
                                                <strong>Rp
                                                    {{ number_format($pengeluaran->total_pengeluaran, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">Tidak ada data pengeluaran</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($pengeluaranPerKaryawan->count() > 0)
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td><strong>Total</strong></td>
                                            <td class="text-right">
                                                <strong>Rp
                                                    {{ number_format($pengeluaranPerKaryawan->sum('total_pengeluaran'), 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barang dengan Harga Tertinggi -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Barang dengan Harga Tertinggi</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topExpensiveItems as $item)
                                        <tr>
                                            <td>{{ $item->nama }}</td>
                                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $item->stok < $item->stok_minimal ? 'badge-danger' : 'badge-success' }}">
                                                    {{ $item->stok }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data barang</td>
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
            <!-- Barang dengan Stok Rendah -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Barang dengan Stok Rendah (< 10)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Stok</th>
                                        <th>Stok Minimal</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lowStockItems as $item)
                                        <tr>
                                            <td>{{ $item->nama }}</td>
                                            <td><span class="badge badge-warning">{{ $item->stok }}</span></td>
                                            <td>{{ $item->stok_minimal }}</td>
                                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada barang dengan stok rendah
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permintaan Barang Belum Diverifikasi -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Permintaan Barang Belum Diverifikasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Karyawan</th>
                                        <th>Jumlah</th>
                                        <th>Total Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingRequests as $request)
                                        <tr>
                                            <td>{{ $request->barang->nama }}</td>
                                            <td>{{ $request->user->name }}</td>
                                            <td>{{ $request->jumlah }}</td>
                                            <td class="text-right">Rp
                                                {{ number_format($request->total_harga, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada permintaan barang</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($pendingRequests->count() > 0)
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="3"><strong>Total Nilai Pending</strong></td>
                                            <td class="text-right">
                                                <strong>Rp
                                                    {{ number_format($pendingRequests->sum('total_harga'), 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Barang Populer (Bulan Ini)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Jumlah Keluar</th>
                                        <th>Harga Satuan</th>
                                        <th>Total Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($popularItems as $item)
                                        <tr>
                                            <td>{{ $item->nama }}</td>
                                            <td class="text-center">{{ $item->transaction_count }}</td>
                                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                            <td class="text-right">
                                                <strong>Rp
                                                    {{ number_format($item->transaction_count * $item->harga, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                // Grafik Pengeluaran Bulanan
                const pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
                new Chart(pengeluaranCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartPengeluaranBulanan['labels']),
                        datasets: [{
                            label: 'Pengeluaran (Rp)',
                            data: @json($chartPengeluaranBulanan['data']),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
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

                // Diagram Status Permintaan
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($chartPermintaanStatus['labels']),
                        datasets: [{
                            data: @json($chartPermintaanStatus['data']),
                            backgroundColor: @json($chartPermintaanStatus['colors']),
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

                // Distribusi Kategori Barang
                const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
                new Chart(kategoriCtx, {
                    type: 'pie',
                    data: {
                        labels: @json($chartKategoriBarang['labels']),
                        datasets: [{
                            data: @json($chartKategoriBarang['data']),
                            backgroundColor: @json($chartKategoriBarang['colors']),
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

                // Trend Pengeluaran
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartTrendPengeluaran['labels']),
                        datasets: [{
                            label: 'Trend Pengeluaran',
                            data: @json($chartTrendPengeluaran['data']),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
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
            });
        </script>
    @endpush


    <!-- BAGIAN BARU: GRAFIK DAN DIAGRAM -->
    <div class="row mt-4">
        <!-- Grafik Pengeluaran Bulanan -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Pengeluaran Tahunan {{ date('Y') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="pengeluaranChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Diagram Status Permintaan -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Permintaan Barang</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Distribusi Kategori Barang -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Kategori Barang</h3>
                </div>
                <div class="card-body">
                    <canvas id="kategoriChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Trend Pengeluaran -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trend Pengeluaran {{ date('Y') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN BARU: MINI STATS CARDS -->
    <div class="row mt-4">
        <div class="col-lg-3 col-6">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-rata Pengeluaran/Bulan</span>
                    <span class="info-box-number">
                        Rp {{ number_format($rataRataPengeluaran, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pertumbuhan Bulan Ini</span>
                    <span class="info-box-number">
                        {{ number_format($pertumbuhanBulanIni, 1) }}%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Approval Rate</span>
                    <span class="info-box-number">
                        {{ number_format($approvalRate, 1) }}%
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Requests</span>
                    <span class="info-box-number">{{ $chartPermintaanStatus['data'][0] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection