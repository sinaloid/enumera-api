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
        Schema::create('suivi_eleves', function (Blueprint $table) {
            $table->id();
            $table->string('type_activite'); // Type d'activité (ex: leçon, exercice, quiz)  ['lecture', 'video', 'audio', 'exercice', 'quiz']
            $table->integer('temps_passe')->default(0); // Temps en secondes
            $table->float('score')->nullable(); // Score si applicable
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->unsignedBigInteger('lecon_id');
            $table->foreign('lecon_id')
                    ->references('id')
                    ->on('lecons')
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
        Schema::dropIfExists('suivi_eleves');
    }
};
