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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['Açougue e Peixaria', 'Laticínios e Frios', 'Mercearia', 'Padaria', 'Bebidas', 'Limpeza', 'Higiene e Beleza', 'Pet Shop', 'Utilidades Domésticas'])->nullable();
            $table->integer('quantity');
            $table->boolean('purchased')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
