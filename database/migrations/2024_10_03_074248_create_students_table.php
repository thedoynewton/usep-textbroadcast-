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
            $table->string('stud_fname');
            $table->string('stud_lname');
            $table->string('stud_mname')->nullable();
            $table->string('stud_contact');
            $table->string('stud_email')->unique()->nullable();
            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('major_id')->nullable();
            $table->unsignedBigInteger('year_id')->nullable();
            $table->string('enrollment_stat');
            $table->timestamps();
    
            // Define composite primary key
            $table->primary(['stud_id', 'campus_id']);
    
            // Foreign keys
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade');
            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('set null');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
            $table->foreign('major_id')->references('major_id')->on('majors')->onDelete('set null');
            $table->foreign('year_id')->references('year_id')->on('years')->onDelete('set null');
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
