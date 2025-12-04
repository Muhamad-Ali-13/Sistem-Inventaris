@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Roles & Permissions</h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Role
             z           </a>
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
                                    <th>No</th>
                                    <th>Nama Role</th>
                                    <th>Permissions</th>
                                    <th>Jumlah Pengguna</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>
                                        <span class="badge badge-primary" style="font-size: 1em;">{{ $role->name }}</span>
                                    </td>
                                    <td>
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="badge badge-info">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="badge badge-secondary">+{{ $role->permissions->count() - 3 }} more</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-dark">{{ $role->users_count ?? $role->users->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($role->name !== 'super admin')
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" data-confirm="Delete this role?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        @endif
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