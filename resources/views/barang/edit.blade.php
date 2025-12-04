@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Barang</h3>
                        <div class="card-tools">
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('barang.update', $barang) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode">Kode Barang *</label>
                                        <input type="text" class="form-control @error('kode') is-invalid @enderror"
                                            id="kode" name="kode" value="{{ old('kode', $barang->kode) }}" required>
                                        @error('kode')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama">Nama Barang *</label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama" value="{{ old('nama', $barang->nama) }}" required>
                                        @error('nama')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="kategori_id">Kategori*</label>
                                <select class="form-control @error('kategori_id') is-invalid @enderror" id="kategori_id"
                                    name="kategori_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('kategori_id', $barang->kategori_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                                    rows="3">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stock">Stok Saat Ini *</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                            id="stock" name="stock" value="{{ old('stock', $barang->stock) }}"
                                            min="0" required>
                                        @error('stock')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="min_stock">Minimum Stock *</label>
                                        <input type="number" class="form-control @error('min_stock') is-invalid @enderror"
                                            id="min_stock" name="min_stock"
                                            value="{{ old('min_stock', $barang->min_stock) }}" min="0" required>
                                        @error('min_stock')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="harga">Harga</label>
                                        <input type="number" class="form-control @error('harga') is-invalid @enderror"
                                            id="harga" name="harga" value="{{ old('harga', $barang->harga) }}" min="0" step="0.01">
                                        @error('harga')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>  

                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lokasi">Lokasi</label>
                                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror"
                                            id="lokasi" name="lokasi" value="{{ old('lokasi', $barang->lokasi) }}">
                                        @error('lokasi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Perbarui Barang</button>
                                <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
