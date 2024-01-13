@extends('layouts.admin')
@section('title','All Holiday Masters')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Holiday Masters</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addHolidayMaster') }}" class="headerButtonStyle" role="button" title="Add Holiday Master">
                                        Add Holiday Master
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
                                    <th>Holiday Year</th>
                                    <th>Holiday Name</th>
                                    <th>Holiday Type</th>
                                    <th>Holiday Date</th>
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
                                @if(!is_null($holidaylist))
                                    @foreach ($holidaylist as $hk=>$hv)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $hv->holiday_year }}</td>
                                                <td>{{ $hv->holiday_name }}</td>
                                                <td>{{ $hv->holiday_type }}</td>
                                                <td>{{ date('d M Y', strtotime($hv->holiday_date)) }}</td>

                                                @if($access->delete != '')
                                                    @php $checked = ''; @endphp
                                                    @if($hv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                                    <td>
                                                        <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                            <input class="form-check-input HolidayChangeStatus " type="checkbox" id="customSwitch{{ $hk }}" value="1" data-id="{{ $hv->id }}" {{ $checked }}>
                                                            <label class="form-check-label" for="customSwitch{{ $hk }}"></label>
                                                        </div>
                                                    </td>
                                                @endif

                                                @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" role="button" title="Edit" href="{{route('admin.editHolidayMaster',base64_encode($hv->id)) }}">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                        
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteHolidayMaster',base64_encode($hv->id)) }}" role="button" onclick="return confirm('Do you want to delete this holiday?');" title="Delete">
                                                            <i class="bx bx-trash"></i>
                                                        </a>
                                                    </td>
                                                @else
                                                    @if($access->edit != '')
                                                        <td>
                                                            <a class="btn btn-primary btn-sm waves-effect waves-light" role="button" title="Edit" href="{{route('admin.editHolidayMaster',base64_encode($hv->id)) }}">
                                                                <i class="bx bx-edit-alt"></i>
                                                            </a>
                                                        </td>
                                                    @endif
                                                    @if($access->delete != '')
                                                        <td>
                                                            <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteHolidayMaster',base64_encode($hv->id)) }}" role="button" onclick="return confirm('Do you want to delete this holiday?');" title="Delete">
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


