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
          // Table clients
          Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('surnom')->unique();
            $table->string('telephone')->unique();
            $table->string('adresse')->nullable();
            $table->string('qrcode')->nullable();
            $table->foreignId('user_id')->unique()->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
