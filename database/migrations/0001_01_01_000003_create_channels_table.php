<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o usuário (dono do canal)
            // onDelete('cascade') significa que se o usuário for deletado, seu canal também será.
            $table->foreignIdFor(User::class)->unique()->constrained()->onDelete('cascade');

            // Propriedades do Canal
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Caminhos para as imagens
            $table->string('avatar_image_path')->nullable();
            $table->string('cover_image_path')->nullable();

            // Status e Configurações
            $table->timestamp('verified_at')->nullable(); // Armazena quando o canal foi verificado
            $table->jsonb('settings')->nullable(); // Para configurações flexíveis (PostgreSQL)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
