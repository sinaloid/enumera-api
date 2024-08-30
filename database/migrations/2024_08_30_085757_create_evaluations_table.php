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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->string("label");
            $table->string("abreviation")->nullable();
            $table->string("date")->nullable();
            $table->string("heure_debut")->nullable();
            $table->string("heure_fin")->nullable();
            $table->string("etat")->default("EN_ATTENTE");
            $table->string("description")->nullable();
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('matiere_de_la_classe_id');
            $table->foreign('matiere_de_la_classe_id')
                    ->references('id')
                    ->on('matiere_de_la_classes')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
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
        Schema::dropIfExists('evaluations');
    }
};
