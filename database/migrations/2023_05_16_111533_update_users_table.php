<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Change the email column to nullable
            $table->string('email')->nullable()->change();

            // Add a unique constraint to the username column
            $table->string('username')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert the changes made in the up() method
            $table->string('email')->nullable(false)->change();
            $table->dropUnique('users_username_unique');
        });
    }
};
