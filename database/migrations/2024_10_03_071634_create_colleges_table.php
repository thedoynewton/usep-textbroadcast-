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
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('campus_id');
            $table->string('college_name');
            $table->timestamps();
        
            // Define a composite primary key
            $table->primary(['college_id', 'campus_id']);
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
