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
        Schema::create('reserved_quizzes', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');     // user id
            $table->integer('qid');     //  Quiz id
            $table->integer('rtype');     //  0: bookmarked
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_quizzes');
    }
};
