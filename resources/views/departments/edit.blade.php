@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Departemen</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('departments.update', $department) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="nama">Nama Departemen *</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" nama="nama" value="{{ old('nama', $department->nama) }}" required>
                                @error('nama')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" nama="deskripsi"
                                    rows="3">{{ old('deskripsi', $department->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('departments.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
