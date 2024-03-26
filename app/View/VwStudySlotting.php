<?php

namespace App\View;

use Illuminate\Database\Eloquent\Model;

class VwStudySlotting extends Model
{
    public $table = "vw_study_slotting";

    public function studySlotting(){
        return $this->hasMany('App\Models\StudyClinicalSlotting', 'study_id', 'study_id');
    }
}
