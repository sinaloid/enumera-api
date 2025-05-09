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
        Schema::create('meets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('date');
            $table->string('heure');
            $table->text('description')->nullable();
            //$table->timestamp('scheduled_at')->nullable(); // Date prévue
            $table->integer('duration')->default(60); // Durée en minutes
            $table->foreignId('moderator_id')->nullable();//->constrained('users'); // Modérateur au lieu de teacher
            $table->string('jitsi_room_name'); // Nom unique pour Jitsi
            $table->string('jitsi_meeting_link'); // Lien de la réunion
            $table->enum('status', ['planned', 'ongoing', 'completed'])->default('planned');
            #$table->text('extra_info')->nullable(); // Informations diverses
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
        Schema::dropIfExists('meets');
    }
};
