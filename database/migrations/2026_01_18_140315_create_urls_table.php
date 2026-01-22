<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->text('original_url');
            $table->string('short_code')->unique();
            $table->boolean('is_commercial')->default(false);
            $table->decimal('cost_per_view', 12, 2)->nullable()->default(0);
            $table->decimal('budget', 12, 2)->nullable()->default(0);
            $table->decimal('budget_spent', 12, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
