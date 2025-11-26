@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Item Name</th>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <th>Item Code</th>
                                    <td>{{ $item->code }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ $item->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $item->description ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Current Stock</th>
                                    <td>
                                        <span class="{{ $item->stock < $item->min_stock ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $item->stock }}
                                            @if($item->stock < $item->min_stock)
                                                <small class="text-danger">(Low Stock!)</small>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Minimum Stock</th>
                                    <td>{{ $item->min_stock }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $item->location ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Quick Actions</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">Edit Item</a>
                                    <a href="{{ route('transactions.create') }}?item_id={{ $item->id }}" class="btn btn-success">Add Transaction</a>
                                    <a href="{{ route('item-requests.create') }}?item_id={{ $item->id }}" class="btn btn-info">Request Item</a>
                                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to List</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection