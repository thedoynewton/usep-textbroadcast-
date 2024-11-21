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
        Schema::create('offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id'); // Remove auto-increment
            $table->string('office_name');
            $table->timestamps();
    
            $table->primary('office_id'); // Set office_id as the primary key
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
