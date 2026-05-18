<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug', 140)->unique();

            // Primary contact
            $table->string('email')->unique();
            $table->string('phone_number', 32)->nullable()->unique();

            // Additional contacts
            $table->string('alternative_email', 255)->nullable();
            $table->string('alternative_phone_number', 32)->nullable();
            $table->string('whatsapp_number', 32)->nullable();

            // Verification + auth
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Profile
            $table->string('image', 255)->nullable();
            $table->text('address')->nullable();

            // Roles
            $table->string('role', 50)->default('employee');
            $table->string('role_short_form', 10)->default('EMP');

            // Status / tracking
            $table->string('status', 20)->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('created_at_ip')->nullable();

            // Soft delete + metadata
            $table->softDeletes();
            $table->json('metadata')->nullable();

            // Extra indexes
            $table->index('deleted_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
