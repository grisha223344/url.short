<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paid_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_url_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('visitor_hash'); // Хэш IP + User-Agent для уникальности
            $table->decimal('cost', 10, 2);
            $table->timestamp('viewed_at')->useCurrent();

            // Индексы для оптимизации
            $table->index('short_url_id');
            $table->index('visitor_hash');
            $table->index(['short_url_id', 'visitor_hash']);
            $table->index('viewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paid_views');
    }
};
