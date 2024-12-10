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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('name');
            $table->decimal('height', 4, 1)->default(0);
            $table->decimal('weight', 4, 1)->default(0);
            $table->enum('position', ['fw', 'mf', 'df', 'gk'])->nullable(); // fw => Forward, mf => Mid Fielder, df => Defender, gk => Goal Keeper
            $table->smallInteger('number');
            $table->enum('status', [0, 1])->default(0); // 0 => inactive, 1 => active
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'team_id', 'position', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
