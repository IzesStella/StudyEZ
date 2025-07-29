<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // vincula ao usuário
            $table->date('date'); // data selecionada
            $table->text('note'); // anotação
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planners');
    }
};