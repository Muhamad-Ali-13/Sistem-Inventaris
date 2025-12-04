@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Vehicle Usage Request</h3>
                    <div class="card-tools">
                        <a href="{{ route('vehicle-usage.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(auth()->user()->hasRole(['super admin', 'admin']))
                    <form method="POST" action="{{ route('vehicle-usage.update', $vehicleUsage) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_id">Vehicle *</label>
                                    <select class="form-control @error('vehicle_id') is-invalid @enderror" 
                                            id="vehicle_id" name="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $vehicleUsage->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->name }} ({{ $vehicle->license_plate }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', $vehicleUsage->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date', $vehicleUsage->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose *</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                      id="purpose" name="purpose" rows="4" required>{{ old('purpose', $vehicleUsage->purpose) }}</textarea>
                            @error('purpose')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="pending" {{ old('status', $vehicleUsage->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $vehicleUsage->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $vehicleUsage->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="returned" {{ old('status', $vehicleUsage->status) == 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rejection_reason">Rejection Reason</label>
                            <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                      id="rejection_reason" name="rejection_reason" rows="3">{{ old('rejection_reason', $vehicleUsage->rejection_reason) }}</textarea>
                            @error('rejection_reason')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Request</button>
                            <a href="{{ route('vehicle-usage.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <p>You don't have permission to edit vehicle usage requests. Please contact administrator.</p>
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
    $(document).ready(function() {
        $('#start_date').change(function() {
            $('#end_date').attr('min', $(this).val());
        });
    });
</script>
@endpush