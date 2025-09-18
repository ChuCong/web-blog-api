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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->string('password', 255);
            $table->string("remember_token", 512)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('phone_number', 10)->nullable();
            $table->tinyInteger('sex')->comment('0: Male, 1: Female');
            $table->string('birthday', 255)->nullable();
            $table->tinyInteger('type')->comment('0: Học viên, 1: Giáo viên, 2: Quản trị');
            $table->string('country', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
