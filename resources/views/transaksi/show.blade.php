@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Transaksi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Kode Transaksi</th>
                            <td>{{ $transaction->code }}</td>
                        </tr>
                        <tr>
                            <th>Barang</th>
                            <td>{{ $transaction->barang->nama }} ({{ $transaction->barang->kode }})</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $transaction->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>
                                <span class="badge {{ $transaction->tipe == 'in' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $transaction->jumlah }} {{ $transaction->tipe == 'in' ? 'IN' : 'OUT' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <td>{{ $transaction->tanggal_transaksi->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $transaction->catatan }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Ditambahkan</th>
                            <td>{{ $transaction->created_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection