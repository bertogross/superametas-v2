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
        Schema::create('survey_metas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id')->comment('The ID of the survey task');
            $table->string('meta_key')->comment('The key of the metadata');
            $table->text('meta_value')->comment('The value of the metadata');
            $table->timestamps();
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_metas');
    }
};
