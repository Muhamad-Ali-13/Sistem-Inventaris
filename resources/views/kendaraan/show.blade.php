@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Kendaraan</h3>
                    <div class="card-tools">
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama Kendaraan</th>
                                    <td>{{ $kendaraan->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Plat Nomor</th>
                                    <td>{{ $kendaraan->plat_nomor }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>{{ $kendaraan->tipe }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($kendaraan->harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Konsumsi Bahan Bakar</th>
                                    <td>{{ $kendaraan->konsumsi_bahan_bakar ? $kendaraan->konsumsi_bahan_bakar . ' km/L' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Perawatan Terakhir</th>
                                    <td>{{ $kendaraan->perawatan_terakhir ? \Carbon\Carbon::parse($kendaraan->perawatan_terakhir)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($kendaraan->tersedia)
                                            <span class="badge badge-success">Tersedia</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection