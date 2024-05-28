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
        Schema::create('matiere_de_la_classes', function (Blueprint $table) {
            $table->id();

            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('matiere_id');
            $table->foreign('matiere_id')
                    ->references('id')
                    ->on('matieres')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                    ->references('id')
                    ->on('classes')
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
        Schema::dropIfExists('matiere_de_la_classes');
    }
};
