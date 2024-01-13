<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaleSlottedWardTrail extends Model
{
    use HasFactory;

    protected $table = 'study_male_slotted_ward_trails';

    protected $connection = 'mysql1';
}
