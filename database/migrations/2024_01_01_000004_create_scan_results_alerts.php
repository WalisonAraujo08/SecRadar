<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scan_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitored_email_id')->nullable()->constrained()->nullOnDelete();
            // source_key é INTERNO — nunca exposto ao cliente (ex: 'hibp', 'leakcheck')
            $table->string('source_key', 50);
            // breach_name é a versão normalizada mostrada ao cliente
            $table->string('breach_name');
            $table->json('data_exposed');   // ['email','senha','cpf','telefone']
            $table->enum('severity', ['critical', 'high', 'medium', 'low']);
            $table->timestamp('detected_at');
            $table->date('breach_date')->nullable();
            $table->boolean('notified_email')->default(false);
            $table->boolean('notified_whatsapp')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'severity', 'detected_at']);
            // Evita duplicatas por fonte interna + nome do vazamento
            $table->unique(['monitored_email_id', 'source_key', 'breach_name'], 'unique_breach');
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scan_result_id')->constrained()->cascadeOnDelete();
            $table->enum('severity', ['critical', 'high', 'medium', 'low']);
            $table->boolean('read')->default(false);
            $table->boolean('seen_by_agent')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('scan_results');
    }
};
