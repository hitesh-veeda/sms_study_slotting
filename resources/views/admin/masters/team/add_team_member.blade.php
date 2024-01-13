@extends('layouts.admin')
@section('title','Add Team Member')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Add Team Member</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.roleList') }}">Team Member List</a></li>
                            <li class="breadcrumb-item active">Add Team Member</li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>     

        <form class="custom-validation" action="{{ route('admin.saveTeamMember') }}" method="post" id="addTeamMember" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card">
                        <div class="card-body">

                            <div class="form-group">
                                <span style="color:red;float:right;" class="pull-right">* is mandatory</span>
                            </div>

                            <div class="form-group mb-3">
                                <label>Full Name<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Full Name" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Login Id<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="login_id" id="login_id" placeholder="Login Id" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Employee Code<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="employee_code" id="employee_code" placeholder="Employee Code" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Department<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="department" id="department" placeholder="Department" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Department No<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="department_no" id="department_no" placeholder="Department No" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Designation<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="designation" id="designation" placeholder="Designation" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label for="answer">Designation No<span class="mandatory">*</span></label>
                                <input type="text" class="form-control" name="designation_no" id="designation_no" placeholder="Designation No" autocomplete="off" required/>
                            </div>

                            <div class="mb-3">
                                <label>Assign Role<span class="mandatory">*</span></label>
                                <select class="form-select select2" name="role_id" id="role_id" required>
                                    <option value="">Select Role</option>
                                    @if(!is_null($roles))
                                        @foreach($roles as $rk => $rv)
                                            <option value="{{ $rv->id }}">{{ $rv->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span id="role"></span>
                            </div>

                            <!-- <div class="form-group mb-3">
                                <label>Mobile Number<span class="mandatory">*</span></label>
                                <input type="text" class="form-control numeric" name="mobile" id="mobile" placeholder="Mobile Number" maxlength="10" minlength="10" autocomplete="off" required/>
                            </div> -->

                            <div class="form-group mb-3">
                                <label for="email">Email<span style="color:red;">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Email" required autocomplete="off" />
                            </div>

                            <div class="mb-3">
                                <label>Location</label>
                                <select class="form-select select2" name="location_id" id="location_id">
                                    <option value="">Select Location</option>
                                    @if(!is_null($locations))
                                        @foreach($locations as $lk => $lv)
                                            <option value="{{ $lv->id }}">
                                                {{ $lv->location_name }} - {{ $lv->location_type }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <!-- <span id="location"></span> -->
                            </div>

                            <div class="form-group mb-3">
                                <label>Password<span class="mandatory">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Password" />
                            </div>

                            <div class="form-group mb-3">
                                <label>Confirm Password<span class="mandatory">*</span></label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required placeholder="Confirm Password"/>
                            </div>

                            <div class="button-items">
                                <center>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" name="btn_submit" value="save">
                                        Save
                                    </button>
                                    <button type="submit" class="btn btn-secondary waves-effect waves-light mr-1" name="btn_submit" value="save_and_update">
                                        Save & Add New
                                    </button>
                                    <a href="{{ route('admin.teamMemberList') }}" class="btn btn-danger waves-effect">
                                        Cancel
                                    </a>
                                </center>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
