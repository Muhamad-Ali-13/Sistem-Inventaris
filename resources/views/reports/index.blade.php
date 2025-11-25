@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reports</h3>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('reports.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="start_date" class="mr-2">Start Date:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="end_date" class="mr-2">End Date:</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('reports.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $transactionSummary['total_transactions'] }}</h3>
                                    <p>Total Transactions</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $transactionSummary['total_in'] }}</h3>
                                    <p>Total Stock In</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $transactionSummary['total_out'] }}</h3>
                                    <p>Total Stock Out</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $requestSummary['total_requests'] }}</h3>
                                    <p>Total Item Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Requests Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Item Requests Summary</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pending</span>
                                                    <span class="info-box-number">{{ $requestSummary['pending'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Approved</span>
                                                    <span class="info-box-number">{{ $requestSummary['approved'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rejected</span>
                                                    <span class="info-box-number">{{ $requestSummary['rejected'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Vehicle Usage Summary</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Pending</span>
                                                    <span class="info-box-number">{{ $vehicleSummary['pending'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Approved</span>
                                                    <span class="info-box-number">{{ $vehicleSummary['approved'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Rejected</span>
                                                    <span class="info-box-number">{{ $vehicleSummary['rejected'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon"><i class="fas fa-undo"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Returned</span>
                                                    <span class="info-box-number">{{ $vehicleSummary['returned'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Most Requested Items</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Category</th>
                                                    <th>Total Requests</th>
                                                    <th>Current Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($popularItems as $item)
                                                <tr>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->category->name }}</td>
                                                    <td>{{ $item->request_count }}</td>
                                                    <td>
                                                        @if($item->stock < $item->min_stock)
                                                            <span class="badge badge-danger">{{ $item->stock }} (Low Stock)</span>
                                                        @else
                                                            <span class="badge badge-success">{{ $item->stock }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No data available</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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

@push('styles')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: 0.5rem;
    position: relative;
    width: 100%;
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box .info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
}
.info-box .info-box-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 0.875rem;
}
.info-box .info-box-number {
    display: block;
    font-weight: bold;
    font-size: 1.5rem;
}
</style>
@endpush