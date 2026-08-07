<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Convenience seeder that runs BOTH the General Session and Track Session seeders.
 *
 * Both are idempotent & non-destructive (they only add missing rows, never delete).
 * Run:  php artisan db:seed --class=SessionDataSeeder
 */
class SessionDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(GeneralSessionSeeder::class);
        $this->call(TrackSessionSeeder::class);
    }
}
