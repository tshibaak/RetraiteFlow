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
        Schema::create('deposites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sexe');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age')->unsigned();
            $table->string('group');
            $table->string('commission');
            $table->string('phone');
            $table->float('amount')->unsigned();
            $table->date('delai');
          
            // pending , confirmed , unconfirmed
            $table->string('status')->default('pending');
            $table->foreignId('validator_id')->nullable()->constrained('users')->onDelete('set null');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposites');
    }
};
