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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->bigInteger('template_id')->default(0);
            $table->enum('status', ['new', 'stoped', 'trash', 'pending', 'in_progress', 'completed', 'audited'])->default('new')->comment('The status of the survey task');
            $table->enum('priority', ['high', 'medium', 'low'])->default('high')->comment('The priority of the survey task');
            $table->date('start_date')->nullable()->comment('The start date of the survey task');
            $table->json('distributed_data')->nullable();
            $table->timestamp('completed_at')->nullable()->comment('The timestamp when the survey task was completed');
            $table->timestamp('audited_at')->nullable()->comment('The timestamp when the survey task was audited');
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
