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
        Schema::create('majors', function (Blueprint $table) {
            $table->unsignedBigInteger('major_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('program_id');
            $table->string('major_name');
            $table->timestamps();

            // Define a composite primary key
            $table->primary(['major_id', 'campus_id']);
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
