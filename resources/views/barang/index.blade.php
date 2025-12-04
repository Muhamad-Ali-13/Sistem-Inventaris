@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Barang</h3>
                    <div class="card-tools">
                        <a href="{{ route('barang.create') }}" class="btn btn-primary">Add Barang</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('barang.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search barang..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="kategori" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="barang-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Stok Minimal</th>
                                    <th>Harga</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barang as $item)
                                <tr class="{{ $item->stok < $item->stok_minimal ? 'table-danger' : '' }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->kategori->nama }}</td>
                                    <td>
                                        <span class="{{ $item->stok < $item->stok_minimal ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $item->stok }}
                                            @if($item->stok < $item->stok_minimal)
                                                <small class="text-danger">(Low!)</small>
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $item->stok_minimal }}</td>
                                    <td>Rp. {{ number_format($item->harga, 2, ',', '.') ?? '-' }}</td>
                                    <td>{{ $item->lokasi ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('barang.show', $item) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('barang.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('barang.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $barang->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#barang-table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "ordering": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endpush