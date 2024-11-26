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
        Schema::create('students', function (Blueprint $table) {
            $table->string('stud_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('major_id');
            $table->unsignedBigInteger('year_id');
            $table->string('stud_fname');
            $table->string('stud_lname');
            $table->string('stud_mname')->nullable();
            $table->string('stud_contact')->nullable();
            $table->string('stud_email')->unique()->nullable();
            $table->string('enrollment_stat');
            $table->timestamps();
        
            // Composite primary key that includes campus_id, allowing different campuses for the same student
            $table->primary(['campus_id', 'stud_id']);
        
            // Unique constraint to prevent duplicate student IDs across campuses
            $table->unique(['campus_id', 'stud_id']);
        
            // Foreign key constraints
            $table->foreign('campus_id')
                ->references('campus_id')
                ->on('campuses')
                ->onDelete('no action');
        
            $table->foreign(['campus_id', 'college_id'])
                ->references(['campus_id', 'college_id'])
                ->on('colleges')
                ->onDelete('no action');
        
            $table->foreign(['campus_id', 'college_id', 'program_id'])
                ->references(['campus_id', 'college_id', 'program_id'])
                ->on('programs')
                ->onDelete('no action');
        
            $table->foreign(['campus_id', 'college_id', 'program_id', 'major_id'])
                ->references(['campus_id', 'college_id', 'program_id', 'major_id'])
                ->on('majors')
                ->onDelete('no action');
        
            $table->foreign('year_id')
                ->references('year_id')
                ->on('years')
                ->onDelete('no action');
        });
        
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
