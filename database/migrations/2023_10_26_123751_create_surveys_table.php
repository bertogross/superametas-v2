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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->comment('The user who created the survey task');
            $table->unsignedBigInteger('current_user_editor')->nullable()->comment(' 	The user who is currently editing the survey task 	');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('The company to which the survey task is assigned');
            $table->unsignedBigInteger('delegated_to')->nullable()->comment('The user to whom the survey task is delegated');
            $table->unsignedBigInteger('audited_by')->nullable()->comment('The user who audited the task');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'audited'])->default('pending')->comment('The status of the survey task');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium')->comment('The priority of the survey task');
            $table->longText('description')->nullable();
            $table->date('due_date')->nullable()->comment('The due date of the survey task');
            $table->timestamp('completed_at')->nullable()->comment('The timestamp when the survey task was completed');
            $table->timestamp('audited_at')->nullable()->comment('The timestamp when the survey task was audited');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('current_user_editor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('delegated_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('audited_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
