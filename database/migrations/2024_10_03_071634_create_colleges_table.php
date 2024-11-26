<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->unsignedBigInteger('college_id'); // Unique within a campus
            $table->unsignedBigInteger('campus_id'); // Reference to campus
            $table->string('college_name');
            $table->timestamps();
        
            // Composite primary key
            $table->primary(['campus_id', 'college_id']);
        
            // Unique constraint to prevent duplicate college names within the same campus
            $table->unique(['campus_id', 'college_id']);
        
            // Foreign key for campus_id
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade');
        });
        
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
