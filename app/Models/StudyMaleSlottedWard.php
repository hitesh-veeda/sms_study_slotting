<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaleSlottedWard extends Model
{
    use HasFactory;

    protected $table = 'study_male_slotted_wards';

    protected $connection = 'mysql';

    public function maleLocationName() {
        return $this->hasOne('App\Models\ClinicalWardMaster', 'id', 'male_clinical_ward_id');
    }
}
