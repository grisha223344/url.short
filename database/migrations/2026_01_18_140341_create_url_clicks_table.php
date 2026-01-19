<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('url_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_url_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->timestamp('clicked_at')->useCurrent();

            // Индексы для оптимизации запросов
            $table->index('short_url_id');
            $table->index('clicked_at');
            $table->index(['short_url_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_clicks');
    }
};
