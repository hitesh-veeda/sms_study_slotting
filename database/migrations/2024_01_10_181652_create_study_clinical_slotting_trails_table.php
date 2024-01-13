<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyClinicalSlottingTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql1')->create('study_clinical_slotting_trails', function (Blueprint $table) {
            $table->id();
            $table->integer('study_clinical_slotting_id');
            $table->integer('study_id');
            $table->integer('period_no');
            $table->timestamp('check_in_date_time')->nullable();
            $table->timestamp('check_out_date_time')->nullable();
            $table->integer('created_by_user_id')->nullable();
            $table->integer('updated_by_user_id')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_delete')->default(0);
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
        Schema::connection('mysql1')->dropIfExists('study_clinical_slotting_trails');
    }
}
