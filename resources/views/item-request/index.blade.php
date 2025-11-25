@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Requests</h3>
                    <div class="card-tools">
                        @auth
                            @if(auth()->user()->hasRole(['super admin', 'admin']) || auth()->user()->hasRole('karyawan'))
                            <a href="{{ route('item-requests.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> New Request
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
                            <form method="GET" action="{{ route('item-requests.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by item or purpose..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('item-requests.index') }}">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item</th>
                                    <th>User</th>
                                    <th>Quantity</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Request Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>
                                        {{ $request->item->name }}
                                        <br>
                                        <small class="text-muted">Stock: {{ $request->item->stock }}</small>
                                    </td>
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>{{ Str::limit($request->purpose, 50) }}</td>
                                    <td>
                                        @if($request->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('item-requests.show', $request) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @if(auth()->user()->hasRole(['super admin', 'admin']))
                                            <!-- Approval Actions -->
                                            @if($request->status == 'pending')
                                                <form action="{{ route('item-requests.approve', $request) }}" method="POST" class="d-inline" data-confirm="Approve this item request?">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $request->id }}">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Item Request</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('item-requests.reject', $request) }}" method="POST">
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
                                        @else
                                            <!-- User Actions -->
                                            @if($request->status == 'pending' && $request->user_id == auth()->id())
                                                <form action="{{ route('item-requests.destroy', $request) }}" method="POST" class="d-inline" data-confirm="Cancel this item request?">
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
                    @if($requests->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->links() }}
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