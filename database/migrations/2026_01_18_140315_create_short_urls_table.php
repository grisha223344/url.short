<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->text('original_url');
            $table->string('short_code')->unique();
            $table->string('custom_code')->nullable()->unique();
            $table->boolean('is_commercial')->default(false);

            // Поля для платных показов
            $table->decimal('cost_per_view', 10, 2)->nullable()->default(0);
            $table->decimal('budget', 10, 2)->nullable()->default(0);
            $table->decimal('budget_spent', 10, 2)->default(0);
            $table->integer('unique_paid_views')->default(0);
            $table->integer('max_daily_views')->nullable();
            $table->date('campaign_start_date')->nullable();
            $table->date('campaign_end_date')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('total_clicks')->default(0);
            $table->timestamps();

            // Индексы для оптимизации
            $table->index('short_code');
            $table->index('expires_at');
            $table->index('user_id');
            $table->index(['is_commercial', 'campaign_end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
