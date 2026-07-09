<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@admin.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'role'     => 'super_admin',
        ]);

        // Create sample registrants
        $registrants = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '081234567890', 'organization' => 'PT Maju Jaya'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'phone' => '082345678901', 'organization' => 'CV Abadi'],
            ['name' => 'Ahmad Rizki', 'email' => 'ahmad@example.com', 'phone' => '083456789012', 'organization' => 'UD Sejahtera'],
        ];

        foreach ($registrants as $data) {
            \App\Models\Registrant::create($data);
        }

        // Create one approved registrant with known password for testing
        \App\Models\Registrant::create([
            'first_name'    => 'Test',
            'last_name'     => 'Registrant',
            'name'          => 'Test Registrant',
            'email'         => 'test@registrant.com',
            'phone'         => '081111111111',
            'organization'  => 'Test Corp',
            'status'        => 'approved',
            'password'      => 'password123',
            'plain_password'=> 'password123',
            'processed_at'  => now(),
        ]);

        // ── Seed Agenda (Floors, Rooms, TimeSlots, AgendaItems) ──
        $this->call(AgendaSeeder::class);

        // ── Seed Tracks & Workshops and link them to agenda items ──
        $this->call(TrackWorkshopSeeder::class);

        foreach ($registrants as $data) {
            \App\Models\Registrant::create($data);
        }
    }
}
