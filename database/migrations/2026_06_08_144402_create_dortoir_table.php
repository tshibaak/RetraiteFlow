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
        Schema::create('dortoir', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->unsigned();
            $table->foreignId('user_id')->constrained()->onDelete('set null');
            $table->string('sexe');
            $table->timestamps();
        });

        Schema::table('deposite',function(Blueprint $table){
            $table->foreignId('dortoir_id')
               ->nullable()
               ->constrained('dortoir')
               ->default(null)
               ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dortoir');
    }
};
