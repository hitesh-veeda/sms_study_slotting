@extends('layouts.admin')
@section('title','All Team Members')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Team Members</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addTeamMember') }}" class="headerButtonStyle" role="button" title="Add Team Member">
                                        Add Team Member
                                    </a>
                                @endif
                            </li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="accordion" id="accordionExample">
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-medium @if(isset($filter) && ($filter == 1)) @else collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#studyCollapseFilter" aria-expanded="false" aria-controls="studyCollapseFilter">
                        Filters
                    </button>
                </h2>
                <div id="studyCollapseFilter" class="accordion-collapse @if(isset($filter) && ($filter == 1)) @else collapse @endif" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body collapse show">
                        <form method="post" action="{{ route('admin.teamMemberList') }}">
                            @csrf
                            <div class="row">  

                                <div class="col-md-4">
                                    <label class="control-label">Status</label>
                                    <select class="form-select" name="status" style="width: 100%;">
                                        <option value="">All</option>
                                        <option value="1" @if($status == 1) selected="selected" @endif>Active</option>
                                        <option value="0" @if($status == 0 && $status != '') selected="selected" @endif>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="control-label">Role</label>
                                    <select class="form-select select2" name="role" style="width: 100%;">
                                        <option value="">Select Role</option>
                                        @if(!is_null($roles))
                                            @foreach($roles as $rk => $rv)
                                                <option @if($roleId == $rv->id) selected @endif value="{{ $rv->id }}">{{ $rv->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <div class="col-md-2 mt-4">
                                    <button type="submit" class="btn btn-primary vendors save_button mt-1">Submit</button>
                                </div>
                                @if(isset($filter) && ($filter == 1))
                                    <div class="col-md-2 mt-4">
                                        <a href="{{ route('admin.teamMemberList') }}" class="btn btn-danger mt-1 cancel_button" id="filter" name="save_and_list" value="save_and_list" style="margin-left:-90px">Reset</a>
                                    </div>
                                @endif
                            </div>
                        </form>
                       
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="datatable-buttons" class="table table-striped table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Location</th>
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
                                @if(!is_null($members))
                                    @foreach($members as $mk => $mv)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $mv->name }}</td>
                                            <td>
                                                {{ (!is_null($mv->role) && $mv->role->name != '') ? $mv->role->name : '---' }}
                                            </td>
                                            <td>{{ $mv->email }}</td>
                                            <td>
                                                {{ (!is_null($mv->location) && (($mv->location->location_name != '') && ($mv->location->location_type != '')) ) ? $mv->location->location_name.' - '.$mv->location->location_type : '---' }}
                                            </td>

                                            @php $checked = ''; @endphp
                                            @if($mv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                            <td>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input class="form-check-input teamMemberStatus" type="checkbox" id="customSwitch{{ $mk }}" value="1" data-id="{{ $mv->id }}" {{ $checked }}>
                                                    <label class="form-check-label" for="customSwitch{{ $mk }}"></label>
                                                </div>
                                            </td>

                                            @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editTeamMember',base64_encode($mv->id)) }}" role="button">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteTeamMember',base64_encode($mv->id)) }}" role="button" onclick="return confirm('Do you want to delete this team member?');">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if($access->edit != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editTeamMember',base64_encode($mv->id)) }}" role="button">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->delete != '')
                                                    <td>
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteTeamMember',base64_encode($mv->id)) }}" role="button" onclick="return confirm('Do you want to delete this team member?');">
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