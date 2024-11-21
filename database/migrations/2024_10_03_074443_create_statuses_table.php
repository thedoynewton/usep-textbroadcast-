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
        Schema::create('statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->primary(); // Use HRIS id as primary key
            $table->string('status_name');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
    
};
