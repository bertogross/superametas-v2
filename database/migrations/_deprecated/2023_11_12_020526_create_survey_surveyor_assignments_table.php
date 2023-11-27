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
        Schema::create('survey_surveyor_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->comment('The surveyor user_id');
            $table->bigInteger('survey_id')->nullable();
            $table->bigInteger('company_id')->default(0);
            $table->enum('status', ['new', 'pending', 'in_progress', 'auditing', 'completed'])->default('new')->comment('The status of the survey task for this surveyor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_surveyor_assignments');
    }
};
