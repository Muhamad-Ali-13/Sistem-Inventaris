@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle Usage</h3>
                    <div class="card-tools">
                        @auth
                            @if(auth()->user()->hasRole(['super admin', 'admin']) || auth()->user()->hasRole('karyawan'))
                            <a href="{{ route('vehicle-usage.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Request Vehicle Usage
                            </a>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('vehicle-usage.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by purpose or vehicle..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('vehicle-usage.index') }}">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Vehicle</th>
                                    <th>User</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usages as $usage)
                                <tr>
                                    <td>{{ $usage->id }}</td>
                                    <td>{{ $usage->vehicle->name }} ({{ $usage->vehicle->license_plate }})</td>
                                    <td>{{ $usage->user->name }}</td>
                                    <td>{{ $usage->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $usage->end_date->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($usage->purpose, 50) }}</td>
                                    <td>
                                        @if($usage->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($usage->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($usage->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-info">Returned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('vehicle-usage.show', $usage) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @if(auth()->user()->hasRole(['super admin', 'admin']))
                                            <!-- Approval Actions -->
                                            @if($usage->status == 'pending')
                                                <form action="{{ route('vehicle-usage.approve', $usage) }}" method="POST" class="d-inline" data-confirm="Approve this vehicle usage request?">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $usage->id }}">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $usage->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Vehicle Usage Request</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('vehicle-usage.reject', $usage) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="rejection_reason">Rejection Reason</label>
                                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Enter reason for rejection..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger">Reject Request</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Return Action -->
                                            @if($usage->status == 'approved')
                                                <form action="{{ route('vehicle-usage.return', $usage) }}" method="POST" class="d-inline" data-confirm="Mark this vehicle as returned?">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-info btn-sm">
                                                        <i class="fas fa-undo"></i> Mark Returned
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Delete Action -->
                                            @if(in_array($usage->status, ['pending', 'rejected']))
                                                <form action="{{ route('vehicle-usage.destroy', $usage) }}" method="POST" class="d-inline" data-confirm="Delete this vehicle usage record?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <!-- User Actions -->
                                            @if($usage->status == 'pending' && $usage->user_id == auth()->id())
                                                <form action="{{ route('vehicle-usage.destroy', $usage) }}" method="POST" class="d-inline" data-confirm="Cancel this vehicle usage request?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($usages->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $usages->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge {
    font-size: 0.8em;
}
.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>
@endpush