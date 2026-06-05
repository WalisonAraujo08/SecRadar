<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('commission_rate', 5, 2)->default(30.00);
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('referral_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('pix_key')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('reference_month');
            $table->timestamps();
        });

        // Adiciona código de referral na tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('is_admin');
            $table->string('pix_key', 255)->nullable()->after('referral_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_payments');
        Schema::dropIfExists('referrals');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'pix_key']);
        });
    }
};
