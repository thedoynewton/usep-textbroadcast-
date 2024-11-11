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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campus_id')->nullable(); // Add campus_id field
            $table->string('recipient_type');
            $table->string('template_name');
            $table->text('content');
            $table->string('message_type');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('cancelled_at')->nullable(); // Add cancelled_at field
            $table->string('status')->default('Pending'); // Default status as Pending
            $table->integer('total_recipients')->default(0); // Add total_recipients field
            $table->integer('sent_count')->default(0); // Add sent_count field
            $table->integer('failed_count')->default(0); // Add failed_count field
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade'); // Add campus_id foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
