@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create User from Employee</h3>
                        <div class="card-tools">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="employee_id">Select Employee *</label>
                                <select class="form-control select2 @error('employee_id') is-invalid @enderror"
                                    id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                            data-email="{{ $employee->email }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} - {{ $employee->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    Only employees without user accounts are shown.
                                </small>
                            </div>

                            <!-- Display selected employee info -->
                            <div id="employee-info" class="card mb-3" style="display: none;">
                                <div class="card-body">
                                    <h6>Employee Information:</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td id="info-name">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td id="info-email">-</td>
                                        </tr>
                                        {{-- <tr>
                                            <td><strong>Position:</strong></td>
                                            <td id="info-position">-</td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <td><strong>Department:</strong></td>
                                            <td id="info-department">-</td>
                                        </tr> --}}
                                    </table>
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
                                        <label for="password_confirmation">Confirm Password *</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="roles">Roles *</label>
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
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create User</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
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
            $('#employee_id').select2({
                placeholder: 'Select an employee',
                allowClear: true
            });

            $('#roles').select2({
                placeholder: 'Select roles',
                allowClear: true
            });

            // Employee info display
            $('#employee_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                const employeeId = selectedOption.val();

                if (employeeId) {
                    // Show loading
                    $('#employee-info').show();
                    $('#info-name').text(selectedOption.data('name'));
                    $('#info-email').text(selectedOption.data('email'));

                    // You can fetch additional employee data via AJAX if needed
                    // For now, we'll just show basic info from data attributes
                    $('#info-position').text(selectedOption.text().match(/\((.*?)\)/)?.[1] || '-');
                    $('#info-department').text('-'); // You can add data-department attribute if needed
                } else {
                    $('#employee-info').hide();
                }
            });

            // Trigger change on page load if there's a selected value
            @if (old('employee_id'))
                $('#employee_id').trigger('change');
            @endif
        });
    </script>
@endpush
