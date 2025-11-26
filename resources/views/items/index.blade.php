@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Items</h3>
                    <div class="card-tools">
                        <a href="{{ route('items.create') }}" class="btn btn-primary">Add Item</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('items.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('items.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="items-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Min Stock</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="{{ $item->stock < $item->min_stock ? 'table-danger' : '' }}">
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>
                                        <span class="{{ $item->stock < $item->min_stock ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $item->stock }}
                                            @if($item->stock < $item->min_stock)
                                                <small class="text-danger">(Low!)</small>
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $item->min_stock }}</td>
                                    <td>{{ $item->location ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#items-table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "ordering": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endpush