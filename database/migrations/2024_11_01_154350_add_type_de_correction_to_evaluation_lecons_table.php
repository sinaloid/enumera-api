<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluation_lecons', function (Blueprint $table) {
            $table->string('type_de_correction')->nullable(); // Vous pouvez adapter le type selon vos besoins
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluation_lecons', function (Blueprint $table) {
            $table->dropColumn('type_de_correction');
        });
    }
};
