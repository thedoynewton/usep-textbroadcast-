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
            $table->unsignedBigInteger('college_id')->primary(); // Primary key without auto-increment
            $table->unsignedBigInteger('campus_id')->nullable(); // Make it nullable
            $table->string('college_name');
            $table->timestamps();
    
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('set null');
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
