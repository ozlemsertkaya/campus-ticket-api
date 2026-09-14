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
        Schema::table('users', function (Blueprint $table) { //var olan users tablosuna ekleme yapıyoruz
            $table->enum('role', ['student', 'agent', 'admin'])->default('student')->after('email');
        });       //sadece bu değerlrdn biri olablr.   //rol belirtilmzse agent olsun der.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
