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
        Schema::create('user_classe_matieres', function (Blueprint $table) {
            $table->id();
            $table->string('matiere_label');
            $table->string('matiere_id');
            $table->string('matiere_slug');
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('user_classe_id');
            $table->foreign('user_classe_id')
                    ->references('id')
                    ->on('user_classes')
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
        Schema::dropIfExists('user_classe_matieres');
    }
};
