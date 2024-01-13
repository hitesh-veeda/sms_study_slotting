@extends('layouts.admin')
@section('title','All Roles')
@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Roles</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addRole') }}" class="headerButtonStyle" role="button" title="Add Role">
                                        Add Role
                                    </a>
                                @endif
                            </li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>     

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="datatable-buttons" class="table table-striped table-bordered datatable-search" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Name</th>
                                    <th>Modules</th>
                                    <th>Status</th>
                                    @if($admin == 'yes')
                                        <th class='notexport'>Actions</th>
                                    @else
                                        @if($access->edit != '' || $access->delete != '')
                                            <th class='notexport'>Actions</th>
                                        @endif
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @if(!is_null($data))
                                @foreach($data as $gk => $gv)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $gv->name }}</td>
                                        <td>
                                            @if(!is_null($gv->defined_module)) 
                                                @php $modules = []; @endphp
                                                @foreach($gv->defined_module as $dk => $dv)
                                                    @if(!is_null($dv->module_name) && $dv->module_name->name)
                                                        @php 
                                                            $modules[] = $dv->module_name->name;
                                                        @endphp
                                                    @endif    
                                                    
                                                @endforeach
                                                <p>{{ implode(' | ', $modules) }}</p>
                                            @endif
                                        </td>
                                        
                                        @php $checked = ''; @endphp
                                        @if($gv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                        <td>
                                            <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                <input class="form-check-input roleStatus" type="checkbox" id="customSwitch{{ $gk }}" value="1" data-id="{{ $gv->id }}" {{ $checked }}>
                                                <label class="form-check-label" for="customSwitch{{ $gk }}"></label>
                                            </div>
                                        </td>

                                        @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                            <td>
                                                <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{route('admin.editRole',$gv->id)}}" role="button">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{route('admin.deleteRole',$gv->id)}}" role="button" onclick="return confirm('Do you want to delete this role?');">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            </td>
                                        @else
                                            @if($access->edit != '')
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{route('admin.editRole',$gv->id)}}" role="button">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                </td>
                                            @endif
                                            @if($access->delete != '')
                                                <td>
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{route('admin.deleteRole',$gv->id)}}" role="button" onclick="return confirm('Do you want to delete this role?');">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection