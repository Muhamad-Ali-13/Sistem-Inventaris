@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Barang</h3>
                        <div class="card-tools">
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('barang.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="nama">Nama Barang *</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- <div class="form-group">
                                <label for="kode">Kode Barang</label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror"
                                    id="kode" name="kode" value="{{ $generatedKode }}" readonly>
                                @error('kode')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}

                            <div class="form-group">
                                <label for="kategori_id">Kategori *</label>
                                <select class="form-control @error('kategori_id') is-invalid @enderror" id="kategori_id"
                                    name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('kategori_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama }}
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
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stok">Stok *</label>
                                        <input type="number" class="form-control @error('stok') is-invalid @enderror"
                                            id="stok" name="stok" value="{{ old('stok', 0) }}" min="0"
                                            required>
                                        @error('stok')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stok_minimal">Minimum Stock *</label> {{-- Ubah id dan name --}}
                                        <input type="number"
                                            class="form-control @error('stok_minimal') is-invalid @enderror"
                                            id="stok_minimal" name="stok_minimal" value="{{ old('stok_minimal', 10) }}"
                                            min="0" required>
                                        @error('stok_minimal')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="harga">Harga Barang *</label>
                                        <input type="number" class="form-control @error('harga') is-invalid @enderror"
                                            id="harga" name="harga" value="{{ old('harga', 0) }}" min="0"
                                            required>
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
                                            id="lokasi" name="lokasi" value="{{ old('lokasi') }}">
                                        @error('lokasi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Tambah Barang</button>
                                <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
