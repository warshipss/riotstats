<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('game_id')->index();
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->cascadeOnDelete();

            $table->binary('uid')->index();
            $table->jsonb('stats')->nullable();
            $table->jsonb('original')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamp('started_at')->nullable();

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
        Schema::dropIfExists('matches');
    }
}
