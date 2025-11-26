@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Vehicle</h3>
                    <div class="card-tools">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Vehicle Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $vehicle->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="license_plate">License Plate *</label>
                                    <input type="text" class="form-control @error('license_plate') is-invalid @enderror" 
                                           id="license_plate" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" required>
                                    @error('license_plate')
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
                                    <label for="type">Vehicle Type *</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                           id="type" name="type" value="{{ old('type', $vehicle->type) }}" required>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_consumption">Fuel Consumption (km/L)</label>
                                    <input type="number" class="form-control @error('fuel_consumption') is-invalid @enderror" 
                                           id="fuel_consumption" name="fuel_consumption" value="{{ old('fuel_consumption', $vehicle->fuel_consumption) }}" min="0" step="0.1">
                                    @error('fuel_consumption')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="last_maintenance">Last Maintenance Date</label>
                            <input type="date" class="form-control @error('last_maintenance') is-invalid @enderror" 
                                   id="last_maintenance" name="last_maintenance" value="{{ old('last_maintenance', $vehicle->last_maintenance ? $vehicle->last_maintenance->format('Y-m-d') : '') }}">
                            @error('last_maintenance')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" 
                                       {{ old('is_available', $vehicle->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Vehicle is available
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Vehicle</button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection