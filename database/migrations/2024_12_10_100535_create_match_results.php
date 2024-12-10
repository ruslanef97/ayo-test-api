<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id');
            $table->integer('home_score');
            $table->integer('away_score');
            $table->enum('result', [0, 1, 2]); //0 => Draw, 1 => Home Win, 2 => Away Win
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('match_result_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_result_id');
            $table->enum('type', [1, 2]); //1 => Goal, 2 => Yellow Card, 3 => Red Card, 4 => Subs
            $table->enum('half', [0, 1, 2, 3, 4]); //0 => First Half, 1 => Second Half, 2 => First Extra Half, 3 => Second Extra Half, 4 => Penalty
            $table->integer('time')->default(0);
            $table->bigInteger('player_id')->default(0); // If type is subs, this is for 'OUT'
            $table->bigInteger('sec_player_id')->nullable(); // If type is subs, this is for 'IN'
            $table->enum('is_penalty', [0, 1])->default(0); //0 => No, 1 => Yes
            $table->mediumText('information')->nullable();
            $table->timestamps();

            $table->foreign('match_result_id')
                ->references('id')
                ->on('match_results')
                ->onDelete('cascade');

                $table->index(['match_result_id', 'type', 'half', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
        Schema::dropIfExists('match_result_histories');
    }
};
