<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->constrained('roles', 'name');
            $table->timestamps();

            $table->foreign('role')->references('name')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
