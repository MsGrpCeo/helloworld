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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('question'); // Question
            $table->integer('did');     // Domain ID --- Domain1 -> 0, Domain2 -> 1, ... , Domain5 -> 4
            $table->integer('tid');    // Type ID --- Multiple Choice -> 0, Multiple Answer -> 1, Categorization -> 2, Order -> 3
            $table->string('answers');  // Answers --- answers are separated by "!@!", and sub answers are separated by "!@@!"
            // Correct Answers --- 
            // For Multiple Choice and Multiple Answer: Start index: 0, separated by ","
            // For Categorization: Start index: 0 in sub answers in each answer
            // For Order: No correct answer is needed. But we need to write the answers in order.
            $table->string('c_answers');
            $table->string('info');     // Question Information or hint
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
