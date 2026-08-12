<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            // 用户外键（若用户表名为 users）
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('name', 100);

            // 账本类型，使用 enum 或 string，此处用 string 更灵活
            $table->string('type', 30)->default('personal');

            // 货币（ISO 4217 三位码）
            $table->char('currency', 3)->default('CNY');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->string('icon', 50)->nullable();

            $table->timestamps();
            $table->softDeletes(); // 软删除支持
        });

        // 额外索引：提升按用户和状态查询的效率
        Schema::table('ledgers', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
