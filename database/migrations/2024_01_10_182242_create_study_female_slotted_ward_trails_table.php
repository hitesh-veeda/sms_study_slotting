<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyFemaleSlottedWardTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql1')->create('study_female_slotted_ward_trails', function (Blueprint $table) {
            $table->id();
            $table->integer('study_female_slotted_ward_id');
            $table->integer('study_clinical_slotting_id');
            $table->integer('female_clinical_ward_id');
            $table->integer('no_of_subject')->nullable();
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
        Schema::connection('mysql1')->dropIfExists('study_female_slotted_ward_trails');
    }
}
