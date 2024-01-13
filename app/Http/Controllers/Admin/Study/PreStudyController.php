<?php

namespace App\Http\Controllers\Admin\Study;

use App\Http\Controllers\Controller;
use App\Models\StudyTrail;
use App\Models\Admin;
use App\Models\LocationMaster;
use App\Models\StudySchedule;
use App\Models\Study;
use Illuminate\Http\Request;
use App\Http\Controllers\GlobalController;
use App\View\VwPreStudyProjection;
use App\View\VwPostStudyProjection;

class PreStudyController extends GlobalController
{
    public function __construct(){
        $this->middleware('admin');
        $this->middleware('checkpermission');
    }

    // Pre study projection list with color coding
    public function preStudyProjectionList(Request $request){

        return view('admin.study.pre_study.pre_study_projection_list');

    }

    public function getPreStudyProjectionList(){
        
        $preStudyProjection = VwPreStudyProjection::whereNotNull('study_no')
                                                    ->with([
                                                        'studyNo' => function($q){
                                                            $q->select('id', 'study_no', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'cr_location', 'project_manager', 'sponsor', 'study_type', 'study_slotted', 'remark', 'projection_status')
                                                              ->with([
                                                                'crLocationName' => function($q){
                                                                    $q->select('id', 'location_name');
                                                                },
                                                                'projectManager' => function($q){
                                                                    $q->select('id', 'name', 'employee_code');
                                                                },
                                                                'sponsorName' => function($q){
                                                                    $q->select('id', 'sponsor_name');
                                                                },
                                                                'studyType' => function($q){
                                                                    $q->select('id', 'para_value');
                                                                },
                                                            ]);
                                                        },
                                                    ])
                                                   ->orderBy('tentative_clinical_date', 'ASC')
                                                   ->get();

        return $preStudyProjection;
    }

    public function getPostStudyProjectionList(){
        
        $postStudyProjection = VwPostStudyProjection::whereNotNull('study_no')
                                                    ->with([
                                                        'studyNo' => function($q){
                                                            $q->select('id', 'study_no', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'cr_location', 'project_manager', 'sponsor', 'study_type', 'study_slotted', 'remark')
                                                              ->with([
                                                                'crLocationName' => function($q){
                                                                    $q->select('id', 'location_name');
                                                                },
                                                                'projectManager' => function($q){
                                                                    $q->select('id', 'name', 'employee_code');
                                                                },
                                                                'sponsorName' => function($q){
                                                                    $q->select('id', 'sponsor_name');
                                                                },
                                                                'studyType' => function($q){
                                                                    $q->select('id', 'para_value');
                                                                },
                                                            ]);
                                                        },
                                                    ])
                                                   ->orderBy('check_in', 'ASC')
                                                   ->get();

        return $postStudyProjection;
    }
}