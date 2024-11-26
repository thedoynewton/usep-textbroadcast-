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
        Schema::create('programs', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id'); // Unique within a college
            $table->unsignedBigInteger('campus_id'); // Reference to campus
            $table->unsignedBigInteger('college_id'); // Reference to college
            $table->string('program_name');
            $table->timestamps();
        
            // Composite primary key
            $table->primary(['campus_id', 'college_id', 'program_id']);
        
            // Unique constraint to prevent duplicate program names within the same college and campus
            $table->unique(['campus_id', 'college_id', 'program_id']);
        
            // Foreign key constraints
            $table->foreign(['campus_id', 'college_id'])
                ->references(['campus_id', 'college_id'])
                ->on('colleges')
                ->onDelete('cascade');
        });
                
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
