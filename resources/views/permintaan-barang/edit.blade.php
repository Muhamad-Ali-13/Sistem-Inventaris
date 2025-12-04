@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Permintaan Barang</h3>
                </div>
                <div class="card-body">
                    @if ($permintaanBarang->status != 'pending')
                        <div class="alert alert-danger">
                            Permintaan tidak dapat diedit karena sudah diproses.
                        </div>
                        <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    @else
                        <form method="POST" action="{{ route('permintaan-barang.update', $permintaanBarang) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="barang_id">Barang *</label>
                                <select class="form-control @error('barang_id') is-invalid @enderror" id="barang_id" name="barang_id" required>
                                    <option value="">Pilih Barang</option>
                                    @foreach($barang as $item)
                                        <option value="{{ $item->id }}" data-stok="{{ $item->stok }}" {{ old('barang_id', $permintaanBarang->barang_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }} (Stok: {{ $item->stok }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('barang_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah *</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $permintaanBarang->jumlah) }}" min="1" required>
                                @error('jumlah')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted" id="quantity-info">
                                    Stok tersedia: <span id="available-stock">0</span>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="tujuan">Tujuan *</label>
                                <textarea class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" rows="3" required>{{ old('tujuan', $permintaanBarang->tujuan) }}</textarea>
                                @error('tujuan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan (Opsional)</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $permintaanBarang->keterangan) }}</textarea>
                                @error('keterangan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Update Permintaan</button>
                                <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Catatan</h6>
                        <ul class="mb-0">
                            <li>Anda hanya dapat mengedit permintaan yang statusnya masih <strong>Pending</strong>.</li>
                            <li>Pastikan jumlah yang diminta tidak melebihi stok yang tersedia.</li>
                            <li>Setelah diupdate, permintaan akan kembali menunggu persetujuan admin.</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
                        <p class="mb-0">Mengubah barang atau jumlah akan mempengaruhi perhitungan harga secara otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const barangSelect = document.getElementById('barang_id');
        const quantityInput = document.getElementById('jumlah');
        const availableStockSpan = document.getElementById('available-stock');
        const submitBtn = document.getElementById('submit-btn');

        function updateStockInfo() {
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            const stok = selectedOption ? parseInt(selectedOption.getAttribute('data-stok')) : 0;
            availableStockSpan.textContent = stok;

            // Validasi jumlah
            const quantity = parseInt(quantityInput.value) || 0;
            if (quantity > stok) {
                availableStockSpan.className = 'text-danger';
                submitBtn.disabled = true;
            } else {
                availableStockSpan.className = 'text-success';
                submitBtn.disabled = false;
            }
        }

        barangSelect.addEventListener('change', updateStockInfo);
        quantityInput.addEventListener('input', updateStockInfo);

        // Initialize
        updateStockInfo();
    });
</script>
@endpush