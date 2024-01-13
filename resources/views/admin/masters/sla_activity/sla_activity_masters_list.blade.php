@extends('layouts.admin')
@section('title','All SLA Activity Master')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All SLA Activity Master</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addSlaActivityMaster') }}" class="headerButtonStyle" role="button" title="Add SLA Activity Master">
                                        Add SLA Activity Master
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
                                    <th>Study Design</th>
                                    <th>No of from subject</th>
                                    <th>No of to subject</th>
                                    <th>No of days</th>
                                    <th>CDISC</th>
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
                                @if(!is_null($activities))
                                    @foreach($activities as $ak => $av)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $av->activityName->activity_name }}
                                            </td>
                                            <td>
                                                {{ $av->studyDesign->para_value}}
                                            </td>
                                            <td>
                                                {{ $av->no_from_subject}}
                                           </td>
                                            <td>
                                                {{ $av->no_to_subject}}
                                            </td>
                                            <td>
                                                {{ $av->no_of_days}}
                                            </td>
                                            <td>
                                                @if($av->is_cdisc == 0)
                                                    No
                                                @else
                                                    Yes
                                                @endif
                                            </td>

                                            @if($access->delete != '')
                                                @php $checked = ''; @endphp
                                                @if($av->is_active == 1) @php $checked = 'checked' @endphp @endif
                                                <td>
                                                    <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                        <input class="form-check-input activitySlottingStatus" type="checkbox" id="customSwitch{{ $ak }}" value="1" data-id="{{ $av->id }}" {{ $checked }}>
                                                        <label class="form-check-label" for="customSwitch{{ $ak }}"></label>
                                                    </div>
                                                </td>
                                            @endif

                                            @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editSlaActivityMaster',base64_encode($av->id)) }}" role="button" title="Edit">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteSlaActivityMaster',base64_encode($av->id)) }}" role="button" onclick="return confirm('Do you want to delete this sla activity?');" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if($access->edit != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editSlaActivityMaster',base64_encode($av->id)) }}" role="button" title="Edit">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->delete != '')
                                                    <td>
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteSlaActivityMaster',base64_encode($av->id)) }}" role="button" onclick="return confirm('Do you want to delete this sla activity?');" title="Delete">
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
