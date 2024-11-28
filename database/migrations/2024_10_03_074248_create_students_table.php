<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->string('stud_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('college_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('major_id')->nullable();
            $table->unsignedBigInteger('year_id');
            $table->string('stud_fname');
            $table->string('stud_lname');
            $table->string('stud_mname')->nullable();
            $table->string('stud_contact')->nullable();
            $table->string('stud_email')->unique()->nullable();
            $table->string('enrollment_stat');
            $table->timestamps();

            // Define a composite primary key
            $table->primary(['stud_id', 'campus_id']);
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
