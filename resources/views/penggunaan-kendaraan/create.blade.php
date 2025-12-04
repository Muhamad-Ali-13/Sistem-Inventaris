@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Penggunaan Kendaraan</h3>
                    <div class="card-tools">
                        <a href="{{ route('penggunaan-kendaraan.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('penggunaan-kendaraan.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="kendaraan_id">Kendaraan *</label>
                                    <select class="form-control @error('kendaraan_id') is-invalid @enderror" 
                                            id="kendaraan_id" name="kendaraan_id" required>
                                        <option value="">Pilih Kendaraan</option>
                                        @foreach($kendaraan as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('kendaraan_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->nama }} ({{ $vehicle->plat_nomor }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kendaraan_id')
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
                                    <label for="tanggal_mulai">Tanggal Mulai *</label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                                    @error('tanggal_mulai')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai *</label>
                                    <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                           id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                                    @error('tanggal_selesai')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tujuan">Tujuan *</label>
                            <textarea class="form-control @error('tujuan') is-invalid @enderror" 
                                      id="tujuan" name="tujuan" rows="4" required 
                                      placeholder="Please describe the purpose of vehicle usage...">{{ old('tujuan') }}</textarea>
                            @error('tujuan')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit Permintaan</button>
                            <a href="{{ route('penggunaan-kendaraan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const today = new Date().toISOString().split('T')[0];
        $('#start_date').attr('min', today);
        
        $('#start_date').change(function() {
            $('#end_date').attr('min', $(this).val());
        });
    });
</script>
@endpush