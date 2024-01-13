@extends('layouts.admin')
@section('title','All Drugs')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Drugs</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                @if($access->add == '1')
                                    <a href="{{ route('admin.addDrugMaster') }}" class="headerButtonStyle" role="button" title="Add Drug">
                                        Add Drug
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
                                    <th>Drug Name</th>
                                    <th>Drug Type</th>
                                    <th>Remarks</th>
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
                                @if(!is_null($drugs))
                                    @foreach($drugs as $dk => $dv)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $dv->drug_name }}</td>
                                            <td>{{ $dv->drug_type }}</td>
                                            <td>{{ $dv->remarks != '' ? $dv->remarks : '---' }}</td>

                                            @if($access->delete != '')
                                                @php $checked = ''; @endphp
                                                @if($dv->is_active == 1) @php $checked = 'checked' @endphp @endif
                                                <td>
                                                    <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                        <input class="form-check-input drugMasterStatus" type="checkbox" id="customSwitch{{ $dk }}" value="1" data-id="{{ $dv->id }}" {{ $checked }}>
                                                        <label class="form-check-label" for="customSwitch{{ $dk }}"></label>
                                                    </div>
                                                </td>
                                            @endif

                                            @if($admin == 'yes' || ($access->edit != '' && $access->delete != ''))
                                                <td>
                                                    <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editDrugMaster',base64_encode($dv->id)) }}" role="button">
                                                        <i class="bx bx-edit-alt"></i>
                                                    </a>
                                                    
                                                    <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteDrugMaster',base64_encode($dv->id)) }}" role="button" onclick="return confirm('Do you want to delete this drug?');">
                                                        <i class="bx bx-trash"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @if($access->edit != '')
                                                    <td>
                                                        <a class="btn btn-primary btn-sm waves-effect waves-light" href="{{ route('admin.editDrugMaster',base64_encode($dv->id)) }}" role="button">
                                                            <i class="bx bx-edit-alt"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if($access->delete != '')
                                                    <td>
                                                        <a class="btn btn-danger btn-sm waves-effect waves-light" href="{{ route('admin.deleteDrugMaster',base64_encode($dv->id)) }}" role="button" onclick="return confirm('Do you want to delete this drug?');">
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