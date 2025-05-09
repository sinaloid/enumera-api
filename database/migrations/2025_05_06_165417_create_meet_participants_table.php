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
        Schema::create('meet_participants', function (Blueprint $table) {
            $table->id();
            $table->longText('meet_token');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string("slug");
            $table->boolean('is_moderator')->default(false);
            $table->boolean('is_deleted')->default(false);

            $table->unsignedBigInteger('meet_id');

            $table->foreign('meet_id')
                    ->references('id')
                    ->on('meets')
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
        Schema::dropIfExists('meet_participants');
    }
};
