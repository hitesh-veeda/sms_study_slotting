@extends('layouts.admin')
@section('title','All Activity Master')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Activity Master</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addActivityMaster') }}" class="headerButtonStyle" role="button" title="Add Activity Master">
                                        Add Activity Master
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
                                    <th>Days Required</th>
                                    <th>Next Activity</th>
                                    <th>Buffer Days</th>
                                    <th>Responsibility</th>
                                    <th>Activity Type</th>
                                    <th>Day Type</th>
                                    <th>Sequence No</th>
                                    <th>Previous Activity</th>
                                    <th>Milestone Activity</th>
                                    <!-- <th>Milestone Percentage</th>
                                    <th>Milestone Amount</th> -->
                                    <th>Parent Activity</th>
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
                                            <td>{{ $av->activity_name }}</td>
                                            <td>{{ $av->days_required }}</td>
                                            <td>
                                                {{ (!is_null($av->nextActivity) && $av->nextActivity->activity_name != '' ) ? $av->nextActivity->activity_name : '---' }}
                                            </td>
                                            <td>{{ $av->buffer_days }}</td>
                                            <td>
                                                {{ (!is_null($av->responsible) && $av->responsible->name != '') ? $av->responsible->name : '' }}
                                            </td>
                                            <td>
                                                {{ (!is_null($av->activityType) && $av->activityType->para_value != '') ? $av->activityType->para_value : '---' }}
                                            </td>
                                            <td>
                                                {{ $av->activity_days }}
                                            </td>
                                            <td>{{ $av->sequence_no }}</td>
                                            <td>
                                                {{ (!is_null($av->previousActivity) && $av->previousActivity->activity_name != '') ? $av->previousActivity->activity_name : '---' }}
                                            </td>
                                            <td>
                                                {{ $av->is_milestone == 1 ? 'Yes' : 'No' }}
                                            </td>
                                            <!-- <td>
                                                {{ $av->milestone_percentage != '' ? $av->milestone_percentage : '---' }}
                                            </td>
                                            <td>
                                                {{ $av->milestone_amount != '' ? $av->milestone_amount : '---' }}
                                            </td> -->
                                            <td>
                                                {{ (!is_null($av->parentActivity) && $av->parentActivity->activity_name != '') ? $av->parentActivity->activity_name : '---' }}
                                            </td>
                                            @if($access->delete != '')
                                                @php $checked = ''; @endphp
                                                @if($av->is_active == 1) @php $checked = 'checked' @endphp @endif
                                                <td>
                                                    <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                        <input class="form-check-input activityStatus" type="checkbox" id="customSwitch{{ $ak }}" value="1" data-id="{{ $av->id }}" {{ $checked }}>
                                                        <label class="form-check-label" for="customSwitch{{ $ak }}"></label>
                                                    </div>
                                                </td>
                                            @endif

                                            @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editActivityMaster',base64_encode($av->id)) }}" role="button" title="Edit">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteActivityMaster',base64_encode($av->id)) }}" role="button" onclick="return confirm('Do you want to delete this activity?');" title="Delete">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if($access->edit != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editActivityMaster',base64_encode($av->id)) }}" role="button" title="Edit">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->delete != '')
                                                    <td>
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteActivityMaster',base64_encode($av->id)) }}" role="button" onclick="return confirm('Do you want to delete this activity?');" title="Delete">
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