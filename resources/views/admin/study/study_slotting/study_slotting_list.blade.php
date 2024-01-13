@extends('layouts.admin')
@section('title','All Study Slotting List')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Study Slotting List</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                All Study Slotting List
                            </li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="accordion" id="accordionExample">
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-medium @if(isset($filter) && ($filter == 1)) @else collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#studySlottingCollapseFilter" aria-expanded="false" aria-controls="studySlottingCollapseFilter">
                        Filters
                    </button>
                </h2>
                <div id="studySlottingCollapseFilter" class="accordion-collapse @if(isset($filter) && ($filter == 1)) @else collapse @endif" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="post" action="{{ route('admin.studySlottingList') }}" name="allStudySlottingListFilter" id="allStudySlottingListFilter">
                            @csrf
                            <div class="row">

                                <div class="col-md-3">
                                    <label class="control-label">Location</label>
                                    <select class="form-control select2" name="location" id="location" style="width: 100%;">
                                        <option value="">Select Location</option>
                                        @if(!is_null($locations))
                                            @foreach($locations as $lk => $lv)
                                                <option @if($locationName == $lv->id) selected @endif value="{{ $lv->id }}">{{ $lv->location_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Checkin Date Range</label>
                                        <div>
                                            <div class="input-daterange input-group" data-date-format="dd/mm/yyyy" data-date-autoclose="true" data-provide="datepickerStyle" autocomplete="off">
                                                <input type="text" class="form-control datepickerStyle" name="checkin_from_date" id="checkin_from_date" value="{{ $checkinFromDate }}" autocomplete="off" placeholder="From Date">
                                                <input type="text" class="form-control datepickerStyle" name="checkin_to_date" id="checkin_to_date" value="{{ $checkinToDate }}" autocomplete="off" placeholder="To Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1 pt-1">
                                    <button type="submit" class="btn btn-primary vendors save_button mt-4">Submit</button>
                                </div>

                                @if(isset($filter) && ($filter == 1))
                                    <div class="col-md-1 pt-1">
                                        <a href="{{ route('admin.studySlottingList') }}" class="btn btn-danger mt-4 cancel_button" id="filter" name="reset" value="reset">
                                            Reset
                                        </a>
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
                                    <th>Study No</th>
                                    <th>Period</th>
                                    <th>Location</th>
                                    <th>Male Clinical Wards</th>
                                    <th>Female Clinical Wards</th>
                                    <th>Checkin Date And Time</th>
                                    <th>Checkout Date And Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!is_null($studySlottingList))
                                    @foreach ($studySlottingList as $sslk => $sslv)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                {{ (((!is_null($sslv->studyNo)) && ($sslv->studyNo->study_no != '')) ? $sslv->studyNo->study_no : '---') }}
                                            </td>

                                            <td>{{ $sslv->period_no }}</td>

                                            <td>
                                                {{ (((!is_null($sslv->studyNo)) && (!is_null($sslv->studyNo->crLocationName)) && ($sslv->studyNo->crLocationName->location_name != '')) ? $sslv->studyNo->crLocationName->location_name : '---') }}
                                            </td>

                                            <td>
                                                @if((!is_null($sslv->maleClinicalWards)) && (count($sslv->maleClinicalWards) > 0))
                                                    @php $maleClinicalWardNames = array(); @endphp
                                                    
                                                    @foreach ($sslv->maleClinicalWards as $key => $value)
                                                        @if((!is_null($value->maleLocationName)) && (!is_null($value->maleLocationName->ward_name)))
                                                            @php
                                                                $maleClinicalWardNames[] = $value->maleLocationName->ward_name;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    {{ implode(' | ', $maleClinicalWardNames) }}
                                                @else
                                                    {{ '---' }}
                                                @endif
                                            </td>

                                            <td>
                                                @if((!is_null($sslv->femaleClinicalWards)) && (count($sslv->femaleClinicalWards) > 0))
                                                    @php $femaleClinicalWardNames = array(); @endphp

                                                    @foreach ($sslv->femaleClinicalWards as $key => $value)
                                                        @if((!is_null($value->femaleLocationName)) && (!is_null($value->femaleLocationName->ward_name)))
                                                            @php
                                                                $femaleClinicalWardNames[] = $value->femaleLocationName->ward_name;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    {{ implode(' | ', $femaleClinicalWardNames) }}
                                                @else
                                                    {{ '---' }}
                                                @endif
                                            </td>

                                            <td>{{ (($sslv->check_in_date_time != '') ? date('d M Y H:i', strtotime($sslv->check_in_date_time)) : '---') }}</td>

                                            <td>{{ (($sslv->check_out_date_time != '') ? date('d M Y H:i', strtotime($sslv->check_out_date_time)) : '---') }}</td>
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
