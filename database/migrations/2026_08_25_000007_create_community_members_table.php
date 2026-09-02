<?php
/**
 * Создание таблицы "Участники сообщества"
 *
 * @author denis.chernonozhkin
 * @Date 25.08.2026 15:54
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamps();
            $table->unique(['user_id', 'community_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_members');
    }
};
