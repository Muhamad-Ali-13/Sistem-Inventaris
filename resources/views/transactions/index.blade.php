@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transactions</h3>
                    <div class="card-tools">
                        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Transaction
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('transactions.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by item or notes..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transactions.index') }}">
                                <select name="type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('transactions.index') }}">
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Item</th>
                                    <th>User</th>
                                    <th>Quantity</th>
                                    <th>Type</th>
                                    <th>Notes</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->code }}</td>
                                    <td>{{ $transaction->item->name }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->quantity }}</td>
                                    <td>
                                        @if($transaction->type == 'in')
                                            <span class="badge badge-success">IN</span>
                                        @else
                                            <span class="badge badge-danger">OUT</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($transaction->notes, 50) }}</td>
                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($transactions->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $transactions->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection