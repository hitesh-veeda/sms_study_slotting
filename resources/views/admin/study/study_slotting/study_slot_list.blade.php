@extends('layouts.admin')
@section('title','All Study Slot List')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Study Slot List</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                Study Slot
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
                                    @if($admin == 'yes')
                                        <th class='notexport'>Action</th>
                                    @else
                                        @if($access->add != '')
                                            <th class='notexport'>Action</th>
                                        @endif
                                    @endif
                                    <th>Study No</th>
                                    <th>Location</th>
                                    <th>Tentative Clinical Date</th>
                                    <th>Study Slotted</th>
                                    <th>Drug</th>
                                    <th>Sponsor</th>
                                    <th>Project Manager</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp

                                @if(!is_null($studies))
                                    @foreach ($studies as $sk => $sv)
                                        @if ($sv->study_slotting_count < $sv->no_of_periods)
                                            <tr>
                                                <td>
                                                    {{ $count++ }}
                                                </td>

                                                @if($admin == 'yes')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light addStudySlot" href="javascript:void(0)" role="button" title="Add Study Slot" data-id="{{ $sv->study_id }}">
                                                            <i class="bx bx-calendar-event"></i>
                                                        </a>
                                                    </td>
                                                    @else
                                                        @if($access->add != '')
                                                            <td>
                                                                <a class="btn btn-primary btn-sm waves-effect waves-light addStudySlot" href="javascript:void(0)" role="button" title="Add Study Slot" data-id="{{ $sv->study_id }}">
                                                                    <i class="bx bx-calendar-event"></i>
                                                                </a>
                                                            </td>
                                                        @endif
                                                @endif

                                                <td>
                                                    {{ (($sv->study_no != '') ? $sv->study_no : '---') }}
                                                </td>

                                                <td>
                                                    {{ (($sv->CR_Location != '') ? $sv->CR_Location : '---') }}
                                                </td>

                                                <td>
                                                    {{ (($sv->tentative_clinical_date != '') ? date('d M Y', strtotime($sv->tentative_clinical_date)) : '---') }}
                                                </td>

                                                <td>
                                                    {{ (($sv->study_slotted != '') ? $sv->study_slotted : '---') }}
                                                </td>

                                                <td>
                                                    {{ (($sv->drug != '') ? $sv->drug : '---') }}
                                                </td>

                                                <td>
                                                    {{ (($sv->sponsor_name != '')) ? $sv->sponsor_name : '---' }}
                                                </td>

                                                <td>
                                                    {{ (($sv->project_manager != '')) ? $sv->project_manager : '---' }}
                                                </td>
                                            </tr>
                                        @endif
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