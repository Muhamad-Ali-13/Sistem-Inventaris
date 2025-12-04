@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Transaksi</h3>
                        <div class="card-tools">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('transaksi.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">Kode Transaksi *</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code" value="{{ old('code') }}" readonly required>
                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="item_id">Barang *</label>
                                        <select class="form-control @error('item_id') is-invalid @enderror" id="item_id"
                                            name="item_id" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barangs as $b)
                                                <option value="{{ $b->id }}" data-stock="{{ $b->stok }}"
                                                    {{ old('item_id') == $b->id ? 'selected' : '' }}>
                                                    {{ $b->nama }} (Stock: {{ $b->stok }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('item_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Tipe Transaksi *</label>
                                        <select class="form-control @error('type') is-invalid @enderror" id="type"
                                            name="type" required>
                                            <option value="">Pilih Tipe</option>
                                            <option value="in" {{ old('tipe') == 'in' ? 'selected' : '' }}>Stock In
                                            </option>
                                            <option value="out" {{ old('tipe') == 'out' ? 'selected' : '' }}>Stock Out
                                            </option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="harga_satuan">Harga Barang *</label>
                                        <input type="number"
                                            class="form-control @error('harga_satuan') is-invalid @enderror"
                                            id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan', 1) }}"
                                            min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantity">Jumlah *</label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                            id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1"
                                            required>
                                        @error('quantity')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted" id="stock-info">
                                            Stok Tersedia: <span id="available-stock">0</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jumlah_harga">Jumlah Harga *</label>
                                        <input type="number"
                                            class="form-control @error('jumlah_harga') is-invalid @enderror"
                                            id="jumlah_harga" name="jumlah_harga" value="{{ old('jumlah_harga', 1) }}"
                                            min="1" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="transaction_date">Tanggal Transaksi *</label>
                                        <input type="date"
                                            class="form-control @error('transaction_date') is-invalid @enderror"
                                            id="transaction_date" name="transaction_date"
                                            value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                        @error('transaction_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Catatan *</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    required placeholder="Masukkan catatan transaksi...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Buat Transaksi</button>
                                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
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
            function updateStockInfo() {
                const selectedItem = $('#item_id option:selected');
                const stock = selectedItem.data('stock') || 0;
                const type = $('#type').val();

                $('#available-stock').text(stock);

                if (type === 'out' && stock === 0) {
                    $('#stock-info').addClass('text-danger');
                } else {
                    $('#stock-info').removeClass('text-danger');
                }
            }

            $('#item_id, #type').change(updateStockInfo);
            updateStockInfo();
        });
    </script>
@endpush
