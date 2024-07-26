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
        Schema::create('evaluation_lecons', function (Blueprint $table) {
            $table->id();
            $table->string("question");
            $table->string("choix")->nullable();
            $table->string("type");
            $table->string("reponses")->nullable();
            $table->string("point")->nullable();
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

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
        Schema::dropIfExists('evaluation_lecons');
    }
};
