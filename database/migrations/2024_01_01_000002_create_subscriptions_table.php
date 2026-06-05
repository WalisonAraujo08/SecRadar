<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mp_subscription_id')->unique();
            $table->string('mp_payer_id')->nullable();
            $table->enum('status', ['pending', 'authorized', 'paused', 'cancelled'])->default('pending');
            $table->decimal('plan_amount', 8, 2)->default(12.99);
            $table->integer('extra_emails')->default(0);
            $table->date('next_billing_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('subscriptions'); }
};
