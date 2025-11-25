@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Vehicles</h3>
                    <div class="card-tools">
                        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Vehicle
                        </a>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>License Plate</th>
                                    <th>Type</th>
                                    <th>Fuel Consumption</th>
                                    <th>Last Maintenance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->id }}</td>
                                    <td>{{ $vehicle->name }}</td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                    <td>{{ $vehicle->type }}</td>
                                    <td>{{ $vehicle->fuel_consumption ? $vehicle->fuel_consumption . ' km/L' : '-' }}</td>
                                    <td>{{ $vehicle->last_maintenance ? $vehicle->last_maintenance->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($vehicle->is_available)
                                            <span class="badge badge-success">Available</span>
                                        @else
                                            <span class="badge badge-danger">In Use</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline" data-confirm>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection