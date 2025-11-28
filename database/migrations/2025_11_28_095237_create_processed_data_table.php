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
        Schema::create('app_processed_data', function (Blueprint $table) {
            $table->id();
            $table->integer('project_data_id');
            $table->text('cleaned_text');
            $table->enum('sentiment', ['positive', 'negative', 'neutral'])->default('neutral');
            $table->float('confidence_score')->default(0);
            $table->integer('preprocessing_time_ms')->default(0);
            $table->varchar('model_version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_processed_data');
    }
};
