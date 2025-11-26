@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Profile</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password">
                            <small class="form-text text-muted">
                                Fill this only if you want to change your password.
                            </small>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password">
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="new_password_confirmation" name="new_password_confirmation">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ auth()->user()->name }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Roles:</strong>
                        </div>
                        <div class="col-sm-8">
                            @foreach (auth()->user()->getRoleNames() as $role)
                                <span class="badge badge-primary">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <strong>Member Since:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ auth()->user()->created_at->format('d F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Quick Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $itemRequestsCount ?? auth()->user()->itemRequests()->count() }}</h3>
                                    <p>Item Requests</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $vehicleUsageCount ?? auth()->user()->vehicleUsage()->count() }}</h3>
                                    <p>Vehicle Usage</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-car"></i>
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
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
}
.small-box > .inner {
    padding: 10px;
}
.small-box h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px;
    padding: 0;
    white-space: nowrap;
}
.small-box p {
    font-size: 1rem;
}
.small-box .icon {
    color: rgba(0,0,0,.15);
    z-index: 0;
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 70px;
    transition: all .3s linear;
}
</style>
@endpush