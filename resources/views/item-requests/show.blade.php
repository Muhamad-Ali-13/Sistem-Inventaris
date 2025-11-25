@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Request Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Request ID</th>
                                    <td>#{{ $itemRequest->id }}</td>
                                </tr>
                                <tr>
                                    <th>Item</th>
                                    <td>
                                        {{ $itemRequest->item->name }}
                                        <br>
                                        <small class="text-muted">Code: {{ $itemRequest->item->code }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $itemRequest->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td>{{ $itemRequest->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Current Stock</th>
                                    <td>
                                        @if($itemRequest->item->stock < $itemRequest->item->min_stock)
                                            <span class="badge badge-danger">{{ $itemRequest->item->stock }} (Low Stock)</span>
                                        @else
                                            <span class="badge badge-success">{{ $itemRequest->item->stock }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($itemRequest->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($itemRequest->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @else
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Request Date</th>
                                    <td>{{ $itemRequest->created_at->format('d F Y H:i') }}</td>
                                </tr>
                                @if($itemRequest->approved_by)
                                <tr>
                                    <th>Processed By</th>
                                    <td>{{ $itemRequest->approver->name }}</td>
                                </tr>
                                <tr>
                                    <th>Processed At</th>
                                    <td>{{ $itemRequest->updated_at->format('d F Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($itemRequest->rejection_reason)
                                <tr>
                                    <th>Rejection Reason</th>
                                    <td>{{ $itemRequest->rejection_reason }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Purpose</h5>
                            <div class="border p-3 bg-light rounded">
                                {{ $itemRequest->purpose }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('item-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            
                            @if(auth()->user()->hasRole(['super admin', 'admin']))
                                @if($itemRequest->status == 'pending')
                                    <form action="{{ route('item-requests.approve', $itemRequest) }}" method="POST" class="d-inline" data-confirm="Approve this item request?">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                @endif
                            @else
                                @if($itemRequest->status == 'pending' && $itemRequest->user_id == auth()->id())
                                    <form action="{{ route('item-requests.destroy', $itemRequest) }}" method="POST" class="d-inline" data-confirm="Cancel this item request?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Cancel Request
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h4">{{ $itemRequest->item->name }}</div>
                        <div class="text-muted">{{ $itemRequest->item->category->name }}</div>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Code:</strong></td>
                            <td>{{ $itemRequest->item->code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Stock:</strong></td>
                            <td>
                                <span class="badge badge-{{ $itemRequest->item->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $itemRequest->item->stock }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Minimum Stock:</strong></td>
                            <td>{{ $itemRequest->item->min_stock }}</td>
                        </tr>
                        <tr>
                            <td><strong>Location:</strong></td>
                            <td>{{ $itemRequest->item->location ?? '-' }}</td>
                        </tr>
                    </table>

                    @if($itemRequest->item->description)
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p class="text-muted small">{{ $itemRequest->item->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if(auth()->user()->hasRole(['super admin', 'admin']) && $itemRequest->status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Item Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('item-requests.reject', $itemRequest) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason *</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required placeholder="Please provide a reason for rejecting this request..."></textarea>
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
@endsection