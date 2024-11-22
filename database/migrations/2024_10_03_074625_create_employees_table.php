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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('emp_id')->primary(); // Change to string for alphanumeric IDs
            $table->string('emp_fname');
            $table->string('emp_lname');
            $table->string('emp_mname')->nullable();
            $table->string('emp_contact')->nullable();
            $table->string('emp_email')->unique()->nullable();
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('type_id');
            $table->timestamps();
    
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('NO ACTION');
            $table->foreign('office_id')->references('office_id')->on('offices')->onDelete('NO ACTION');
            $table->foreign('status_id')->references('status_id')->on('statuses')->onDelete('NO ACTION');
            $table->foreign('type_id')->references('type_id')->on('types')->onDelete('NO ACTION');
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
