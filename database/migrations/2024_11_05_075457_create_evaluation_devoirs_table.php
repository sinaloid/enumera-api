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
        Schema::create('evaluation_devoirs', function (Blueprint $table) {
            $table->id();
            $table->string("label");
            $table->string("abreviation")->nullable();
            $table->string('type_de_correction')->nullable();
            $table->string("date")->nullable();
            $table->string("heure_debut")->nullable();
            $table->string("heure_fin")->nullable();
            $table->string("etat")->default("EN_ATTENTE");
            $table->string("description")->nullable();
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
        Schema::dropIfExists('evaluation_devoirs');
    }
};
