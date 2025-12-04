@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Kendaraan</h3>
                    <div class="card-tools">
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kendaraan.update', $kendaraan) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Kendaraan *</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                           id="nama" name="nama" value="{{ old('nama', $kendaraan->nama) }}" required>
                                    @error('nama')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plat_nomor">Plat Nomor *</label>
                                    <input type="text" class="form-control @error('plat_nomor') is-invalid @enderror" 
                                           id="plat_nomor" name="plat_nomor" value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}" required>
                                    @error('plat_nomor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipe">Tipe Kendaraan *</label>
                                    <input type="text" class="form-control @error('tipe') is-invalid @enderror" 
                                           id="tipe" name="tipe" value="{{ old('tipe', $kendaraan->tipe) }}" required>
                                    @error('tipe')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga">Harga (Rp) *</label>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                           id="harga" name="harga" value="{{ old('harga', $kendaraan->harga) }}" required min="0">
                                    @error('harga')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="konsumsi_bahan_bakar">Konsumsi Bahan Bakar (km/L)</label>
                                    <input type="number" class="form-control @error('konsumsi_bahan_bakar') is-invalid @enderror" 
                                           id="konsumsi_bahan_bakar" name="konsumsi_bahan_bakar" 
                                           value="{{ old('konsumsi_bahan_bakar', $kendaraan->konsumsi_bahan_bakar) }}" 
                                           min="0" step="0.1">
                                    @error('konsumsi_bahan_bakar')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="perawatan_terakhir">Perawatan Terakhir</label>
                                    <input type="date" class="form-control @error('perawatan_terakhir') is-invalid @enderror" 
                                           id="perawatan_terakhir" name="perawatan_terakhir" 
                                           value="{{ old('perawatan_terakhir', $kendaraan->perawatan_terakhir ? \Carbon\Carbon::parse($kendaraan->perawatan_terakhir)->format('Y-m-d') : '') }}">
                                    @error('perawatan_terakhir')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tersedia" name="tersedia" 
                                       value="1" {{ old('tersedia', $kendaraan->tersedia) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tersedia">
                                    Kendaraan Tersedia
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Perbarui Kendaraan</button>
                            <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection