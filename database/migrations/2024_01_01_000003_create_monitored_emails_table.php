<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monitored_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->boolean('is_primary')->default(false);
            $table->string('mp_item_id')->nullable(); // referência do item adicional no MP
            $table->timestamp('last_scanned_at')->nullable();
            $table->enum('status', ['active', 'paused', 'removed'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('monitored_emails'); }
};
