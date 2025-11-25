@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction Details</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Transaction Code</th>
                            <td>{{ $transaction->code }}</td>
                        </tr>
                        <tr>
                            <th>Item</th>
                            <td>{{ $transaction->item->name }} ({{ $transaction->item->code }})</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $transaction->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <td>
                                <span class="badge {{ $transaction->type == 'in' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $transaction->quantity }} {{ $transaction->type == 'in' ? 'IN' : 'OUT' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Transaction Date</th>
                            <td>{{ $transaction->transaction_date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>{{ $transaction->notes }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $transaction->created_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection