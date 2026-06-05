<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf', 20)->nullable()->after('email');
            $table->string('phone', 30)->nullable()->after('cpf');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->boolean('is_admin')->default(false)->after('is_active');
            $table->string('agent_token', 64)->nullable()->unique()->after('is_admin');
            $table->timestamp('agent_last_seen_at')->nullable()->after('agent_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cpf','phone','is_active','is_admin','agent_token','agent_last_seen_at']);
        });
    }
};
