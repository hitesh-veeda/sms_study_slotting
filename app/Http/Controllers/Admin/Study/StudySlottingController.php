<?php

namespace App\Http\Controllers\Admin\Study;

use App\Models\Study;
use Illuminate\Http\Request;
use App\Models\LocationMaster;
use Illuminate\Support\Carbon;
use App\Models\ClinicalWardMaster;
use App\Http\Controllers\Controller;
use App\Models\StudyMaleSlottedWard;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyClinicalSlotting;
use App\Models\StudyFemaleSlottedWard;
use App\Models\StudyMaleSlottedWardTrail;
use App\Http\Controllers\GlobalController;
use App\Models\StudyClinicalSlottingTrail;
use App\Models\StudyFemaleSlottedWardTrail;

class StudySlottingController extends GlobalController
{
    public function __construct(){
        $this->middleware('admin');
        $this->middleware('checkpermission');
    }

    public function studySlottingList(Request $request) {

        $filter = 0;
        $locationName = '';
        $checkinFromDate = '';
        $checkinToDate = '';

        $locations = LocationMaster::select('id', 'location_name')
                                   ->where('location_type', 'CRSITE')
                                   ->where('location_name', '!=', 'NA')
                                   ->where('is_active', 1)
                                   ->where('is_delete', 0)
                                   ->get();

        $query = StudyClinicalSlotting::select('id', 'study_id', 'period_no', 'check_in_date_time', 'check_out_date_time', 'is_active')
                                      ->where('is_delete', 0);

        if((isset($request->location)) && ($request->location != '')) {
            $filter = 1;
            $locationName = $request->location;
            $query->whereHas('studyNo', function($q) use($locationName) {
                $q->where('is_active', 1)
                  ->where('is_delete', 0)
                  ->whereHas('crLocationName', function($q) use($locationName) {
                        $q->where('id', $locationName)
                          ->where('is_active', 1)
                          ->where('is_delete', 0);
                    });
                });
        }

        if(((isset($request->checkin_from_date)) && ($request->checkin_from_date != '')) && ((isset($request->checkin_to_date)) && ($request->checkin_to_date != ''))) {
            $filter = 1;
            $checkinFromDate = $request->checkin_from_date;
            $checkinToDate = $request->checkin_to_date;

            $query->whereDate('check_in_date_time', '>=', $this->convertDateTime($checkinFromDate))->whereDate('check_in_date_time', '<=', $this->convertDateTime($checkinToDate));
        }

        $studySlottingList = $query->with([
                                        'studyNo' => function($q) {
                                            $q->select('id', 'study_no', 'cr_location')
                                              ->where('is_active', 1)
                                              ->where('is_delete', 0)
                                              ->with([
                                                    'crLocationName' => function($q) {
                                                        $q->select('id', 'location_name')
                                                          ->where('is_active', 1)
                                                          ->where('is_delete', 0);
                                                    }
                                               ]);
                                        },
                                        'maleClinicalWards' => function($q) {
                                            $q->select('id', 'study_clinical_slotting_id', 'male_clinical_ward_id')
                                              ->with([
                                                   'maleLocationName' => function($q) {
                                                        $q->select('id', 'ward_name')
                                                          ->where('is_active', 1)
                                                          ->where('is_delete', 0);
                                                    }
                                                ]);
                                        },
                                        'femaleClinicalWards' => function($q) {
                                            $q->select('id', 'study_clinical_slotting_id', 'female_clinical_ward_id')
                                              ->with([
                                                    'femaleLocationName' => function($q) {
                                                        $q->select('id', 'ward_name')
                                                          ->where('is_active', 1)
                                                          ->where('is_delete', 0);
                                                    }
                                                ]);
                                        }
                                    ])
                                    ->whereHas('studyNo', function($q) {
                                        $q->where('is_active', 1)
                                          ->where('is_delete', 0)
                                          ->whereHas('crLocationName', function($q){
                                                $q->where('is_active', 1)
                                                  ->where('is_delete', 0);
                                            });
                                    })
                                    ->orderBy('id', 'DESC')
                                    ->get();

        return view('admin.study.study_slotting.study_slotting_list', compact('locations', 'studySlottingList', 'filter', 'locationName', 'checkinFromDate', 'checkinToDate'));
    }

    public function studyCalendarList(Request $request) {
        return view('admin.study.study_slotting.clinical_calendar');
    }

    public function addStudySlot($id) {

        $studyPeriodNos = array();
        $expectedChekinDate = '';

        $study = Study::select('id', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'washout_period', 'cr_location', 'no_of_periods', 'pre_housing', 'post_housing')
                      ->where('id', $id)
                      ->where('is_delete', 0)
                      ->first();

        $crClinicalWardList = ClinicalWardMaster::select('id', 'ward_name')
                                                ->where('location_id', $study->cr_location)
                                                ->where('no_of_beds', '>=', $study->no_of_subject)
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->get();

        $studyPeriodNos = StudyClinicalSlotting::where('study_id', $study->id)->where('is_active', 1)->where('is_delete', 0)->pluck('period_no')->toArray();

        $studyPeriodExist = StudyClinicalSlotting::where('study_id', $study->id)->where('is_active', 1)->where('is_delete', 0)->orderBy('id', 'DESC')->first();

        if(!is_null($studyPeriodExist)) {
            // $expectedChekinDate = date('Y-m-d H:i', strtotime($studyPeriodExist->check_in_date_time .($study->washout_period). 'days'));
            $expectedChekinDate = date('Y-m-d\TH:i', strtotime($studyPeriodExist->check_out_date_time . '+1 minute'));
        }

        return view('admin.study.study_slotting.study_slotting_modal', compact('study', 'crClinicalWardList', 'studyPeriodNos', 'expectedChekinDate'));
    }

    public function saveStudySlot(Request $request) {

        $totalHours = $request->pre_housing + $request->post_housing;
        $checkOutDateTime = date('Y-m-d H:i', strtotime($request->check_in_date_time. ($totalHours) . 'hours' .($request->washout_period). 'days'));

        $saveStudyClinicalSlotting = new StudyClinicalSlotting;
        $saveStudyClinicalSlotting->study_id = $request->study_id;
        $saveStudyClinicalSlotting->period_no = $request->period_no;
        $saveStudyClinicalSlotting->check_in_date_time = $request->check_in_date_time;
        $saveStudyClinicalSlotting->check_out_date_time = $checkOutDateTime;
        $saveStudyClinicalSlotting->created_by_user_id = Auth::guard('admin')->user()->id;
        $saveStudyClinicalSlotting->save();

        $saveStudyClinicalSlottingTrail = new StudyClinicalSlottingTrail;
        $saveStudyClinicalSlottingTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
        $saveStudyClinicalSlottingTrail->study_id = $request->study_id;
        $saveStudyClinicalSlottingTrail->period_no = $request->period_no;
        $saveStudyClinicalSlottingTrail->check_in_date_time = $request->check_in_date_time;
        $saveStudyClinicalSlottingTrail->check_out_date_time = $checkOutDateTime;
        $saveStudyClinicalSlottingTrail->created_by_user_id = Auth::guard('admin')->user()->id;
        $saveStudyClinicalSlottingTrail->save();

        if($request->has('male_clinical_ward_location')) {

            foreach($request->male_clinical_ward_location as $key => $value) {
                $saveMaleClinicalWard = new StudyMaleSlottedWard;
                $saveMaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                $saveMaleClinicalWard->male_clinical_ward_id = $value;
                $saveMaleClinicalWard->save();

                $saveMaleClinicalWardTrail = new StudyMaleSlottedWardTrail;
                $saveMaleClinicalWardTrail->study_male_slotted_ward_id = $saveMaleClinicalWard->id;
                $saveMaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                $saveMaleClinicalWardTrail->male_clinical_ward_id = $value;
                $saveMaleClinicalWardTrail->save();
            }
        }

        if($request->has('female_clinical_ward_location')) {

            foreach($request->female_clinical_ward_location as $key => $value) {
                $saveFemaleClinicalWard = new StudyFemaleSlottedWard;
                $saveFemaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                $saveFemaleClinicalWard->female_clinical_ward_id = $value;
                $saveFemaleClinicalWard->save();

                $saveFemaleClinicalWardTrail = new StudyFemaleSlottedWardTrail;
                $saveFemaleClinicalWardTrail->study_female_slotted_ward_id = $saveFemaleClinicalWard->id;
                $saveFemaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                $saveFemaleClinicalWardTrail->female_clinical_ward_id = $value;
                $saveFemaleClinicalWardTrail->save();
            }
        }

        return redirect(route('admin.studyList'))->with('messages', [
            [
                'type' => 'success',
                'title' => 'Study Slot',
                'message' => 'Study slot successfully added!',
            ],
        ]);
    }

    public function checkClinicalWardsCapacity(Request $request) {

        if((isset($request->check_in_date_time)) && ($request->check_in_date_time != '')) {
            return 'false';
        } else {
            return $request->toArray();
        }
    }
}
