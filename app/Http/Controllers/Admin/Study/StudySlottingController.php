<?php

namespace App\Http\Controllers\Admin\Study;

use DateTime;
use App\Models\Study;
use Illuminate\Http\Request;
use App\View\VwStudySlotting;
use App\Models\LocationMaster;
use App\Models\RoleModuleAccess;
use App\Models\ClinicalWardMaster;
use Illuminate\Support\Facades\DB;
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

    /**
     * Retrieve a list of study slots along with their related information.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function studySlotList(Request $request) {

        // Retrieve study slot information including study ID, study number, location, tentative clinical date, slotted status, sponsor name, project manager, drug, and number of periods.
        $studies = VwStudySlotting::select('study_id', 'study_no', 'CR_Location', 'tentative_clinical_date', 'study_slotted', 'sponsor_name', 'project_manager', 'drug', 'no_of_periods')
                                  ->withCount([
                                       'studySlotting' => function($q) {
                                           $q->where('is_active', 1)
                                             ->where('is_delete', 0);
                                        }
                                   ])
                                  ->get();

        $admin = '';
        $access = '';

        // Check if the user is an admin or not, and assign 'yes' to $admin if true.
        // Otherwise, fetch the access rights for the user based on their role.
        if(Auth::guard('admin')->user()->role == 'admin'){
            $admin = 'yes';
        } else {
            $access = RoleModuleAccess::where('role_id', Auth::guard('admin')->user()->role_id)
                                      ->where('module_name','study-slot')
                                      ->first();
        }

        // Pass the retrieved data to the corresponding view along with admin status and access rights.
        return view('admin.study.study_slotting.study_slot_list', compact('studies', 'admin', 'access'));
    }

    /**
     * Retrieve a list of clinical slotting information based on optional filters such as location and date range.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function clinicalSlottingList(Request $request) {

        // Initialize variables for filtering and storing filter values.
        $filter = 0;
        $locationName = '';
        $checkinFromDate = '';
        $checkinToDate = '';

        // Retrieve active clinical locations.
        $locations = LocationMaster::select('id', 'location_name')
                                   ->where('location_type', 'CRSITE')
                                   ->where('location_name', '!=', 'NA')
                                   ->where('is_active', 1)
                                   ->where('is_delete', 0)
                                   ->get();

        // Initialize the base query for retrieving clinical slotting information.
        $query = StudyClinicalSlotting::select('id', 'study_id', 'period_no', 'check_in_date_time', 'check_out_date_time')
                                      ->where('is_delete', 0);

        // Apply optional filters if provided in the request.
        if((isset($request->location)) && ($request->location != '')) {
            $filter = 1;
            $locationName = $request->location;
            $query->whereHas('studyNo', function($q) use($locationName) {
                $q->where('cr_location', $locationName);
            });
        }

        if(((isset($request->checkin_from_date)) && ($request->checkin_from_date != '')) && ((isset($request->checkin_to_date)) && ($request->checkin_to_date != ''))) {
            $filter = 1;
            $checkinFromDate = $request->checkin_from_date;
            $checkinToDate = $request->checkin_to_date;
            $query->whereDate('check_in_date_time', '>=', $this->convertDateTime($checkinFromDate))->whereDate('check_in_date_time', '<=', $this->convertDateTime($checkinToDate));
        }

        // Retrieve clinical slotting data with relationships and constraints.
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
                                                    },
                                                    'schedule' => function($q) {
                                                        $q->select('id', 'study_id', 'actual_start_date', 'period_no')
                                                          ->where('activity_name', 'Checkin')
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

        $admin = '';
        $access = '';

        // Check if the user is an admin or not, and assign 'yes' to $admin if true.
        // Otherwise, fetch the access rights for the user based on their role.
        if(Auth::guard('admin')->user()->role == 'admin'){
            $admin = 'yes';
        } else {
            $access = RoleModuleAccess::where('role_id', Auth::guard('admin')->user()->role_id)
                                      ->where('module_name','clinical-slotting')
                                      ->first();
        }

        // Pass data to the corresponding view along with filter information, admin status, and access rights.
        return view('admin.study.study_slotting.study_slotting_list', compact('locations', 'studySlottingList', 'filter', 'locationName', 'checkinFromDate', 'checkinToDate', 'admin', 'access'));
    }

    /**
     * Retrieve a list of clinical slotting information for rendering in a calendar view.
     * If the request is AJAX, returns JSON data; otherwise, renders the calendar view.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function clinicalCalendarList(Request $request) {

        // Retrieve active clinical locations for filtering options.
        $locations = LocationMaster::select('id', 'location_name')
                                   ->where('location_type', 'CRSITE')
                                   ->where('location_name', '!=', 'NA')
                                   ->where('is_active', 1)
                                   ->where('is_delete', 0)
                                   ->get();

        // If the request is AJAX, retrieve and return JSON data for the clinical slotting list.
        if($request->ajax()) {
            $query = StudyClinicalSlotting::select('id', 'study_id', 'period_no', 'check_in_date_time', 'check_out_date_time')
                                          ->where('is_active', 1)
                                          ->where('is_delete', 0);

            // if((isset($request->location)) && ($request->location != '')) {
            //     $query->whereHas('studyNo', function($q) use($request) {
            //         $q->where('cr_location', $request->location);
            //     });
            // }

            $studySlottingList = $query->with([
                                            'studyNo' => function($q) {
                                                $q->select('id', 'study_no', 'subject_type', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'washout_period', 'cr_location', 'study_type', 'no_of_periods', 'pre_housing', 'post_housing', 'project_manager')
                                                  ->where('is_active', 1)
                                                  ->where('is_delete', 0)
                                                  ->with([
                                                      'crLocationName' => function($q) {
                                                            $q->select('id', 'location_name')
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0);
                                                       },
                                                       'subjectTypeName' => function($q) {
                                                            $q->select('id', 'para_value')
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0);
                                                       },
                                                       'projectManager' => function($q) {
                                                            $q->select('id', 'name')
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0);
                                                       },
                                                       'drugDetails' => function($q) {
                                                            $q->select('id', 'study_id', 'drug_id')
                                                              ->where('type', 'TEST')
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0)
                                                              ->with([
                                                                    'drugName' => function($q) {
                                                                        $q->select('id', 'drug_name');
                                                                    }
                                                               ]);
                                                       },
                                                       'studyType' => function($q) {
                                                            $q->select('id', 'para_value')
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0);
                                                       },
                                                       'schedule' => function($q) {
                                                            $q->select('id', 'study_id', 'activity_id', 'actual_start_date', 'period_no')
                                                              ->where('activity_id', 2)
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
                                        ->orderBy('check_in_date_time')
                                        ->get();

            // Return JSON data for AJAX request.
            return $studySlottingList;
            // echo "<pre>";
            // print_r($studySlottingList->toArray());
            // return;
        }

        // If the request is not AJAX, render the calendar view with the locations for filtering.
        return view('admin.study.study_slotting.clinical_calendar', compact('locations'));
    }

    /**
     * Retrieve studies available for slotting on a particular date and render a modal for selecting slots on the calendar.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function openStudySlotModalForCalendar(Request $request) {

        $checkinDateTime = '';

        // Retrieve study IDs with no available slots for the selected date.
        // $noSlotAvailableStudyIds = StudyClinicalSlotting::where('is_active', 1)
        //                                                 ->where('is_delete', 0)
        //                                                 ->groupBy('study_id', 'period_no')
        //                                                 ->orderBy('period_no', 'DESC')
        //                                                 ->pluck('period_no')
        //                                                 ->toArray();

        $noSlotAvailableStudyIds = StudyClinicalSlotting::join('studies', 'study_clinical_slottings.study_id', '=', 'studies.id')
                                                        ->whereDate(DB::raw('study_clinical_slottings.check_in_date_time + INTERVAL studies.pre_housing HOUR + INTERVAL studies.washout_period DAY'), '>=', date('Y-m-d', strtotime($request->check_in_date_time)))
                                                        ->groupBy('study_clinical_slottings.study_id')
                                                        ->pluck('study_id')
                                                        ->toArray();

        // echo "<pre>";
        // print_r($noSlotAvailableStudyIds);
        // exit;

        // Query studies with available slots, excluding those with no slots available for the selected date.
        $query = VwStudySlotting::select('study_id', 'study_no', 'no_of_periods')
                                ->whereNotIn('study_id', $noSlotAvailableStudyIds)
                                ->withCount([
                                    'studySlotting' => function ($q) {
                                        $q->where('is_active', 1)
                                          ->where('is_delete', 0);
                                    }
                                ]);

        if((isset($request->check_in_date_time)) && ($request->check_in_date_time != '')) {
            $checkinDateTime = date('d-M-Y H:i', strtotime($request->check_in_date_time));
        }

        // Filter studies by clinical location if specified.
        if((isset($request->cr_location)) && ($request->cr_location != '') && ($request->cr_location != 'All')){
            $query->where('CR_Location', $request->cr_location);
        }

        // Retrieve the studies matching the criteria and pass them to the modal view.
        $studies = $query->get();

        // echo "<pre>";
        // print_r($noSlotAvailableStudyIds->toArray());
        // return;

        return view('admin.study.study_slotting.clinical_calendar_study_slotting_modal', compact('studies', 'checkinDateTime'));
    }

    /**
     * Retrieve study and clinical ward data for slotting based on the selected study and date.
     * Check availability and capacity of male and female clinical wards and return JSON response.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudyAndWardData(Request $request) {

        // Initialize variables for storing error messages, selected wards, and remaining capacity.
        $maleErrorMessage = '';
        $femaleErrorMessage = '';
        $maleClinicalWardIds = array();
        $femaleClinicalWardIds = array();
        $remainingMaleCapacity = 0;
        $remainingFemaleCapacity = 0;
        $maleWardSelection = true;
        $femaleWardSelection = true;
        $remainingWardsCapacity = array();

        // Retrieve study information including subject counts and location.
        $study = Study::select('id', 'study_no', 'study_design', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'washout_period', 'cr_location', 'study_condition', 'no_of_periods', 'pre_housing', 'post_housing', 'project_manager')
                      ->where('id', $request->study_id)
                      ->where('is_delete', 0)
                      ->with([
                            'studyDesignName' => function($q) {
                                $q->select('id', 'para_value')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'studyConditionName' => function($q) {
                                $q->select('id', 'para_value')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'crLocationName' => function($q) {
                                $q->select('id', 'location_name')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'projectManager' => function($q) {
                                $q->select('id', 'name', 'employee_code')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            }
                      ])
                      ->withCount([
                            'studySlotting' => function($q) {
                                $q->where('is_active', 1)
                                  ->where('is_delete', 0);
                            }
                      ])
                      ->first();

        // Added pre housing and post housing in request checkin date and time.
        $checkOutDate = date('Y-m-d', strtotime($request->checkin_date_time. ($study->pre_housing + $study->post_housing) . 'hours'));

        // Check for existing slots on the selected date and checkout date for the study's location.
        $studySlotExists = StudyClinicalSlotting::whereBetween('check_in_date_time', [date('Y-m-d', strtotime($request->checkin_date_time)), $checkOutDate])
                                                ->whereBetween('check_out_date_time', [date('Y-m-d', strtotime($request->checkin_date_time)), $checkOutDate])
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->whereHas('studyNo', function($q) use($study) {
                                                    $q->where('cr_location', $study->cr_location)
                                                      ->where('is_delete', 0);
                                                })
                                                ->pluck('id')
                                                ->toArray();
                                        

        echo "<pre>";
        print_r($studySlotExists);
        return;

        // If slots exist, check occupancy and calculate remaining capacity for male and female wards.
        if(count($studySlotExists) > 0) {
            // Retrieve occupied beds by male wards.
            $occupiedBedsByMaleWards = StudyMaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_male_beds, male_clinical_ward_id')
                                                           ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                           ->groupBy('male_clinical_ward_id')
                                                           ->get();

            // Store all male selected ward ids in $maleClinicalWardIds
            $maleClinicalWardIds = $occupiedBedsByMaleWards->pluck('male_clinical_ward_id')->toArray();

            // Process male wards occupancy and remaining capacity.
            if(count($occupiedBedsByMaleWards) > 0) {
                foreach($occupiedBedsByMaleWards as $key => $value) {
                    // Retrieve clinical ward details.
                    $clinicalWard = ClinicalWardMaster::select('id', 'ward_name', 'no_of_beds')
                                                      ->where('id', $value->male_clinical_ward_id)
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->first();

                    // Calculate remaining capacity and add to the list of remaining wards.
                    if(!is_null($clinicalWard)) {
                        if($value->occupied_male_beds >= $clinicalWard->no_of_beds) {
                            array_push($remainingWardsCapacity, [$value->male_clinical_ward_id => ($clinicalWard->ward_name. ' (0)')]);
                        } else {
                            array_push($remainingWardsCapacity, [$value->male_clinical_ward_id => ($clinicalWard->ward_name. ' (' .($clinicalWard->no_of_beds - $value->occupied_male_beds). ')')]);
                        }
                    }
                }
            }

            // Retrieve occupied beds by female wards.
            $occupiedBedsByFemaleWards = StudyFemaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_female_beds, female_clinical_ward_id')
                                                               ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                               ->groupBy('female_clinical_ward_id')
                                                               ->get();

            // Store all female selected ward ids in $femaleClinicalWardIds
            $femaleClinicalWardIds = $occupiedBedsByFemaleWards->pluck('female_clinical_ward_id')->toArray();

            // Process female wards occupancy and remaining capacity, similar to male wards.
            if(count($occupiedBedsByFemaleWards) > 0) {
                foreach($occupiedBedsByFemaleWards as $key => $value) {
                    // Retrieve clinical ward details.
                    $clinicalWard = ClinicalWardMaster::select('id', 'ward_name', 'no_of_beds')
                                                      ->where('id', $value->female_clinical_ward_id)
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->first();

                    // Calculate remaining capacity and add to the list of remaining wards.
                    if(!is_null($clinicalWard)) {
                        if($value->occupied_male_beds >= $clinicalWard->no_of_beds) {
                            array_push($remainingWardsCapacity, [$value->female_clinical_ward_id => ($clinicalWard->ward_name. ' (0)')]);
                        } else {
                            array_push($remainingWardsCapacity, [$value->female_clinical_ward_id => ($clinicalWard->ward_name. ' (' .($clinicalWard->no_of_beds - $value->occupied_female_beds). ')')]);
                        }
                    }
                }
            }
        }

        // Retrieve clinical wards that are not selected in any slot.
        $clinicalWards = ClinicalWardMaster::select('id', 'ward_name', 'no_of_beds')
                                           ->where('location_id', $study->cr_location)
                                           ->whereNotIn('id', array_merge($maleClinicalWardIds, $femaleClinicalWardIds))
                                           ->where('is_active', 1)
                                           ->where('is_delete', 0)
                                           ->orderBy('ward_name')
                                           ->get();

        // If clinical wards available, add them to the list of remaining wards.
        if(!is_null($clinicalWards)) {
            foreach($clinicalWards as $cwk => $cwv) {
                array_push($remainingWardsCapacity, [$cwv->id => ($cwv->ward_name. ' (' .$cwv->no_of_beds. ')')]);
            }
        }

        // echo "<pre>";
        // print_r($remainingWardsCapacity);
        // return;

        // Check and calculate remaining capacity for selected male clinical wards, if any.
        if($request->has('male_clinical_wards')) {
            // Retrieve selected male ward capacities.
            $selectedMaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                          ->whereIn('id', $request->male_clinical_wards)
                                                          ->where('is_active', 1)
                                                          ->where('is_delete', 0)
                                                          ->get();

            // Process selected male ward capacities and remaining capacity.
            if(count($selectedMaleWardCapacity) > 0) {
                foreach($selectedMaleWardCapacity as $key => $value) {
                    // Check if ward is already occupied and calculate remaining capacity accordingly.
                    if(in_array($value->id, $maleClinicalWardIds)) {
                        foreach($occupiedBedsByMaleWards as $k => $v) {
                            if($value->id == $v->male_clinical_ward_id) {
                                $remainingMaleCapacity += ($value->no_of_beds - $v->occupied_male_beds);
                            }
                        }
                    } else {
                        $remainingMaleCapacity += $value->no_of_beds;
                    }
                }

                // Subjects required for the study and set appropriate error messages and prevent to not select any non required ward if capacity is available.
                if($remainingMaleCapacity < $study->no_of_male_subjects) {
                    $maleErrorMessage = 'Male clinical ward capacity exceeded';
                } else {
                    $maleWardSelection = false;
                }
            }
        }

        // Check and calculate remaining capacity for selected female clinical wards, if any.
        if($request->has('female_clinical_wards')) {
            // Retrieve selected female ward capacities.
            $selectedFemaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                            ->whereIn('id', $request->female_clinical_wards)
                                                            ->where('is_active', 1)
                                                            ->where('is_delete', 0)
                                                            ->get();

            // Process selected female ward capacities and remaining capacity.
            if(count($selectedFemaleWardCapacity) > 0) {
                foreach($selectedFemaleWardCapacity as $key => $value) {
                    // Check if ward is already occupied and calculate remaining capacity accordingly.
                    if(in_array($value->id, $femaleClinicalWardIds)) {
                        foreach($occupiedBedsByFemaleWards as $k => $v) {
                            if($value->id == $v->female_clinical_ward_id) {
                                $remainingFemaleCapacity += ($value->no_of_beds - $v->occupied_female_beds);
                            }
                        }
                    } else {
                        $remainingFemaleCapacity += $value->no_of_beds;
                    }
                }

                // Subjects required for the study and set appropriate error messages and prevent to not select any non required ward if capacity is available.
                if($remainingFemaleCapacity < $study->no_of_female_subjects) {
                    $femaleErrorMessage = 'Female clinical ward capacity exceeded';
                } else {
                    $femaleWardSelection = false;
                }
            }
        }

        // Return JSON response containing error messages, ward selection flags, study data, and remaining ward capacities.
        return response()->json(['maleErrorMessage' => $maleErrorMessage, 'femaleErrorMessage' => $femaleErrorMessage, 'maleWardSelection' => $maleWardSelection, 'femaleWardSelection' => $femaleWardSelection, 'studyData' => $study, 'clinicalWards' => $remainingWardsCapacity, 'maleSelectedWards' => $maleClinicalWardIds, 'femaleSelectedWards' => $femaleClinicalWardIds]);
    }

    /**
     * Save study slot of modal popup in calendar.
     * This function handles the saving of study slots selected through a modal popup in the calendar.
     *
     * @param  Request  $request The HTTP request containing the slot information.
     * @return \Illuminate\View\View Redirects back to the clinical calendar list view with success or error messages.
     */
    public function saveOpenStudySlotModalForCalendar(Request $request) {

        // Retrieve study details based available request study id.
        $study = Study::select('id', 'cr_location', 'pre_housing', 'post_housing')
                      ->where('id', $request->study_id)
                      ->first();

        // Check if subjects are assigned to either male or female.
        if(($request->totalMale > 0) || ($request->totalFemale > 0)) {
            // Calculate the check-out date and time based on check-in time and pre/post housing duration.
            $checkOutDateTime = date('Y-m-d H:i', strtotime($request->checkin_date_time. ($study->pre_housing + $study->post_housing) . 'hours'));

            // Retrieve existing study slot IDs for the same date and location.
            $studySlotExistIds = StudyClinicalSlotting::whereRaw('? between DATE(check_in_date_time) and DATE(check_out_date_time)', date('Y-m-d', strtotime($request->checkin_date_time)))
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->whereHas('studyNo', function($q) use($study){
                                                           $q->where('cr_location', $study->cr_location)
                                                             ->where('is_delete', 0);
                                                      })
                                                      ->pluck('id')
                                                      ->toArray();
            
            // Create a new study clinical slotting entry.
            $saveStudyClinicalSlotting = new StudyClinicalSlotting;
            $saveStudyClinicalSlotting->study_id = $request->study_id;
            $saveStudyClinicalSlotting->period_no = $request->period_no;
            $saveStudyClinicalSlotting->check_in_date_time = $request->checkin_date_time;
            $saveStudyClinicalSlotting->check_out_date_time = $checkOutDateTime;
            $saveStudyClinicalSlotting->created_by_user_id = Auth::guard('admin')->user()->id;
            $saveStudyClinicalSlotting->save();

            // Create a trail entry for the study clinical slotting.
            $saveStudyClinicalSlottingTrail = new StudyClinicalSlottingTrail;
            $saveStudyClinicalSlottingTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
            $saveStudyClinicalSlottingTrail->study_id = $request->study_id;
            $saveStudyClinicalSlottingTrail->period_no = $request->period_no;
            $saveStudyClinicalSlottingTrail->check_in_date_time = $request->checkin_date_time;
            $saveStudyClinicalSlottingTrail->check_out_date_time = $checkOutDateTime;
            $saveStudyClinicalSlottingTrail->created_by_user_id = Auth::guard('admin')->user()->id;
            $saveStudyClinicalSlottingTrail->save();

            if($request->has('male_clinical_ward_location')) {
                // Initialize variables.
                $occupiMaleSubject = 0;
                $totalMale = $request->totalMale;
                $totalOccupiedMaleSubjects = 0;

                // Iterate through each selected male clinical ward location.
                foreach($request->male_clinical_ward_location as $key => $value) {
                    // Retrieve total capacity of the ward.
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    // Calculate the total number of male subjects already occupyied in the ward.
                    $occupiedMaleWardSubjects = StudyMaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                    ->where('male_clinical_ward_id', $value)
                                                                    ->groupBy('male_clinical_ward_id')
                                                                    ->sum('no_of_subject');

                    // Calculate the remaining capacity of the ward.
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedMaleWardSubjects;

                    // Determine the number of subjects to be assigned to this ward.
                    if((count($request->male_clinical_ward_location) == 1) && ($totalMale <= $remainingWardCapacity)) {
                        $occupiMaleSubject = $totalMale;
                    } else {
                        $totalMale -= $remainingWardCapacity;
                        if($totalOccupiedMaleSubjects <= $request->totalMale) {
                            if($totalMale <= 0) {
                                $occupiMaleSubject = $request->totalMale - $totalOccupiedMaleSubjects;
                            } else {
                                $occupiMaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedMaleSubjects += $occupiMaleSubject;
                        }
                    }

                    // Save the male clinical ward assignment.
                    if($occupiMaleSubject > 0) {
                        $saveMaleClinicalWard = new StudyMaleSlottedWard;
                        $saveMaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveMaleClinicalWard->male_clinical_ward_id = $value;
                        $saveMaleClinicalWard->no_of_subject = $occupiMaleSubject;
                        $saveMaleClinicalWard->save();

                        // Create a trail entry for the male clinical ward assignment.
                        $saveMaleClinicalWardTrail = new StudyMaleSlottedWardTrail;
                        $saveMaleClinicalWardTrail->study_male_slotted_ward_id = $saveMaleClinicalWard->id;
                        $saveMaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveMaleClinicalWardTrail->male_clinical_ward_id = $value;
                        $saveMaleClinicalWardTrail->no_of_subject = $occupiMaleSubject;
                        $saveMaleClinicalWardTrail->save();
                    }
                }
            }

            // Process and save female clinical ward assignments.
            if($request->has('female_clinical_ward_location')) {
                // Initialize variables.
                $occupiFemaleSubject = 0;
                $totalFemale = $request->totalFemale;
                $totalOccupiedFemaleSubjects = 0;

                // Iterate through each selected female clinical ward location.
                foreach($request->female_clinical_ward_location as $key => $value) {
                    // Retrieve total capacity of the ward.
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    // Calculate the total number of female subjects already occupying the ward.
                    $occupiedFemaleWardSubjects = StudyFemaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                        ->where('female_clinical_ward_id', $value)
                                                                        ->groupBy('female_clinical_ward_id')
                                                                        ->sum('no_of_subject');

                    // Calculate the remaining capacity of the ward.
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedFemaleWardSubjects;

                    // Determine the number of subjects to be assigned to this ward.
                    if((count($request->female_clinical_ward_location) == 1) && ($totalFemale <= $remainingWardCapacity)) {
                        $occupiFemaleSubject = $totalFemale;
                    } else {
                        $totalFemale -= $remainingWardCapacity;
                        if($totalOccupiedFemaleSubjects <= $request->totalFemale) {
                            if($totalFemale <= 0) {
                                $occupiFemaleSubject = $request->totalFemale - $totalOccupiedFemaleSubjects;
                            } else {
                                $occupiFemaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedFemaleSubjects += $occupiFemaleSubject;
                        }
                    }

                    // Save the female clinical ward assignment.
                    if($occupiFemaleSubject > 0) {
                        $saveFemaleClinicalWard = new StudyFemaleSlottedWard;
                        $saveFemaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveFemaleClinicalWard->female_clinical_ward_id = $value;
                        $saveFemaleClinicalWard->no_of_subject = $occupiFemaleSubject;
                        $saveFemaleClinicalWard->save();

                        // Create a trail entry for the female clinical ward assignment.
                        $saveFemaleClinicalWardTrail = new StudyFemaleSlottedWardTrail;
                        $saveFemaleClinicalWardTrail->study_female_slotted_ward_id = $saveFemaleClinicalWard->id;
                        $saveFemaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveFemaleClinicalWardTrail->female_clinical_ward_id = $value;
                        $saveFemaleClinicalWard->no_of_subject = $occupiFemaleSubject;
                        $saveFemaleClinicalWardTrail->save();
                    }
                }
            }

            // Redirect with success message if the process is successful.
            return redirect(route('admin.clinicalCalendarList'))->with('messages', [
                [
                    'type' => 'success',
                    'title' => 'Study Slot',
                    'message' => 'Study slot successfully added!',
                ],
            ]);
        }

        // Redirect with error message if no wards are selected.
        return redirect(route('admin.clinicalCalendarList'))->with('messages', [
            [
                'type' => 'error',
                'title' => 'Study Slot',
                'message' => 'Please select wards!',
            ],
        ]);
    }

    /**
     * Open the modal for editing a study slot in the calendar.
     * This function retrieves the necessary data to populate the edit modal for a study slot.
     *
     * @param  int  $id The ID of the study slot to be edited.
     * @return \Illuminate\View\View Returns the view for the edit modal with the required data.
     */
    public function openEditStudySlotModalForCalendar($id) {

        // Initialize arrays to store male and female clinical ward IDs
        $maleClinicalWardIds = array();
        $femaleClinicalWardIds = array();

        // Retrieve the study slot information based on the provided ID
        $studySlot = StudyClinicalSlotting::where('id', $id)
                                          ->where('is_active', 1)
                                          ->where('is_delete', 0)
                                          ->with([
                                                'studyNo' => function($q) {
                                                    $q->select('id', 'study_no', 'study_design', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'washout_period', 'cr_location', 'study_condition', 'no_of_periods', 'pre_housing', 'post_housing', 'project_manager')
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->with([
                                                          'studyDesignName' => function($q) {
                                                                $q->select('id', 'para_value')
                                                                  ->where('is_active', 1)
                                                                  ->where('is_delete', 0);
                                                           },
                                                           'studyConditionName' => function($q) {
                                                                $q->select('id', 'para_value')
                                                                  ->where('is_active', 1)
                                                                  ->where('is_delete', 0);
                                                            },
                                                            'crLocationName' => function($q) {
                                                                $q->select('id', 'location_name')
                                                                  ->where('is_active', 1)
                                                                  ->where('is_delete', 0);
                                                            },
                                                            'projectManager' => function($q) {
                                                                $q->select('id', 'name', 'employee_code')
                                                                  ->where('is_active', 1)
                                                                  ->where('is_delete', 0);
                                                            }
                                                        ]);
                                                }
                                           ])
                                          ->first();

        // Retrieve clinical wards based on the study's location
        $clinicalWards = ClinicalWardMaster::select('id', 'ward_name', 'no_of_beds')
                                           ->where('location_id', $studySlot->studyNo->cr_location)
                                           ->where('is_active', 1)
                                           ->where('is_delete', 0)
                                           ->get();
        
        // Retrieve IDs of male clinical wards associated with the study slot
        $maleClinicalWardsIds = StudyMaleSlottedWard::where('study_clinical_slotting_id', $id)->pluck('male_clinical_ward_id')->toArray();
        
        // Retrieve IDs of female clinical wards associated with the study slot
        $femaleClinicalWardsIds = StudyFemaleSlottedWard::where('study_clinical_slotting_id', $id)->pluck('female_clinical_ward_id')->toArray();
        
        // Check for existing study slots within the same date range
        $studySlotExists = StudyClinicalSlotting::whereBetween('check_in_date_time', [date('Y-m-d', strtotime($studySlot->check_in_date_time)), date('Y-m-d', strtotime($studySlot->check_out_date_time))])
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->whereHas('studyNo', function($q) use($studySlot){
                                                     $q->where('cr_location', $studySlot->studyNo->cr_location)
                                                       ->where('is_delete', 0);
                                                })
                                                ->pluck('id')
                                                ->toArray();

        // echo "<pre>";
        // print_r($studySlotExists);
        // exit;

        // If there are existing study slots, calculate occupied beds for male clinical wards
        if(count($studySlotExists) > 0) {
            // Calculate occupied beds by male subjects in each male clinical ward
            $occupiedBedsByMaleWards = StudyMaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_male_beds, male_clinical_ward_id')
                                                           ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                           ->groupBy('male_clinical_ward_id')
                                                           ->get();

            // Update available beds for each male clinical ward based on occupied beds
            if(count($occupiedBedsByMaleWards) > 0) {
                foreach($occupiedBedsByMaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->male_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        foreach($clinicalWards as $k => $v) {
                            if($v->id == $value->male_clinical_ward_id) {
                                if($totalBeds->no_of_beds <= $value->occupied_male_beds) {
                                    $v->no_of_beds = 0;
                                } else {
                                    $v->no_of_beds = $totalBeds->no_of_beds - $value->occupied_male_beds;
                                }
                            }
                        }
                    }
                }
            }

            // Calculate occupied beds for female clinical wards
            $occupiedBedsByFemaleWards = StudyFemaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_female_beds, female_clinical_ward_id')
                                                               ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                               ->groupBy('female_clinical_ward_id')
                                                               ->get();

            // Update available beds for each female clinical ward based on occupied beds
            if(count($occupiedBedsByFemaleWards) > 0) {
                foreach($occupiedBedsByFemaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->female_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        foreach($clinicalWards as $k => $v) {
                            if($v->id == $value->female_clinical_ward_id) {
                                if($totalBeds->no_of_beds <= $value->occupied_female_beds) {
                                    $v->no_of_beds = 0;
                                } else {
                                    $v->no_of_beds = $totalBeds->no_of_beds - $value->occupied_female_beds;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Return the view for editing the clinical study slotting modal with the required data
        return view('admin.study.study_slotting.edit_clinical_study_slotting_modal', compact('studySlot', 'clinicalWards', 'maleClinicalWardsIds', 'femaleClinicalWardsIds'));
    }

    /**
     * Update the study slot modal for the calendar.
     * This function handles the updating of study slot information based on user input from the modal.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request containing the updated study slot data.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the clinical calendar list page after updating the study slot.
     */
    public function updateOpenStudySlotModalForCalendar(Request $request) {

        // Find the study slot to be updated based on the provided slot ID
        $updateStudySlot = StudyClinicalSlotting::findOrFail($request->slotId);

        // Check if there are male or female subjects selected for the study slot
        if(($request->totalMale > 0) || ($request->totalFemale > 0)) {
            // Calculate the check-out date time based on the check-in date time and housing durations
            $checkOutDateTime = date('Y-m-d H:i', strtotime($request->check_in_date_time. ($request->preHousing + $request->postHousing) . 'hours'));

            // Retrieve IDs of existing study slots within the same date range
            $studySlotExistIds = StudyClinicalSlotting::whereRaw('? between DATE(check_in_date_time) and DATE(check_out_date_time)', date('Y-m-d', strtotime($request->check_in_date_time)))
                                                      ->where('id', '!=', $request->slotId)
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->whereHas('studyNo', function($q) use($request){
                                                           $q->where('cr_location', $request->crLocation)
                                                             ->where('is_delete', 0);
                                                      })
                                                      ->pluck('id')
                                                      ->toArray();
            
            // Update the study slot information
            $updateStudySlot->check_in_date_time = $request->check_in_date_time;
            $updateStudySlot->study_id = $request->studyId;
            $updateStudySlot->period_no = $request->period_no;
            $updateStudySlot->check_out_date_time = $checkOutDateTime;
            $updateStudySlot->updated_by_user_id = Auth::guard('admin')->user()->id;
            $updateStudySlot->save();
            
            // Create a trail record for the updated study slot
            $updateStudySlotTrail = new StudyClinicalSlottingTrail;
            $updateStudySlotTrail->study_clinical_slotting_id = $updateStudySlot->id;
            $updateStudySlotTrail->check_in_date_time = $request->check_in_date_time;
            $updateStudySlotTrail->study_id = $request->studyId;
            $updateStudySlotTrail->period_no = $request->period_no;
            $updateStudySlotTrail->check_out_date_time = $checkOutDateTime;
            $updateStudySlotTrail->updated_by_user_id = Auth::guard('admin')->user()->id;
            $updateStudySlotTrail->save();
            
            // Handle male subjects
            if($request->has('male_clinical_ward_location')) {
                // Initialize variables
                $occupiMaleSubject = 0;
                $totalMale = $request->totalMale;
                $totalOccupiedMaleSubjects = 0;

                // Loop through selected male clinical ward locations
                foreach($request->male_clinical_ward_location as $key => $value) {
                    // Retrieve total ward capacity and occupied male subjects
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    $occupiedMaleWardSubjects = StudyMaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                    ->where('male_clinical_ward_id', $value)
                                                                    ->groupBy('male_clinical_ward_id')
                                                                    ->sum('no_of_subject');

                    // Calculate remaining ward capacity
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedMaleWardSubjects;

                    // Determine the number of male subjects to be assigned to the ward
                    if((count($request->male_clinical_ward_location) == 1) && ($totalMale <= $remainingWardCapacity)) {
                        $occupiMaleSubject = $totalMale;
                    } else {
                        $totalMale -= $remainingWardCapacity;
                        if($totalOccupiedMaleSubjects <= $request->totalMale) {
                            if($totalMale <= 0) {
                                $occupiMaleSubject = $request->totalMale - $totalOccupiedMaleSubjects;
                            } else {
                                $occupiMaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedMaleSubjects += $occupiMaleSubject;
                        }
                    }

                    // Update or create records for male subjects in the ward
                    if($occupiMaleSubject > 0) {
                        $updateStudyMaleSlottedWard = StudyMaleSlottedWard::where('study_clinical_slotting_id', $updateStudySlot->id)
                                                                          ->where('male_clinical_ward_id', $value)
                                                                          ->first();

                        if(!is_null($updateStudyMaleSlottedWard)) {
                            $updateStudyMaleSlottedWard->update(['no_of_subject' => $occupiMaleSubject]);
                        } else {
                            $updateStudyMaleSlottedWard = new StudyMaleSlottedWard;
                            $updateStudyMaleSlottedWard->study_clinical_slotting_id = $updateStudySlot->id;
                            $updateStudyMaleSlottedWard->male_clinical_ward_id = $value;
                            $updateStudyMaleSlottedWard->no_of_subject = $occupiMaleSubject;
                            $updateStudyMaleSlottedWard->save();
                        }

                        // Create trail record for male subjects
                        $updateMaleClinicalWardTrail = new StudyMaleSlottedWardTrail;
                        $updateMaleClinicalWardTrail->study_male_slotted_ward_id = $updateStudyMaleSlottedWard->id;
                        $updateMaleClinicalWardTrail->study_clinical_slotting_id = $updateStudySlot->id;
                        $updateMaleClinicalWardTrail->male_clinical_ward_id = $value;
                        $updateMaleClinicalWardTrail->no_of_subject = $occupiMaleSubject;
                        $updateMaleClinicalWardTrail->save();
                    }
                }

                // Delete records for male subjects ward that are not selected
                StudyMaleSlottedWard::where('study_clinical_slotting_id', $updateStudySlot->id)->whereNotIn('male_clinical_ward_id', $request->male_clinical_ward_location)->delete();
            }

            // Handle female subjects
            if($request->has('female_clinical_ward_location')) {
                // Initialize variables
                $occupiFemaleSubject = 0;
                $totalFemale = $request->totalFemale;
                $totalOccupiedFemaleSubjects = 0;

                // Loop through selected female clinical ward locations
                foreach($request->female_clinical_ward_location as $key => $value) {
                    // Retrieve total ward capacity and occupied female subjects
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    $occupiedFemaleWardSubjects = StudyFemaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                        ->where('female_clinical_ward_id', $value)
                                                                        ->groupBy('female_clinical_ward_id')
                                                                        ->sum('no_of_subject');

                    // Calculate remaining ward capacity
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedFemaleWardSubjects;

                    // Determine the number of female subjects to be assigned to the ward
                    if((count($request->female_clinical_ward_location) == 1) && ($totalFemale <= $remainingWardCapacity)) {
                        $occupiFemaleSubject = $totalFemale;
                    } else {
                        $totalFemale -= $remainingWardCapacity;
                        if($totalOccupiedFemaleSubjects <= $request->totalFemale) {
                            if($totalFemale <= 0) {
                                $occupiFemaleSubject = $request->totalFemale - $totalOccupiedFemaleSubjects;
                            } else {
                                $occupiFemaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedFemaleSubjects += $occupiFemaleSubject;
                        }
                    }

                    // Update or create records for female subjects in the ward
                    if($occupiFemaleSubject > 0) {
                        $updateStudyFemaleSlottedWard = StudyFemaleSlottedWard::where('study_clinical_slotting_id', $updateStudySlot->id)
                                                                              ->where('female_clinical_ward_id', $value)
                                                                              ->first();

                        if(!is_null($updateStudyFemaleSlottedWard)) {
                            $updateStudyFemaleSlottedWard->update(['no_of_subject' => $occupiFemaleSubject]);
                        } else {
                            $updateStudyFemaleSlottedWard = new StudyFemaleSlottedWard;
                            $updateStudyFemaleSlottedWard->study_clinical_slotting_id = $updateStudySlot->id;
                            $updateStudyFemaleSlottedWard->female_clinical_ward_id = $value;
                            $updateStudyFemaleSlottedWard->no_of_subject = $occupiFemaleSubject;
                            $updateStudyFemaleSlottedWard->save();
                        }

                        // Create trail record for female subjects
                        $updateMaleClinicalWardTrail = new StudyFemaleSlottedWardTrail;
                        $updateMaleClinicalWardTrail->study_female_slotted_ward_id = $updateStudyFemaleSlottedWard->id;
                        $updateMaleClinicalWardTrail->study_clinical_slotting_id = $updateStudySlot->id;
                        $updateMaleClinicalWardTrail->female_clinical_ward_id = $value;
                        $updateMaleClinicalWardTrail->no_of_subject = $occupiFemaleSubject;
                        $updateMaleClinicalWardTrail->save();
                    }
                }

                // Delete records for female subjects ward that are not selected
                StudyFemaleSlottedWard::where('study_clinical_slotting_id', $updateStudySlot->id)->whereNotIn('female_clinical_ward_id', $request->female_clinical_ward_location)->delete();
            }

            // Redirect back to the clinical calendar list page with success message
            return redirect(route('admin.clinicalCalendarList'))->with('messages', [
                [
                    'type' => 'success',
                    'title' => 'Study Slot',
                    'message' => 'Study slot successfully updated!',
                ],
            ]);
        }

        // Redirect back to the clinical calendar list page with error message if no wards are selected
        return redirect(route('admin.clinicalCalendarList'))->with('messages', [
            [
                'type' => 'error',
                'title' => 'Study Slot',
                'message' => 'Please select wards!',
            ],
        ]);
    }

    /**
     * Delete a clinical calendar slot.
     * This function deletes a study slot from the clinical calendar based on the provided slot ID.
     *
     * @param  int  $id The ID of the study slot to be deleted.
     * @return string Returns 'true' if the deletion was successful, 'false' otherwise.
     */
    public function deleteClinicalCalendarSlot($id) {

        // Retrieve the study slot to be deleted
        $deleteStudySlot = StudyClinicalSlotting::where('id', $id)->first();

        // Retrieve the study ID and period number for further use
        $studyId = $deleteStudySlot->study_id;
        $periodNo = $deleteStudySlot->period_no;

        // Check if the study slot exists
        if (!is_null($deleteStudySlot)) {
            // Soft delete the study slots with a period number greater than or equal to the current one for the same study
            $deleteStudySlot->where('period_no', '>=', $periodNo)->where('study_id', $studyId)->update(['is_delete' => 1, 'updated_by_user_id' => Auth::guard('admin')->user()->id]);

            // Retrieve the deleted study slots
            $deletedStudySlots = StudyClinicalSlotting::where('study_id', $studyId)
                                                      ->where('period_no', '>=', $periodNo)
                                                      ->where('is_delete', 1)
                                                      ->get();
            
            // Create trail records for the deleted study slots
            if(!is_null($deletedStudySlots)) {
                foreach($deletedStudySlots as $dstk => $dstv) {
                    $deleteStudyClinicalTrail = new StudyClinicalSlottingTrail();
                    $deleteStudyClinicalTrail->study_clinical_slotting_id = $dstv->id;
                    $deleteStudyClinicalTrail->study_id = $dstv->study_id;
                    $deleteStudyClinicalTrail->period_no = $dstv->period_no;
                    $deleteStudyClinicalTrail->check_in_date_time = $dstv->check_in_date_time;
                    $deleteStudyClinicalTrail->check_out_date_time = $dstv->check_out_date_time;
                    $deleteStudyClinicalTrail->updated_by_user_id = Auth::guard('admin')->user()->id;
                    $deleteStudyClinicalTrail->is_delete = 1;
                    $deleteStudyClinicalTrail->save();
                }
            }
        }

        // Return true if the deletion was successful, false otherwise
        return $deleteStudySlot ? 'true' : 'false';
    }

    /**
     * Check clinical wards capacity for calendar.
     * This function checks the capacity of clinical wards for a given date and slot ID, ensuring that the selection of wards is valid and within capacity limits.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request containing the clinical wards data to be checked.
     * @return \Illuminate\Http\JsonResponse Returns a JSON response indicating the validity of the selected check-in date, any errors related to male or female ward selection, the selection status of male and female wards, the remaining capacity of wards, and the IDs of selected male and female wards.
     */
    public function checkClinicalWardsCapacityForCalendar(Request $request) {

        $maleErrorMessage = '';
        $femaleErrorMessage = '';
        $isSelectedCheckinDateValid = true;
        $maleClinicalWardIds = array();
        $femaleClinicalWardIds = array();
        $remainingMaleCapacity = 0;
        $remainingFemaleCapacity = 0;
        $maleWardSelection = true;
        $femaleWardSelection = true;
        $remainingWardsCapacity = array();

        $slot = StudyClinicalSlotting::where('id', $request->slotId)->first();

        $studyData = Study::select('id', 'washout_period', 'pre_housing', 'post_housing')
                          ->where('id', $slot->study_id)
                          ->where('is_delete', 0)
                          ->first();

        // $checkOutDate = date('Y-m-d', strtotime)

        $studySlotExists = StudyClinicalSlotting::whereBetween('check_in_date_time', [date('Y-m-d', strtotime($request->checkin_date_time))])
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->whereHas('studyNo', function($q) use($request){
                                                     $q->where('cr_location', $request->cr_location)
                                                       ->where('is_delete', 0);
                                                })
                                                ->pluck('id')
                                                ->toArray();

        // $slot = StudyClinicalSlotting::where('id', $request->slotId)->first();

        // $studyData = Study::select('id', 'washout_period', 'pre_housing', 'post_housing')
        //                   ->where('id', $slot->study_id)
        //                   ->where('is_delete', 0)
        //                   ->first();

        if(!is_null($slot)) {
            $otherExistsSlot = StudyClinicalSlotting::where('period_no', '>', $slot->period_no)
                                                    ->where('study_id', $slot->study_id)
                                                    ->where('is_active', 1)
                                                    ->where('is_delete', 0)
                                                    ->first();

            if(!is_null($otherExistsSlot)) {
                $checkOutDateTime = date('Y-m-d H:i', strtotime($request->checkin_date_time. ($studyData->pre_housing + $studyData->post_housing) . 'hours'));

                if($checkOutDateTime >= date('Y-m-d H:i', strtotime($otherExistsSlot->check_in_date_time))) {
                    $isSelectedCheckinDateValid = false;
                }
            }

            // $checkOutDateTime = date('Y-m-d H:i', strtotime($request->checkin_date_time. ($studyData->pre_housing + $studyData->post_housing) . 'hours'));
            // return $checkOutDateTime;
            // echo $checkOutDateTime;
            // echo date('Y-m-d H:i', strtotime($otherExistsSlot->check_in_date_time));

            // if($checkOutDateTime > date('Y-m-d H:i', strtotime($otherExistsSlot->check_in_date_time))) {
            //     $isSelectedCheckinDateValid = false;
            // }

            // if(!is_null($otherExistsSlot)) {
            //     $startDateTime = $otherExistsSlot->check_in_date_time;
            //     $endDateTime = $otherExistsSlot->check_out_date_time;
            //     $previousSlotStartDateTime = new DateTime($startDateTime);
            //     $previousSlotEndDateTime = new DateTime($endDateTime);
            //     $requiredInterval = $previousSlotStartDateTime->diff($previousSlotEndDateTime);
            //     $requiredDays = $requiredInterval->format('%a');

            //     $selectedDateTime = $request->checkin_date_time;
            //     $convertedDateTime = new DateTime($selectedDateTime);
            //     $interval = $previousSlotStartDateTime->diff($convertedDateTime);
            //     $days = $interval->format('%a');

            //     $daysDifference = $requiredDays - $days;

            //     // echo $daysDifference. "<br>";
            //     // echo $days. "<br>";
            //     // return;

            //     echo $requiredDays - $days. "<br>";
            //     echo $days. "<br>";
            //     return;

            //     if($daysDifference > 0) {
            //         $isSelectedCheckinDateValid = true;
            //     }
            //     //  else {
            //     //     $isSelectedCheckinDateValid = false;
            //     // }
            // }
            // else {
            //     $isSelectedCheckinDateValid = true;
            // }
        }

        if(count($studySlotExists) > 0) {
            $occupiedBedsByMaleWards = StudyMaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_male_beds, male_clinical_ward_id')
                                                           ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                           ->groupBy('male_clinical_ward_id')
                                                           ->get();

            $maleClinicalWardIds = $occupiedBedsByMaleWards->pluck('male_clinical_ward_id')->toArray();

            if(count($occupiedBedsByMaleWards) > 0) {
                foreach($occupiedBedsByMaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->male_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        if($totalBeds->no_of_beds <= $value->occupied_male_beds) {
                            array_push($remainingWardsCapacity, [$value->male_clinical_ward_id => 0]);
                        } else {
                            array_push($remainingWardsCapacity, [$value->male_clinical_ward_id => ($totalBeds->no_of_beds - $value->occupied_male_beds)]);
                        }
                    }
                }
            }

            $occupiedBedsByFemaleWards = StudyFemaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_female_beds, female_clinical_ward_id')
                                                               ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                               ->groupBy('female_clinical_ward_id')
                                                               ->get();

            $femaleClinicalWardIds = $occupiedBedsByFemaleWards->pluck('female_clinical_ward_id')->toArray();

            if(count($occupiedBedsByFemaleWards) > 0) {
                foreach($occupiedBedsByFemaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->female_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        if($totalBeds->no_of_beds <= $value->occupied_female_beds) {
                            array_push($remainingWardsCapacity, [$value->female_clinical_ward_id => 0]);
                        } else {
                            array_push($remainingWardsCapacity, [$value->female_clinical_ward_id => ($totalBeds->no_of_beds - $value->occupied_female_beds)]);
                        }
                    }
                }
            }
        }

        $clinicalWards = ClinicalWardMaster::select('id', 'no_of_beds')
                                           ->where('location_id', $request->cr_location)
                                           ->whereNotIn('id', array_merge($maleClinicalWardIds, $femaleClinicalWardIds))
                                           ->where('is_active', 1)
                                           ->where('is_delete', 0)
                                           ->orderBy('ward_name')
                                           ->get();

        if(!is_null($clinicalWards)) {
            foreach($clinicalWards as $cwk => $cwv) {
                array_push($remainingWardsCapacity, [$cwv->id => $cwv->no_of_beds]);
            }
        }

        if($request->has('male_clinical_wards')) {
            if(array_intersect($request->male_clinical_wards, $femaleClinicalWardIds)) {
                $maleErrorMessage = 'Please remove female occupied ward from selection';
            } else {
                $selectedMaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                              ->whereIn('id', $request->male_clinical_wards)
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0)
                                                              ->get();

                if(count($selectedMaleWardCapacity) > 0) {
                    foreach($selectedMaleWardCapacity as $key => $value) {
                        if(in_array($value->id, $maleClinicalWardIds)) {
                            foreach($occupiedBedsByMaleWards as $k => $v) {
                                if($value->id == $v->male_clinical_ward_id) {
                                    $remainingMaleCapacity += ($value->no_of_beds - $v->occupied_male_beds);
                                }
                            }
                        } else {
                            $remainingMaleCapacity += $value->no_of_beds;
                        }
                    }

                    if($remainingMaleCapacity < $request->no_of_male_subject) {
                        $maleErrorMessage = 'Male clinical ward capacity exceeded';
                    } else {
                        $maleWardSelection = false;
                    }
                }
            }
        }

        if($request->has('female_clinical_wards')) {
            if(array_intersect($request->female_clinical_wards, $maleClinicalWardIds)) {
                $femaleErrorMessage = 'Please remove male occupied ward from selection';
            } else {
                $selectedFemaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                                ->whereIn('id', $request->female_clinical_wards)
                                                                ->where('is_active', 1)
                                                                ->where('is_delete', 0)
                                                                ->get();

                if(count($selectedFemaleWardCapacity) > 0) {
                    foreach($selectedFemaleWardCapacity as $key => $value) {
                        if(in_array($value->id, $femaleClinicalWardIds)) {
                            foreach($occupiedBedsByFemaleWards as $k => $v) {
                                if($value->id == $v->female_clinical_ward_id) {
                                    $remainingFemaleCapacity += ($value->no_of_beds - $v->occupied_female_beds);
                                }
                            }
                        } else {
                            $remainingFemaleCapacity += $value->no_of_beds;
                        }
                    }

                    if($remainingFemaleCapacity < $request->no_of_female_subject) {
                        $femaleErrorMessage = 'Female clinical ward capacity exceeded';
                    } else {
                        $femaleWardSelection = false;
                    }
                }
            }
        }

        return response()->json(['isCheckinDateValid' => $isSelectedCheckinDateValid, 'maleErrorMessage' => $maleErrorMessage, 'femaleErrorMessage' => $femaleErrorMessage, 'maleWardSelection' => $maleWardSelection, 'femaleWardSelection' => $femaleWardSelection, 'remainingWardsCapacity' => $remainingWardsCapacity, 'maleSelectedWards' => $maleClinicalWardIds, 'femaleSelectedWards' => $femaleClinicalWardIds]);
    }

    /**
     * Add study slot.
     * This function retrieves study details and clinical ward list for a specific clinical research location.
     * It also calculates the expected check-in date for the new study slot based on the previous period's data.
     *
     * @param  int  $id The ID of the study for which the slot is to be added.
     * @return \Illuminate\View\View Returns a view with study and related data for the study slot modal popup.
     */
    public function addStudySlot($id) {
        
        // Initialize variables
        $expectedChekinDate = ''; // Expected check-in date

        // Retrieve study details
        $study = Study::select('id', 'study_no', 'study_design', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects', 'washout_period', 'cr_location', 'study_condition', 'no_of_periods', 'pre_housing', 'post_housing', 'project_manager')
                      ->where('id', $id)
                      ->where('is_delete', 0)
                      ->with([
                            'studyDesignName' => function($q) {
                                $q->select('id', 'para_value')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'studyConditionName' => function($q) {
                                $q->select('id', 'para_value')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'crLocationName' => function($q) {
                                $q->select('id', 'location_name')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            },
                            'projectManager' => function($q) {
                                $q->select('id', 'name', 'employee_code')
                                  ->where('is_active', 1)
                                  ->where('is_delete', 0);
                            }
                      ])
                      ->first();
        
        // Retrieve clinical ward list for the CR location
        $crClinicalWardList = ClinicalWardMaster::select('id', 'ward_name', 'no_of_beds')
                                                ->where('location_id', $study->cr_location)
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->orderBy('ward_name')
                                                ->get();

        // Count existing study slots and determine the period start from
        $studySlotCount = StudyClinicalSlotting::where('study_id', $study->id)->where('is_active', 1)->where('is_delete', 0)->count();
        $studyPeriodStartFrom = (($studySlotCount == 0) ? ($studySlotCount = 1) : ($studySlotCount + 1));

        // Retrieve the last study period's expected check-in date
        $studyPeriodExist = StudyClinicalSlotting::where('study_id', $study->id)->where('is_active', 1)->where('is_delete', 0)->orderBy('id', 'DESC')->first();
        
        // Store expected checkin date
        if(!is_null($studyPeriodExist)) {
            $expectedChekinDate = date('Y-m-d\TH:i', strtotime($studyPeriodExist->check_in_date_time .($study->pre_housing). 'hours' .($study->washout_period). 'days'));
        } else {
            $expectedChekinDate = date('Y-m-d\TH:i');
        }

        // Return view with study and related data for modal popup
        return view('admin.study.study_slotting.study_slotting_modal', compact('study', 'crClinicalWardList', 'studyPeriodStartFrom', 'expectedChekinDate'));
    }

    /**
     * Save study slot.
     * This function handles the saving of a new study slot including the allocation of male and female subjects to clinical wards.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request containing the data to be saved for the new study slot.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the study slot list page with success or error message.
     */
    public function saveStudySlot(Request $request) {

        // Check if there are male or female subjects to allocate
        if(($request->totalMale > 0) || ($request->totalFemale > 0)) {
            // Calculate the check-out date time based on check-in date time and housing durations
            $checkOutDateTime = date('Y-m-d H:i', strtotime($request->check_in_date_time. ($request->preHousing + $request->postHousing) . 'hours'));

            // Retrieve IDs of existing study slots overlapping with the new slot's check-in date
            $studySlotExistIds = StudyClinicalSlotting::whereRaw('? between DATE(check_in_date_time) and DATE(check_out_date_time)', date('Y-m-d', strtotime($request->check_in_date_time)))
                                                      ->where('is_active', 1)
                                                      ->where('is_delete', 0)
                                                      ->whereHas('studyNo', function($q) use($request){
                                                           $q->where('cr_location', $request->crLocation)
                                                             ->where('is_delete', 0);
                                                      })
                                                      ->pluck('id')
                                                      ->toArray();
            
            // Create a new study clinical slotting instance
            $saveStudyClinicalSlotting = new StudyClinicalSlotting;
            $saveStudyClinicalSlotting->study_id = $request->studyId;
            $saveStudyClinicalSlotting->period_no = $request->period_no;
            $saveStudyClinicalSlotting->check_in_date_time = $request->check_in_date_time;
            $saveStudyClinicalSlotting->check_out_date_time = $checkOutDateTime;
            $saveStudyClinicalSlotting->created_by_user_id = Auth::guard('admin')->user()->id;
            $saveStudyClinicalSlotting->save();

            // Create a trail record for the new study clinical slotting instance
            $saveStudyClinicalSlottingTrail = new StudyClinicalSlottingTrail;
            $saveStudyClinicalSlottingTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
            $saveStudyClinicalSlottingTrail->study_id = $request->studyId;
            $saveStudyClinicalSlottingTrail->period_no = $request->period_no;
            $saveStudyClinicalSlottingTrail->check_in_date_time = $request->check_in_date_time;
            $saveStudyClinicalSlottingTrail->check_out_date_time = $checkOutDateTime;
            $saveStudyClinicalSlottingTrail->created_by_user_id = Auth::guard('admin')->user()->id;
            $saveStudyClinicalSlottingTrail->save();

            // Allocate male subjects to clinical wards
            if($request->has('male_clinical_ward_location')) {
                $occupiMaleSubject = 0;
                $totalMale = $request->totalMale;
                $totalOccupiedMaleSubjects = 0;

                foreach($request->male_clinical_ward_location as $key => $value) {
                    // Calculate remaining capacity in the ward
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    $occupiedMaleWardSubjects = StudyMaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                    ->where('male_clinical_ward_id', $value)
                                                                    ->groupBy('male_clinical_ward_id')
                                                                    ->sum('no_of_subject');

                    // Allocate subjects based on ward capacity and remaining male subjects
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedMaleWardSubjects;

                    if((count($request->male_clinical_ward_location) == 1) && ($totalMale <= $remainingWardCapacity)) {
                        $occupiMaleSubject = $totalMale;
                    } else {
                        $totalMale -= $remainingWardCapacity;
                        if($totalOccupiedMaleSubjects <= $request->totalMale) {
                            if($totalMale <= 0) {
                                $occupiMaleSubject = $request->totalMale - $totalOccupiedMaleSubjects;
                            } else {
                                $occupiMaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedMaleSubjects += $occupiMaleSubject;
                        }
                    }

                    // Save allocation details
                    if($occupiMaleSubject > 0) {
                        $saveMaleClinicalWard = new StudyMaleSlottedWard;
                        $saveMaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveMaleClinicalWard->male_clinical_ward_id = $value;
                        $saveMaleClinicalWard->no_of_subject = $occupiMaleSubject;
                        $saveMaleClinicalWard->save();

                        $saveMaleClinicalWardTrail = new StudyMaleSlottedWardTrail;
                        $saveMaleClinicalWardTrail->study_male_slotted_ward_id = $saveMaleClinicalWard->id;
                        $saveMaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveMaleClinicalWardTrail->male_clinical_ward_id = $value;
                        $saveMaleClinicalWardTrail->no_of_subject = $occupiMaleSubject;
                        $saveMaleClinicalWardTrail->save();
                    }
                }
            }

            if($request->has('female_clinical_ward_location')) {
                $occupiFemaleSubject = 0;
                $totalFemale = $request->totalFemale;
                $totalOccupiedFemaleSubjects = 0;

                foreach($request->female_clinical_ward_location as $key => $value) {
                    // Calculate remaining capacity in the ward
                    $totalWardCapacity = ClinicalWardMaster::where('id', $value)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    $occupiedFemaleWardSubjects = StudyFemaleSlottedWard::whereIn('study_clinical_slotting_id', $studySlotExistIds)
                                                                        ->where('female_clinical_ward_id', $value)
                                                                        ->groupBy('female_clinical_ward_id')
                                                                        ->sum('no_of_subject');

                    // Allocate subjects based on ward capacity and remaining female subjects
                    $remainingWardCapacity = $totalWardCapacity->no_of_beds - $occupiedFemaleWardSubjects;

                    if((count($request->female_clinical_ward_location) == 1) && ($totalFemale <= $remainingWardCapacity)) {
                        $occupiFemaleSubject = $totalFemale;
                    } else {
                        $totalFemale -= $remainingWardCapacity;
                        if($totalOccupiedFemaleSubjects <= $request->totalFemale) {
                            if($totalFemale <= 0) {
                                $occupiFemaleSubject = $request->totalFemale - $totalOccupiedFemaleSubjects;
                            } else {
                                $occupiFemaleSubject = $remainingWardCapacity;
                            }
                            $totalOccupiedFemaleSubjects += $occupiFemaleSubject;
                        }
                    }

                    // Save allocation details
                    if($occupiFemaleSubject > 0) {
                        $saveFemaleClinicalWard = new StudyFemaleSlottedWard;
                        $saveFemaleClinicalWard->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveFemaleClinicalWard->female_clinical_ward_id = $value;
                        $saveFemaleClinicalWard->no_of_subject = $occupiFemaleSubject;
                        $saveFemaleClinicalWard->save();

                        $saveFemaleClinicalWardTrail = new StudyFemaleSlottedWardTrail;
                        $saveFemaleClinicalWardTrail->study_female_slotted_ward_id = $saveFemaleClinicalWard->id;
                        $saveFemaleClinicalWardTrail->study_clinical_slotting_id = $saveStudyClinicalSlotting->id;
                        $saveFemaleClinicalWardTrail->female_clinical_ward_id = $value;
                        $saveFemaleClinicalWard->no_of_subject = $occupiFemaleSubject;
                        $saveFemaleClinicalWardTrail->save();
                    }
                }
            }

            // Redirect with success message if wards are selected
            return redirect(route('admin.studySlotList'))->with('messages', [
                [
                    'type' => 'success',
                    'title' => 'Study Slot',
                    'message' => 'Study slot successfully added!',
                ],
            ]);
        }

        // Redirect with error message if no wards are selected
        return redirect(route('admin.studySlotList'))->with('messages', [
            [
                'type' => 'error',
                'title' => 'Study Slot',
                'message' => 'Please select wards!',
            ],
        ]);
    }

    /**
     * Delete study slot.
     * This function handles the deletion of a study slot and creates trail entries for the deleted slots.
     *
     * @param  string  $id The base64 encoded ID of the study slot to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the clinical slotting list page with a success message.
     */
    public function deleteStudySlot($id) {

        // Retrieve the study slot to delete using the base64 decoded ID
        $deleteStudySlot = StudyClinicalSlotting::where('id', base64_decode($id))->first();
        $studyId = $deleteStudySlot->study_id;
        $periodNo = $deleteStudySlot->period_no;

        // Check if the study slot exists
        if(!is_null($deleteStudySlot)) {
            // Update the study slot and mark it as deleted
            $deleteStudySlot->where('period_no', '>=', $periodNo)->where('study_id', $studyId)->update(['is_delete' => 1, 'updated_by_user_id' => Auth::guard('admin')->user()->id]);

            // Retrieve all deleted study slots for the same study and period
            $deletedStudySlots = StudyClinicalSlotting::where('study_id', $studyId)
                                                      ->where('period_no', '>=', $periodNo)
                                                      ->where('is_delete', 1)
                                                      ->get();
            
            // Check if there are deleted study slots
            if(!is_null($deletedStudySlots)) {
                // Create a trail entry for each deleted study slot
                foreach($deletedStudySlots as $dstk => $dstv) {
                    $deleteStudyClinicalTrail = new StudyClinicalSlottingTrail();
                    $deleteStudyClinicalTrail->study_clinical_slotting_id = $dstv->id;
                    $deleteStudyClinicalTrail->study_id = $dstv->study_id;
                    $deleteStudyClinicalTrail->period_no = $dstv->period_no;
                    $deleteStudyClinicalTrail->check_in_date_time = $dstv->check_in_date_time;
                    $deleteStudyClinicalTrail->check_out_date_time = $dstv->check_out_date_time;
                    $deleteStudyClinicalTrail->updated_by_user_id = Auth::guard('admin')->user()->id;
                    $deleteStudyClinicalTrail->is_delete = 1;
                    $deleteStudyClinicalTrail->save();
                }
            }
        }

        // Redirect with a success message
        return redirect(route('admin.clinicalSlottingList'))->with('messages', [
            [
                'type' => 'success',
                'title' => 'Clinical Slotting',
                'message' => 'Clinical slot successfully deleted!',
            ],
        ]);
    }

    /**
     * Check clinical wards capacity.
     * This function checks the capacity of clinical wards based on user input and existing study slots.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request containing the selected clinical wards and other parameters.
     * @return \Illuminate\Http\JsonResponse Returns JSON response with capacity status and error messages.
     */
    public function checkClinicalWardsCapacity(Request $request) {

        // Initialize variables for error messages and ward capacities
        $maleErrorMessage = '';
        $femaleErrorMessage = '';
        $maleClinicalWardIds = array();
        $femaleClinicalWardIds = array();
        $remainingMaleCapacity = 0;
        $remainingFemaleCapacity = 0;
        $maleWardSelection = true;
        $femaleWardSelection = true;
        $remainingWardsCapacity = array();

        // Check for existing study slots overlapping with the requested check-in date
        $studySlotExists = StudyClinicalSlotting::whereRaw('? between DATE(check_in_date_time) and DATE(check_out_date_time)', date('Y-m-d', strtotime($request->checkin_date_time)))
                                                ->where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->whereHas('studyNo', function($q) use($request){
                                                     $q->where('cr_location', $request->cr_location)
                                                       ->where('is_delete', 0);
                                                })
                                                ->pluck('id')
                                                ->toArray();

        if(count($studySlotExists) > 0) {
            // Calculate occupied beds by male wards
            $occupiedBedsByMaleWards = StudyMaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_male_beds, male_clinical_ward_id')
                                                           ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                           ->groupBy('male_clinical_ward_id')
                                                           ->get();

            // Extract male clinical ward IDs and calculate remaining capacity
            $maleClinicalWardIds = $occupiedBedsByMaleWards->pluck('male_clinical_ward_id')->toArray();

            if(count($occupiedBedsByMaleWards) > 0) {
                foreach($occupiedBedsByMaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->male_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        array_push($remainingWardsCapacity, [$value->male_clinical_ward_id => ($totalBeds->no_of_beds - $value->occupied_male_beds)]);
                    }
                }
            }

            // Calculate occupied beds by female wards
            $occupiedBedsByFemaleWards = StudyFemaleSlottedWard::selectRaw('SUM(no_of_subject) as occupied_female_beds, female_clinical_ward_id')
                                                               ->whereIn('study_clinical_slotting_id', $studySlotExists)
                                                               ->groupBy('female_clinical_ward_id')
                                                               ->get();

            // Extract female clinical ward IDs and calculate remaining capacity
            $femaleClinicalWardIds = $occupiedBedsByFemaleWards->pluck('female_clinical_ward_id')->toArray();

            if(count($occupiedBedsByFemaleWards) > 0) {
                foreach($occupiedBedsByFemaleWards as $key => $value) {
                    $totalBeds = ClinicalWardMaster::where('id', $value->female_clinical_ward_id)->where('is_active', 1)->where('is_delete', 0)->first('no_of_beds');
                    if(!is_null($totalBeds)) {
                        array_push($remainingWardsCapacity, [$value->female_clinical_ward_id => ($totalBeds->no_of_beds - $value->occupied_female_beds)]);
                    }
                }
            }
        }

        // Retrieve remaining ward capacities for unoccupied wards
        $clinicalWards = ClinicalWardMaster::select('id', 'no_of_beds')
                                           ->where('location_id', $request->cr_location)
                                           ->whereNotIn('id', array_merge($maleClinicalWardIds, $femaleClinicalWardIds))
                                           ->where('is_active', 1)
                                           ->where('is_delete', 0)
                                           ->orderBy('ward_name')
                                           ->get();

        if(!is_null($clinicalWards)) {
            foreach($clinicalWards as $cwk => $cwv) {
                array_push($remainingWardsCapacity, [$cwv->id => $cwv->no_of_beds]);
            }
        }

        // Check for selected male clinical wards and calculate remaining capacity
        if($request->has('male_clinical_wards')) {
            if(array_intersect($request->male_clinical_wards, $femaleClinicalWardIds)) {
                $maleErrorMessage = 'Please remove female occupied ward from selection';
            } else {
                $selectedMaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                              ->whereIn('id', $request->male_clinical_wards)
                                                              ->where('is_active', 1)
                                                              ->where('is_delete', 0)
                                                              ->get();

                if(count($selectedMaleWardCapacity) > 0) {
                    foreach($selectedMaleWardCapacity as $key => $value) {
                        if(in_array($value->id, $maleClinicalWardIds)) {
                            foreach($occupiedBedsByMaleWards as $k => $v) {
                                if($value->id == $v->male_clinical_ward_id) {
                                    $remainingMaleCapacity += ($value->no_of_beds - $v->occupied_male_beds);
                                }
                            }
                        } else {
                            $remainingMaleCapacity += $value->no_of_beds;
                        }
                    }

                    if($remainingMaleCapacity < $request->no_of_male_subject) {
                        $maleErrorMessage = 'Male clinical ward capacity exceeded';
                    } else {
                        $maleWardSelection = false;
                    }
                }
            }
        }

        // Check for selected female clinical wards and calculate remaining capacity
        if($request->has('female_clinical_wards')) {
            if(array_intersect($request->female_clinical_wards, $maleClinicalWardIds)) {
                $femaleErrorMessage = 'Please remove male occupied ward from selection';
            } else {
                $selectedFemaleWardCapacity = ClinicalWardMaster::select('id', 'no_of_beds')
                                                                ->whereIn('id', $request->female_clinical_wards)
                                                                ->where('is_active', 1)
                                                                ->where('is_delete', 0)
                                                                ->get();

                if(count($selectedFemaleWardCapacity) > 0) {
                    foreach($selectedFemaleWardCapacity as $key => $value) {
                        if(in_array($value->id, $femaleClinicalWardIds)) {
                            foreach($occupiedBedsByFemaleWards as $k => $v) {
                                if($value->id == $v->female_clinical_ward_id) {
                                    $remainingFemaleCapacity += ($value->no_of_beds - $v->occupied_female_beds);
                                }
                            }
                        } else {
                            $remainingFemaleCapacity += $value->no_of_beds;
                        }
                    }

                    if($remainingFemaleCapacity < $request->no_of_female_subject) {
                        $femaleErrorMessage = 'Female clinical ward capacity exceeded';
                    } else {
                        $femaleWardSelection = false;
                    }
                }
            }
        }

        // Return JSON response with capacity status and error messages
        return response()->json(['maleErrorMessage' => $maleErrorMessage, 'femaleErrorMessage' => $femaleErrorMessage, 'maleWardSelection' => $maleWardSelection, 'femaleWardSelection' => $femaleWardSelection, 'remainingWardsCapacity' => $remainingWardsCapacity]);
    }
}
