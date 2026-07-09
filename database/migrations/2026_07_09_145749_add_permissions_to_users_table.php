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
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role');
        });

        // Set default permissions for existing users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $permissions = match ($user->role) {
                'super_admin' => self::allPermissions(),
                'admin' => self::adminPermissions(),
                'client' => self::clientPermissions(),
                default => self::adminPermissions(),
            };
            DB::table('users')->where('id', $user->id)->update([
                'permissions' => json_encode($permissions),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }

    private static function allPermissions(): array
    {
        return [
            'registrants' => true,
            'workshops' => true,
            'workshop_registrants' => true,
            'tracks' => true,
            'agenda' => true,
            'speakers' => true,
            'time_slots' => true,
            'rooms' => true,
            'email_templates' => true,
            'utm_sources' => true,
            'qr_codes' => true,
            'checkin_log' => true,
            'admin_users' => true,
        ];
    }

    private static function adminPermissions(): array
    {
        return [
            'registrants' => true,
            'workshops' => true,
            'workshop_registrants' => true,
            'tracks' => true,
            'agenda' => false,
            'speakers' => false,
            'time_slots' => false,
            'rooms' => false,
            'email_templates' => false,
            'utm_sources' => true,
            'qr_codes' => true,
            'checkin_log' => false,
            'admin_users' => false,
        ];
    }

    private static function clientPermissions(): array
    {
        return [
            'registrants' => false,
            'workshops' => true,
            'workshop_registrants' => false,
            'tracks' => false,
            'agenda' => false,
            'speakers' => false,
            'time_slots' => false,
            'rooms' => false,
            'email_templates' => false,
            'utm_sources' => true,
            'qr_codes' => true,
            'checkin_log' => false,
            'admin_users' => false,
        ];
    }
};
