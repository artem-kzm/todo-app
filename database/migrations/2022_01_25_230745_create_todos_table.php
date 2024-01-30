<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodosTable extends Migration
{
    public function up(): void
    {
        Schema::create('todo_lists', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('todo_list_items', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_list_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_done')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_list_items');
        Schema::dropIfExists('todo_lists');
    }
}
