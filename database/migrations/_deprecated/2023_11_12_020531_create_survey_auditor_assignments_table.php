<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_auditor_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->comment('The auditor user_id');
            $table->bigInteger('survey_id')->nullable();
            $table->bigInteger('company_id')->default(0);
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new')->comment('The status of the survey task for this auditor');
            $table->bigInteger('surveyor_assignment_id')->nullable()->comment('Form the survey_surveyor_assignments id. It`s useful when auditor end your task and change survey_surveyor_assignments column `status` to completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_auditor_assignments');
    }
};
