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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED NOT NULL
            $table->unsignedBigInteger('post_id')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->string('attachment_url', 300);
            $table->string('attachment_type', 100);
            $table->string('attachment_title', 100)->nullable();
            $table->longText('attachment_description');
            $table->string('attachment_destiny', 20)->default('multiple');
            $table->string('attachment_size', 100);
            $table->unsignedTinyInteger('attachment_order')->default(0);
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
