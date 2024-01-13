<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyFemaleSlottedWard extends Model
{
    use HasFactory;

    protected $table = 'study_female_slotted_wards';

    protected $connection = 'mysql';

    public function femaleLocationName() {
        return $this->hasOne('App\Models\ClinicalWardMaster', 'id', 'female_clinical_ward_id');
    }
}
