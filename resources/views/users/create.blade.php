@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah User</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}" id="userForm">
                        @csrf

                        <div class="form-group">
                            <label for="karyawan_id">Pilih Karyawan (Opsional untuk Admin/Super Admin)</label>
                            <select class="form-control select2 @error('karyawan_id') is-invalid @enderror"
                                id="karyawan_id" name="karyawan_id">
                                <option value="">Pilih Karyawan (Opsional)</option>
                                @foreach ($karyawan as $krywn)
                                    <option value="{{ $krywn->id }}" 
                                        data-nama="{{ $krywn->nama }}"
                                        data-email="{{ $krywn->email }}"
                                        data-jabatan="{{ $krywn->jabatan ?? '-' }}"
                                        data-department="{{ $krywn->department->nama ?? '-' }}"
                                        {{ old('karyawan_id') == $krywn->id ? 'selected' : '' }}>
                                        {{ $krywn->nama }} - {{ $krywn->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('karyawan_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Pilih karyawan jika ingin membuat user untuk karyawan. Biarkan kosong untuk Super Admin/Admin.
                            </small>
                        </div>

                        <!-- Informasi Karyawan Terpilih -->
                        <div id="karyawan-info" class="card mb-3" style="display: none;">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Karyawan</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Nama:</strong></td>
                                        <td id="info-nama">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td id="info-email">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jabatan:</strong></td>
                                        <td id="info-jabatan">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Departemen:</strong></td>
                                        <td id="info-department">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
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
                                    <label for="password">Password *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Konfirmasi Password *</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="roles">Role *</label>
                            <select class="form-control select2 @error('roles') is-invalid @enderror" id="roles"
                                name="roles[]" multiple="multiple" required style="width: 100%;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('roles')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Catatan:</strong> Role "karyawan" membutuhkan data karyawan.
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Buat User</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#karyawan_id').select2({
                placeholder: 'Pilih karyawan (opsional)',
                allowClear: true
            });

            $('#roles').select2({
                placeholder: 'Pilih role',
                allowClear: true
            });

            // Tampilkan informasi karyawan yang dipilih
            $('#karyawan_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                const karyawanId = selectedOption.val();

                if (karyawanId) {
                    // Tampilkan informasi karyawan
                    $('#karyawan-info').show();
                    $('#info-nama').text(selectedOption.data('nama'));
                    $('#info-email').text(selectedOption.data('email'));
                    $('#info-jabatan').text(selectedOption.data('jabatan'));
                    $('#info-department').text(selectedOption.data('department'));
                    
                    // Isi otomatis nama dan email
                    $('#name').val(selectedOption.data('nama'));
                    $('#email').val(selectedOption.data('email'));
                } else {
                    $('#karyawan-info').hide();
                    $('#name').val('');
                    $('#email').val('');
                }
            });

            // Validasi sebelum submit
            $('#userForm').submit(function(e) {
                const selectedRoles = $('#roles').val() || [];
                const karyawanId = $('#karyawan_id').val();
                
                // Cek apakah ada role 'karyawan' tapi tidak pilih karyawan
                if (selectedRoles.includes('karyawan') && !karyawanId) {
                    e.preventDefault();
                    alert('Role "karyawan" membutuhkan data karyawan. Silakan pilih karyawan atau hapus role "karyawan".');
                    return false;
                }
            });

            // Trigger change jika ada value yang sudah dipilih sebelumnya
            @if (old('karyawan_id'))
                $('#karyawan_id').trigger('change');
            @endif
        });
    </script>
@endpush