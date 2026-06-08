<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'encadreur', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'finance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'logistique', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'discipline', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()]
        ]);

    
        Schema::table('users', function (Blueprint $table) {
           $table->foreignId('role_id')
            ->nullable() 
            ->default(1) 
            ->constrained('roles')
            ->nullOnDelete(); // équivalent à onDelete('set null')
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
