@extends('layouts.admin')
@section('title','Reason Master')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Reason Masters</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addReasonMaster') }}" class="headerButtonStyle" role="button" title="Add Para Master">
                                        Add Reason Master
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

                        <table id="datatable-buttons" class="table table-striped table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Activity Name</th>
                                    <th>Activity</th>
                                    <th>Start Delay Remark</th>
                                    <th>End Delay Remark</th>
                                    @if($access->delete != '')
                                        <th>Status</th>
                                    @endif
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
                                @foreach($reasonMaster as $rmk=>$rmv)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $rmv->activityType->para_value }}</td>
                                        <td>{{ $rmv->activityName->activity_name }}</td>
                                        <td>{{ $rmv->start_delay_remark == '' ? '-' : $rmv->start_delay_remark }}</td>
                                        <td>{{ $rmv->end_delay_remark == '' ? '-' : $rmv->end_delay_remark }}</td>

                                        @if($access->delete != '')
                                            @php $checked = ''; @endphp
                                            @if($rmv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                            <td>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input class="form-check-input reasonMasterStatus" type="checkbox" id="customSwitch{{ $rmk }}" value="1" data-id="{{ $rmv->id }}" {{ $checked }}>
                                                    <label class="form-check-label" for="customSwitch{{ $rmk }}"></label>
                                                </div>
                                            </td>
                                        @endif

                                        @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                            <td>
                                                <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editReasonMaster',base64_encode($rmv->id)) }}" role="button">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                
                                                <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteReasonMaster',base64_encode($rmv->id)) }}" role="button" onclick="return confirm('Do you want to delete this reason master?');">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            </td>
                                        @else
                                            @if($access->edit != '')
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editReasonMaster',base64_encode($rmv->id)) }}" role="button">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                </td>
                                            @endif
                                            @if($access->delete != '')
                                                <td>
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteReasonMaster',base64_encode($rmv->id)) }}" role="button" onclick="return confirm('Do you want to delete this reason master?');">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @endif
                                        @endif

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