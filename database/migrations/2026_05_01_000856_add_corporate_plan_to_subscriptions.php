<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('plan_type', ['personal', 'corporate'])->default('personal')->after('status');
            $table->integer('email_limit')->default(6)->after('extra_emails');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'email_limit']);
        });
    }
};