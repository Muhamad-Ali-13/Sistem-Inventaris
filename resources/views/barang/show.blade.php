@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Barang</h3>
                    <div class="card-tools">
                        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Nama Barang</th>
                                    <td>{{ $barang->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Kode Barang</th>
                                    <td>{{ $barang->kode }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $barang->kategori->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $barang->deskripsi ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Stok Saat Ini</th>
                                    <td>
                                        <span class="{{ $barang->stok < $barang->stok_minimal ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $barang->stok }}
                                            @if($barang->stok < $barang->stok_minimal)
                                                <small class="text-danger">(Stok Rendah!)</small>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stok Minimal</th>
                                    <td>{{ $barang->stok_minimal }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>{{ $barang->harga }}</td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $barang->lokasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Ditambahkan</th>
                                    <td>{{ $barang->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Aksi Cepat</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('barang.edit', $barang) }}" class="btn btn-primary">Edit Barang</a>
                                    <a href="{{ route('transaksi.create') }}?item_id={{ $barang->id }}" class="btn btn-success">Tambah Transaksi</a>
                                    <a href="{{ route('permintaan-barang.create') }}?item_id={{ $barang->id }}" class="btn btn-info">Tambah Permintaan Barang</a>
                                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
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