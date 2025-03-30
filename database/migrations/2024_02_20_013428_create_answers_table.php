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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');     // user id
            $table->integer('date');    //  yyyyMMdd - ex; 20240105, 20240319
            $table->integer('qid');     //  Quiz id
            $table->integer('did');     //  Domain id
            $table->integer('tid');     //  Type id
            $table->integer('dur');     //  Duration in second
            $table->integer('score');  //  Score(0-1, 1: 100% corect, 0: 100% wrong)
            $table->integer('exam_type'); // 0: Daily Question, 1: Exam Builder
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
