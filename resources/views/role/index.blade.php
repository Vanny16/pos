@extends('layouts.master')

@section('title')
    User List
@endsection

@section('breadcrumb')
    @parent
    <li class="active">User List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#role-form-modal">
                    <i class="fa fa-plus-circle"></i> Add New User Role
                </button>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center"  width="15%">#</th>
                            <th >Role</th>
                            <th class="text-center" width="15%">Status</th>
                            <th class="text-center" width="15%"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $count = 1;
                        @endphp
                        @if($roles != null)
                            @forelse($roles as $role)
                                <tr>
                                    <td class="text-center">{{ $count++ }}</td>
                                    <td>{{ $role->role_name }}</td>
                                    <td class="text-center">
                                        @if ($role->role_status == 1)
                                            <button class="btn btn-success">Active</button>
                                        @else
                                            <button class="btn btn-danger">Inactive</button>
                                        @endif
                                    </td>
                                    <td class="text-center"><div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-flat update-btn" data-toggle="modal" data-target="#update-form-modal-{{ $role->role_id }}"><i class="fa fa-pencil"></i></button>
                                        {{-- <button type="button" class="btn btn-danger btn-flat" style="margin-left: 5px;"><i class="fa fa-trash"></i></button> --}}
                                    </div>
                                </td>

                                <div class="modal fade" id="update-form-modal-{{ $role->role_id }}" tabindex="-1" role="dialog" aria-labelledby="role-form-modal-label">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('user.update_role') }}" method="post" class="form-horizontal">
                                            @csrf
                                            @method('post')
                                            <div class="modal-content">
                                                <div class="modal-header bg-green">
                                                    <h4 class="modal-title">Update Role</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="role_id" id="role_id">
                                                    <div class="form-group row">
                                                        <label for="role_name" class="col-lg-3 col-lg-offset-1 control-label" >Role Name</label>
                                                        <div class="col-lg-6">
                                                            <input type="hidden" name="role_id" id="role_id" class="form-control" value="{{ $role->role_id }}"  required autofocus>

                                                            <input type="text" name="role_name" id="role_name" class="form-control" value="{{ $role->role_name }}"  required autofocus>
                                                            <span class="help-block with-errors"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-sm btn-flat btn-success"><i class="fa fa-save"></i> Save</button>
                                                    <button type="button" class="btn btn-sm btn-flat btn-danger" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No roles found</td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4">No roles found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="modal fade" id="role-form-modal" tabindex="-1" role="dialog" aria-labelledby="role-form-modal-label">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('user.role') }}" method="post" class="form-horizontal">
                            @csrf
                            @method('post')
                            <div class="modal-content">
                                <div class="modal-header bg-green">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Add new Role</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="name" class="col-lg-3 col-lg-offset-1 control-label">Role Name</label>
                                        <div class="col-lg-6">
                                            <input type="text" name="role_name" id="role_name" class="form-control" required autofocus>
                                            <span class="help-block with-errors"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-sm btn-flat btn-success"><i class="fa fa-save"></i> Save</button>
                                    <button type="button" class="btn btn-sm btn-flat btn-danger" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>

@endsection
