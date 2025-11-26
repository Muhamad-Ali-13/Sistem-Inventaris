@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Role</h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('roles.update', $role) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Role Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required 
                                   placeholder="Enter role name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Permissions *</label>
                            <div class="row">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <input type="checkbox" class="group-checkbox" data-group="{{ $group }}"
                                                           {{ $thisGroupAllSelected = true }}
                                                           @foreach($groupPermissions as $permission)
                                                               @if(!in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())))
                                                                   {{ $thisGroupAllSelected = false }}
                                                               @endif
                                                           @endforeach
                                                           {{ $thisGroupAllSelected ? 'checked' : '' }}>
                                                    <strong>{{ ucfirst($group) }} Management</strong>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->id }}"
                                                               id="permission_{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Group checkbox functionality
        $('.group-checkbox').change(function() {
            const group = $(this).data('group');
            const isChecked = $(this).is(':checked');
            
            $(`.permission-checkbox[name="permissions[]"]`).each(function() {
                if ($(this).closest('.card').find('.card-header').text().includes(group)) {
                    $(this).prop('checked', isChecked);
                }
            });
        });

        // Individual permission checkbox
        $('.permission-checkbox').change(function() {
            const group = $(this).closest('.card').find('.card-header').text().trim();
            const groupCheckbox = $(`.group-checkbox[data-group="${group.split(' ')[0].toLowerCase()}"]`);
            const totalPermissions = $(this).closest('.card-body').find('.permission-checkbox').length;
            const checkedPermissions = $(this).closest('.card-body').find('.permission-checkbox:checked').length;
            
            if (checkedPermissions === totalPermissions) {
                groupCheckbox.prop('checked', true);
            } else {
                groupCheckbox.prop('checked', false);
            }
        });
    });
</script>
@endpush