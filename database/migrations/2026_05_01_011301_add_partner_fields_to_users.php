<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_partner')->default(false)->after('is_admin');
            $table->enum('partner_status', ['pending', 'approved', 'rejected'])->nullable()->after('is_partner');
            $table->timestamp('partner_requested_at')->nullable()->after('partner_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_partner', 'partner_status', 'partner_requested_at']);
        });
    }
};
