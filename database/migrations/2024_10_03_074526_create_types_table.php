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
        Schema::create('types', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->primary(); // HRIS id as the primary key
            $table->string('type_name'); // Corresponds to the "name" field in HRIS
            $table->timestamps(); // Optional: Keeps track of creation and update times
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
