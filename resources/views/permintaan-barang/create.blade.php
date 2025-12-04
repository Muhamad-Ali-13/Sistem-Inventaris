@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Permintaan Barang</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('permintaan-barang.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="barang_id">Barang *</label>
                                <select class="form-control @error('barang_id') is-invalid @enderror" id="barang_id"
                                    name="barang_id" required>
                                    <option value="">Pilih Barang</option>
                                    @foreach ($barang as $item)
                                        <option value="{{ $item->id }}" data-stok="{{ $item->stok }}"
                                            {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }} (Stok: {{ $item->stok }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('barang_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted" id="stock-info">
                                    Pilih barang untuk melihat stok yang tersedia
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah *</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                    id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="1" required>
                                @error('jumlah')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted" id="quantity-info">
                                    Masukkan jumlah yang dibutuhkan
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="tujuan">Tujuan *</label>
                                <textarea class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" rows="4"
                                    placeholder="Berikan alasan mengapa Anda membutuhkan barang ini..." required>{{ old('tujuan') }}</textarea>
                                @error('tujuan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Berikan penjelasan rinci mengapa Anda membutuhkan barang ini
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi Permintaan</h6>
                                <ul class="mb-0">
                                    <li>Permintaan Anda akan ditinjau oleh admin</li>
                                    <li>Anda akan diberitahu saat permintaan diproses</li>
                                    <li>Anda dapat membatalkan permintaan yang masih pending kapan saja</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-paper-plane"></i> Kirim Permintaan
                                </button>
                                <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Barang Tersedia</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach ($barang->take(10) as $item)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $item->nama }}</h6>
                                        <small class="text-{{ $item->stok > 0 ? 'success' : 'danger' }}">
                                            {{ $item->stok }} stok
                                        </small>
                                    </div>
                                    <p class="mb-1 small text-muted">{{ $item->kategori->nama }}</p>
                                    <small class="text-muted">Kode: {{ $item->kode }}</small>
                                </div>
                            @endforeach
                        </div>
                        @if ($barang->count() > 10)
                            <div class="text-center mt-2">
                                <small class="text-muted">dan {{ $barang->count() - 10 }} barang lainnya...</small>
                            </div>
                        @endif
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
            const stockInfo = document.getElementById('stock-info');
            const quantityInfo = document.getElementById('quantity-info');
            const submitBtn = document.getElementById('submit-btn');

            function updateStockInfo() {
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                const stok = selectedOption ? parseInt(selectedOption.getAttribute('data-stok')) : 0;

                if (selectedOption && selectedOption.value) {
                    stockInfo.textContent = `Stok tersedia: ${stok}`;
                    stockInfo.className = `form-text text-${stok > 0 ? 'success' : 'danger'}`;

                    // Update quantity max value
                    quantityInput.max = stok;

                    if (stok === 0) {
                        quantityInput.disabled = true;
                        quantityInfo.textContent = 'Barang ini habis stok';
                        quantityInfo.className = 'form-text text-danger';
                        submitBtn.disabled = true;
                    } else {
                        quantityInput.disabled = false;
                        quantityInfo.textContent = `Anda dapat meminta hingga ${stok} barang`;
                        quantityInfo.className = 'form-text text-success';
                        submitBtn.disabled = false;
                    }
                } else {
                    stockInfo.textContent = 'Pilih barang untuk melihat stok yang tersedia';
                    stockInfo.className = 'form-text text-muted';
                    quantityInput.disabled = true;
                    quantityInfo.textContent = 'Pilih barang terlebih dahulu';
                    quantityInfo.className = 'form-text text-muted';
                    submitBtn.disabled = false;
                }
            }

            function validateQuantity() {
                const selectedOption = barangSelect.options[barangSelect.selectedIndex];
                const stok = selectedOption ? parseInt(selectedOption.getAttribute('data-stok')) : 0;
                const quantity = parseInt(quantityInput.value) || 0;

                if (quantity > stok) {
                    quantityInfo.textContent = `Tidak dapat meminta lebih dari stok yang tersedia (${stok})`;
                    quantityInfo.className = 'form-text text-danger';
                    submitBtn.disabled = true;
                } else if (quantity <= 0) {
                    quantityInfo.textContent = 'Masukkan jumlah yang valid';
                    quantityInfo.className = 'form-text text-danger';
                    submitBtn.disabled = true;
                } else {
                    quantityInfo.textContent = `Anda dapat meminta hingga ${stok} barang`;
                    quantityInfo.className = 'form-text text-success';
                    submitBtn.disabled = false;
                }
            }

            barangSelect.addEventListener('change', updateStockInfo);
            quantityInput.addEventListener('input', validateQuantity);

            // Initialize on page load
            updateStockInfo();
        });
    </script>
@endpush