<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyClinicalSlotting extends Model
{
    use HasFactory;

    protected $table = 'study_clinical_slottings';

    protected $connection = 'mysql';

    protected $guarded = ['id'];

    public function studyNo() {
        return $this->hasOne('App\Models\Study', 'id', 'study_id');
    }

    public function maleClinicalWards() {
        return $this->hasMany('App\Models\StudyMaleSlottedWard', 'study_clinical_slotting_id', 'id');
    }

    public function femaleClinicalWards() {
        return $this->hasMany('App\Models\StudyFemaleSlottedWard', 'study_clinical_slotting_id', 'id');
    }
}
