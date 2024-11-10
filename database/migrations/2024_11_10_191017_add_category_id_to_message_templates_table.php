<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToMessageTemplatesTable extends Migration
{
    public function up()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id');

            // Optionally add a foreign key constraint if needed
            $table->foreign('category_id')->references('id')->on('message_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
