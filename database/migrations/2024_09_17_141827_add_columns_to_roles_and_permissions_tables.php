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
         // Ajouter une colonne 'description' et 'slug' nullable à la table 'roles'
         Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
        });

        // Ajouter une colonne 'description' et 'slug' nullable à la table 'permissions'
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer les colonnes ajoutées lors de la migration
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['display_name','description', 'slug']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name','description', 'slug']);
        });
    }
};
