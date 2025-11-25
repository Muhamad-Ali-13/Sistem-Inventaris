@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard Karyawan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $userRequestsCount }}</h3>
                                    <p>Permintaan Barang Saya</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <a href="{{ route('item-requests.index') }}" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $userVehicleUsageCount }}</h3>
                                    <p>Peminjaman Kendaraan Saya</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-car"></i>
                                </div>
                                <a href="{{ route('vehicle-usage.index') }}" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $userPendingRequestsCount }}</h3>
                                    <p>Permintaan Pending</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <a href="{{ route('item-requests.index') }}" class="small-box-footer">
                                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Permintaan Barang Terbaru</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user->itemRequests()->latest()->take(5)->get() as $request)
                                                <tr>
                                                    <td>{{ $request->item->name ?? 'N/A' }}</td>
                                                    <td>{{ $request->quantity }}</td>
                                                    <td>
                                                        @if($request->status == 'pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif($request->status == 'approved')
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Peminjaman Kendaraan Terbaru</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Kendaraan</th>
                                                    <th>Tanggal Mulai</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user->vehicleUsage()->latest()->take(5)->get() as $usage)
                                                <tr>
                                                    <td>{{ $usage->vehicle->name ?? 'N/A' }}</td>
                                                    <td>{{ $usage->start_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($usage->status == 'pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif($usage->status == 'approved')
                                                            <span class="badge badge-success">Disetujui</span>
                                                        @elseif($usage->status == 'rejected')
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection