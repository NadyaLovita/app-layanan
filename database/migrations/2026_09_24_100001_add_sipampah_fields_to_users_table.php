<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('password');
            $table->string('role')->default('officer')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->softDeletes()->after('remember_token');
        });

        // Populate username & role for any existing user
        $existingUsers = DB::table('users')->whereNull('username')->get();
        foreach ($existingUsers as $user) {
            $username = explode('@', (string) $user->email)[0] ?: ('user_'.$user->id);
            DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
                'role' => 'admin',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['username', 'phone', 'role', 'is_active']);
        });
    }
};
