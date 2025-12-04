@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kendaraan</h3>
                    <div class="card-tools">
                        <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Kendaraan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filter Form (Optional) -->
                    <form method="GET" action="{{ route('kendaraan.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari nama/plat/tipe..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="availability" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="1" {{ request('availability') == '1' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="0" {{ request('availability') == '0' ? 'selected' : '' }}>Tidak Tersedia</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Plat Nomor</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Konsumsi Bahan Bakar</th>
                                    <th>Perawatan Terakhir</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kendaraan as $item)  <!-- Ubah ke $item -->
                                <tr>
                                    <td>{{ $loop->iteration + ($kendaraan->perPage() * ($kendaraan->currentPage() - 1)) }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->plat_nomor }}</td>
                                    <td>{{ $item->tipe }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>{{ $item->konsumsi_bahan_bakar ? $item->konsumsi_bahan_bakar . ' km/L' : '-' }}</td>
                                    <td>{{ $item->perawatan_terakhir ? \Carbon\Carbon::parse($item->perawatan_terakhir)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($item->tersedia)  <!-- Ubah ke tersedia -->
                                            <span class="badge badge-success">Tersedia</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group " role="group">
                                            <a href="{{ route('kendaraan.show', $item) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('kendaraan.edit', $item) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('kendaraan.destroy', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data kendaraan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $kendaraan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection