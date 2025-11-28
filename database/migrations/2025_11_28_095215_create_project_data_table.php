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
        Schema::create('app_project_data', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->varchar('raw_id');
            $table->text('raw_text');
            $table->enum('status', ['pending', 'processing', 'done', 'error'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_project_data');
    }
};
