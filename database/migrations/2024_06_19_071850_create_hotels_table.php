<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */ 

    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('address' , 100);
            $table->tinyInteger('stars');
            $table->integer('phone');
            $table->string('email', 256);
            $table->string('website', 150);
            $table->string('image', 400);
            $table->timestamp('created_at')->nullable(false)->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('hotels');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
