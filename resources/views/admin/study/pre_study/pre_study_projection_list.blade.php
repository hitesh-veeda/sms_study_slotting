@extends('layouts.admin')
@section('title','All Study Projection List')
@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <!-- <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#preStudyCollapseFilter" aria-expanded="false" aria-controls="preStudyCollapseFilter">
                            All Pre Study Projection List
                        </button>
                    </h2>
                    <div id="preStudyCollapseFilter" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body collapse show"> -->

                            <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">All Pre Study Projection List</h4>

                                        <div class="page-title-right">
                                            <ol class="breadcrumb m-0">
                                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">All Pre Study Projection List</li>
                                            </ol>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="gridContainer">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">

                                            
                                        </div>
                                    </div>
                                </div>
                            </div><br><br>

                        <!-- </div>
                    </div>
                </div>
            </div> -->

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo" style="color: #4d63cf !important; background-color: #eef1fd !important;">
                        <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#postStudyCollapseFilter" aria-expanded="false" aria-controls="postStudyCollapseFilter">
                            All Executed Study List
                        </button>
                    </h2>
                    <div id="postStudyCollapseFilter" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body collapse show">

                            <!-- <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">All Post Study Projection List</h4>

                                        <div class="page-title-right">
                                            <ol class="breadcrumb m-0">
                                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                                <li class="breadcrumb-item active">All Pre Study Projection List</li>
                                            </ol>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div> -->

                            <div class="row" id="postStudyGridContainer">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection