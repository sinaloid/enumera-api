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
        Schema::create('evaluation_matiere_de_la_classes', function (Blueprint $table) {
            $table->id();
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('evaluation_devoir_id');
            $table->foreign('evaluation_devoir_id')
                    ->references('id')
                    ->on('evaluation_devoirs')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

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
        Schema::dropIfExists('evaluation_matiere_de_la_classes');
    }
};
