<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Create (or sync) one mobile-app account per room.
 *
 * Usage:
 *   php artisan app:create-room-accounts
 *   php artisan app:create-room-accounts --password=MySecret1
 *
 * Each account is named after its room, uses {slug}@msd26.app as email, role
 * "room" (is_admin = false → blocked from the admin panel), and has NO session
 * assignments → it manages ALL sessions until the super admin assigns sessions.
 * Idempotent: existing accounts are updated, never duplicated.
 */
class CreateRoomAccounts extends Command
{
    protected $signature = 'app:create-room-accounts {--password=room12345 : Default password for every room account}';

    protected $description = 'Create one mobile-app room account per room (idempotent).';

    public function handle(): int
    {
        $password = $this->option('password');
        $rooms    = Room::ordered()->get();

        if ($rooms->isEmpty()) {
            $this->error('No rooms found in the rooms table.');
            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;

        foreach ($rooms as $room) {
            $email = Str::slug($room->name) . '@msd26.app';

            $user = User::where('email', $email)->first();

            $data = [
                'name'        => $room->name,
                'email'       => $email,
                'is_admin'    => false,
                'role'        => 'room',
                'room_id'     => $room->id,
                'permissions' => User::defaultPermissions('room'),
            ];

            if ($user) {
                $user->update($data);
                $updated++;
                $this->line("  ↻ {$room->name} — {$email} (updated)");
            } else {
                $data['password'] = $password;
                User::create($data);
                $created++;
                $this->line("  ✓ {$room->name} — {$email} (created)");
            }
        }

        $this->info("Done. {$created} created, {$updated} updated. Default password: {$password}");

        return self::SUCCESS;
    }
}
