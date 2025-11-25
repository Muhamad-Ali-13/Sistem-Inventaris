@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicle Usage Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Vehicle</th>
                                    <td>{{ $vehicleUsage->vehicle->name }} ({{ $vehicleUsage->vehicle->license_plate }})</td>
                                </tr>
                                <tr>
                                    <th>Requested By</th>
                                    <td>{{ $vehicleUsage->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $vehicleUsage->start_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ $vehicleUsage->end_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ $vehicleUsage->start_date->diffInDays($vehicleUsage->end_date) + 1 }} days</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($vehicleUsage->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($vehicleUsage->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($vehicleUsage->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-info">Returned</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($vehicleUsage->approved_by)
                                <tr>
                                    <th>Approved By</th>
                                    <td>{{ $vehicleUsage->approver->name }}</td>
                                </tr>
                                <tr>
                                    <th>Approved At</th>
                                    <td>{{ $vehicleUsage->updated_at->format('d F Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($vehicleUsage->rejection_reason)
                                <tr>
                                    <th>Rejection Reason</th>
                                    <td>{{ $vehicleUsage->rejection_reason }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Purpose</h5>
                            <div class="border p-3 bg-light">
                                {{ $vehicleUsage->purpose }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('vehicle-usage.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            
                            @if(auth()->user()->hasRole(['super admin', 'admin']))
                                @if($vehicleUsage->status == 'pending')
                                    <form action="{{ route('vehicle-usage.approve', $vehicleUsage) }}" method="POST" class="d-inline" data-confirm="Approve this vehicle usage request?">
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

                                @if($vehicleUsage->status == 'approved')
                                    <form action="{{ route('vehicle-usage.return', $vehicleUsage) }}" method="POST" class="d-inline" data-confirm="Mark this vehicle as returned?">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-undo"></i> Mark Returned
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if(auth()->user()->hasRole(['super admin', 'admin']) && $vehicleUsage->status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Vehicle Usage Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('vehicle-usage.reject', $vehicleUsage) }}" method="POST">
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
@endsection