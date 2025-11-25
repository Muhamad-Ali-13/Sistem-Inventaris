@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Item Request</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('item-requests.store') }}">
                        @csrf
                        
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
                            <small class="form-text text-muted" id="stock-info">
                                Select an item to see available stock
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity *</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity') }}" 
                                   min="1" required>
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted" id="quantity-info">
                                Enter the quantity you need
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose *</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                      id="purpose" name="purpose" rows="4" 
                                      placeholder="Please describe the purpose of this request..." 
                                      required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Provide a detailed explanation of why you need these items
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Request Information</h6>
                            <ul class="mb-0">
                                <li>Your request will be reviewed by admin</li>
                                <li>You will be notified when the request is processed</li>
                                <li>You can cancel pending requests anytime</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                            <a href="{{ route('item-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Items</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($items->take(10) as $item)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $item->name }}</h6>
                                <small class="text-{{ $item->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $item->stock }} in stock
                                </small>
                            </div>
                            <p class="mb-1 small text-muted">{{ $item->category->name }}</p>
                            <small class="text-muted">Code: {{ $item->code }}</small>
                        </div>
                        @endforeach
                    </div>
                    @if($items->count() > 10)
                    <div class="text-center mt-2">
                        <small class="text-muted">and {{ $items->count() - 10 }} more items...</small>
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
    const itemSelect = document.getElementById('item_id');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stock-info');
    const quantityInfo = document.getElementById('quantity-info');
    const submitBtn = document.getElementById('submit-btn');

    function updateStockInfo() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const stock = selectedOption ? parseInt(selectedOption.getAttribute('data-stock')) : 0;
        
        if (selectedOption && selectedOption.value) {
            stockInfo.textContent = `Available stock: ${stock}`;
            stockInfo.className = `form-text text-${stock > 0 ? 'success' : 'danger'}`;
            
            // Update quantity max value
            quantityInput.max = stock;
            
            if (stock === 0) {
                quantityInput.disabled = true;
                quantityInfo.textContent = 'This item is out of stock';
                quantityInfo.className = 'form-text text-danger';
                submitBtn.disabled = true;
            } else {
                quantityInput.disabled = false;
                quantityInfo.textContent = `You can request up to ${stock} items`;
                quantityInfo.className = 'form-text text-success';
                submitBtn.disabled = false;
            }
        } else {
            stockInfo.textContent = 'Select an item to see available stock';
            stockInfo.className = 'form-text text-muted';
            quantityInput.disabled = true;
            quantityInfo.textContent = 'Select an item first';
            quantityInfo.className = 'form-text text-muted';
            submitBtn.disabled = false;
        }
    }

    function validateQuantity() {
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const stock = selectedOption ? parseInt(selectedOption.getAttribute('data-stock')) : 0;
        const quantity = parseInt(quantityInput.value) || 0;

        if (quantity > stock) {
            quantityInfo.textContent = `Cannot request more than available stock (${stock})`;
            quantityInfo.className = 'form-text text-danger';
            submitBtn.disabled = true;
        } else if (quantity <= 0) {
            quantityInfo.textContent = 'Please enter a valid quantity';
            quantityInfo.className = 'form-text text-danger';
            submitBtn.disabled = true;
        } else {
            quantityInfo.textContent = `You can request up to ${stock} items`;
            quantityInfo.className = 'form-text text-success';
            submitBtn.disabled = false;
        }
    }

    itemSelect.addEventListener('change', updateStockInfo);
    quantityInput.addEventListener('input', validateQuantity);

    // Initialize on page load
    updateStockInfo();
});
</script>
@endpush