<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up(): void
    {
        Schema::create('roles', static function (Blueprint $table) {
            $table->string('name')->primary();
        });

        DB::table('roles')->insert([
            ['name' => 'user'],
            ['name' => 'administrator'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
