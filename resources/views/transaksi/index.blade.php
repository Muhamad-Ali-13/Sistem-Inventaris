@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">transaksi</h3>
                    <div class="card-tools">
                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Transaksi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('transaksi.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by item or notes..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transaksi.index') }}">
                                <select name="type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="in" {{ request('tipe') == 'in' ? 'selected' : '' }}>Stock In</option>
                                    <option value="out" {{ request('tipe') == 'out' ? 'selected' : '' }}>Stock Out</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transaksi.index') }}">
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Pengguna</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah Harga</th>
                                    <th>Tipe</th>
                                    <th>Catatan</th>
                                    <th>Tanggal Trsansaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi as $transaction)
                                <tr>
                                    <td>{{ $transaction->Kode }}</td>
                                    <td>{{ $transaction->barang->nama }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->jumlah }}</td>
                                    <td>{{ number_format($transaction->harga_satuan, 2) }}</td>
                                    <td>{{ number_format($transaction->jumlah_harga, 2) }}</td>
                                    <td>
                                        @if($transaction->tipe == 'in')
                                            <span class="badge badge-success">IN</span>
                                        @else
                                            <span class="badge badge-danger">OUT</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($transaction->catatan, 50) }}</td>
                                    <td>{{ $transaction->tanggal_transaksi->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('transaksi.show', $transaction) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($transaksi->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $transaksi->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection