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
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); // ID auto-incrémenté
            $table->string('libelle')->unique(); // Nom de l'article
            $table->decimal('prix', 8, 2); // Prix de l'article avec 8 chiffres au total et 2 décimales
            $table->integer('qte'); // Quantité en stock
            $table->softDeletes(); // Pour activer le soft delete
            $table->timestamps(); // Pour les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
