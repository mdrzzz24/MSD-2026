<?php

namespace Database\Seeders;

use App\Models\Booth;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BoothAccountSeeder extends Seeder
{
    /**
     * Create one mobile-app booth account per booth.
     *
     * Email:    {slug}@msd26.app   (e.g. aws-amazon-web-services@msd26.app)
     * Password: qwe123
     * Role:     booth (is_admin = false → blocked from the admin panel)
     *
     * Idempotent & non-destructive: missing accounts are created, existing booth
     * accounts are updated to the required email/password; nothing is ever
     * deleted and no other table/data is touched.
     *
     * Run it standalone:
     *   php artisan db:seed --class=BoothAccountSeeder --force
     */
    public function run(): void
    {
        $password = 'qwe123';
        $booths   = Booth::ordered()->get();

        $created = 0;
        $updated = 0;

        foreach ($booths as $booth) {
            $email = Str::slug($booth->name) . '@msd26.app';

            $data = [
                'name'        => $booth->name,
                'email'       => $email,
                'is_admin'    => false,
                'role'        => 'booth',
                'booth_id'    => $booth->id,
                'permissions' => User::defaultPermissions('booth'),
                'password'    => $password,
            ];

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update($data);
                $updated++;
            } else {
                User::create($data);
                $created++;
            }
        }

        $this->command?->info("Booth accounts seeded: {$created} created, {$updated} updated. Password: {$password}");
    }
}
