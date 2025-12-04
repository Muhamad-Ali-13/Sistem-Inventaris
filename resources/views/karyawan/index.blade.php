@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Karyawan</h3>
                    <div class="card-tools">
                        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Karyawan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" data-datatable>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Departemen</th>
                                    <th>Jabatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($karyawan as $karyawan)
                                <tr>
                                    <td>{{ $karyawan->id }}</td>
                                    <td>{{ $karyawan->nama }}</td>
                                    <td>{{ $karyawan->email }}</td>
                                    <td>{{ $karyawan->telepon }}</td>
                                    <td>{{ $karyawan->department->nama }}</td>
                                    <td>{{ $karyawan->jabatan }}</td>
                                    <td>
                                        <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" class="d-inline" data-confirm>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
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