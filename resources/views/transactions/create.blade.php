@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Transaction</h3>
                    <div class="card-tools">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transactions.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_id">Item *</label>
                                    <select class="form-control @error('item_id') is-invalid @enderror" 
                                            id="item_id" name="item_id" required>
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" 
                                                    data-stock="{{ $item->stock }}"
                                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }} (Stock: {{ $item->stock }})
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Transaction Type *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
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
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted" id="stock-info">
                                        Available stock: <span id="available-stock">0</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="transaction_date">Transaction Date *</label>
                                    <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                           id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes *</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" required 
                                      placeholder="Enter transaction notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Transaction</button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
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