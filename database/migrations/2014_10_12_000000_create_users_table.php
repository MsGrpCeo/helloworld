<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full Name
            $table->string('email')->unique();  // Email
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Password
            $table->enum('ss_tier', ["basic", "monthly", "yearly"]);    // Subscription Tier
            $table->double('exam_date');    // Exam Date
            $table->integer('daily_streak');    // Daily Question Streak
            $table->string('overal_percent');   // Overall Percent
            $table->string('d1_percent');   // Domain1 Percent
            $table->string('d2_percent');   // Domain2 Percent
            $table->string('d3_percent');   // Domain3 Percent
            $table->string('d4_percent');   // Domain4 Percent
            $table->string('d5_percent');   // Domain5 Percent
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
