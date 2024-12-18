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
        Schema::create('message_defilants', function (Blueprint $table) {
            $table->id();
            $table->string("titre");
            $table->longText("contenu");
            $table->string("type");
            $table->date("date_debut");
            $table->date("date_fin");
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_defilants');
    }
};
