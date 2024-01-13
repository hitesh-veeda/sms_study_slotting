<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyMaleSlottedWardTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql1')->create('study_male_slotted_ward_trails', function (Blueprint $table) {
            $table->id();
            $table->integer('study_male_slotted_ward_id');
            $table->integer('study_clinical_slotting_id');
            $table->integer('male_clinical_ward_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql1')->dropIfExists('study_male_slotted_ward_trails');
    }
}
