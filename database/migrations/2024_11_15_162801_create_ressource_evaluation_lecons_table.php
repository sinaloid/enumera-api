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
        Schema::create('ressource_evaluation_lecons', function (Blueprint $table) {
            $table->id();
            $table->string("original_name")->nullable();
            $table->string("name")->nullable();
            $table->string("type");
            $table->string("url");
            $table->string("slug");
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('evaluation_lecon_id');
            $table->foreign('evaluation_lecon_id')
                    ->references('id')
                    ->on('evaluation_lecons')
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
        Schema::dropIfExists('ressource_evaluation_lecons');
    }
};
