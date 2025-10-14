<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add profile fields
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('username')->after('last_name')->unique()->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->text('bio')->after('phone')->nullable();
            $table->string('avatar')->after('bio')->nullable();
            $table->string('role')->after('avatar')->default('user');
            $table->timestamp('last_login_at')->after('remember_token')->nullable();
            
            // Keep the name field but make it nullable since we'll use first_name + last_name
            $table->string('name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'username',
                'phone',
                'bio',
                'avatar',
                'role',
                'last_login_at'
            ]);
            $table->string('name')->nullable(false)->change();
        });
    }
};