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
        Schema::create('majors', function (Blueprint $table) {
            $table->unsignedBigInteger('major_id'); // Unique within a program
            $table->unsignedBigInteger('campus_id'); // Reference to campus
            $table->unsignedBigInteger('college_id'); // Reference to college
            $table->unsignedBigInteger('program_id'); // Reference to program
            $table->string('major_name');
            $table->timestamps();
        
            // Composite primary key
            $table->primary(['campus_id', 'college_id', 'program_id', 'major_id']);
        
            // Unique constraint to prevent duplicate major names within the same program
            $table->unique(['campus_id', 'college_id', 'program_id', 'major_id']);
        
            // Foreign key constraints
            $table->foreign(['campus_id', 'college_id', 'program_id'])
                ->references(['campus_id', 'college_id', 'program_id'])
                ->on('programs')
                ->onDelete('cascade'); // Delete majors if the program is deleted
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
