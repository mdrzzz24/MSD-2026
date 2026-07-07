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

        // ── Sample Agenda Items ──
        $agenda = [
            ['title' => 'Registration, Morning Refreshment, Networking & Exhibition', 'category' => 'general',  'room' => null,     'start_time' => '08:00', 'end_time' => '08:30'],
            ['title' => 'General Sessions',                                            'category' => 'general',  'room' => null,     'start_time' => '08:30', 'end_time' => '10:30'],
            ['title' => 'Workshop A1',                                                  'category' => 'workshop','room' => 'Sumatra','start_time' => '10:30', 'end_time' => '12:00'],
            ['title' => 'Workshop B1',                                                  'category' => 'workshop','room' => 'Java',   'start_time' => '10:30', 'end_time' => '12:00'],
            ['title' => 'Workshop C1',                                                  'category' => 'workshop','room' => 'Sulawesi','start_time' => '10:30', 'end_time' => '12:00'],
            ['title' => 'Lunch, Networking & Exhibition',                              'category' => 'break',    'room' => null,     'start_time' => '12:00', 'end_time' => '13:00'],
            ['title' => 'Platinum 1',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '13:00', 'end_time' => '13:30'],
            ['title' => 'Platinum 4',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '13:00', 'end_time' => '13:30'],
            ['title' => 'Platinum 7',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '13:00', 'end_time' => '13:30'],
            ['title' => 'Gold A1',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '13:00', 'end_time' => '13:30'],
            ['title' => 'Gold B1',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '13:00', 'end_time' => '13:30'],
            ['title' => 'Platinum 2',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Platinum 5',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Platinum 7',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Workshop A2',                                                  'category' => 'workshop','room' => 'Sumatra','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Workshop B2',                                                  'category' => 'workshop','room' => 'Java',   'start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Workshop C2',                                                  'category' => 'workshop','room' => 'Sulawesi','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Gold A2',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Gold B2',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '13:35', 'end_time' => '14:05'],
            ['title' => 'Platinum 3',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '14:10', 'end_time' => '14:40'],
            ['title' => 'Platinum 6',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '14:10', 'end_time' => '14:40'],
            ['title' => 'Platinum 9',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '14:10', 'end_time' => '14:40'],
            ['title' => 'Gold A3',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '14:10', 'end_time' => '14:40'],
            ['title' => 'Gold B3',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '14:10', 'end_time' => '14:40'],
            ['title' => 'Break Session, Exhibition Booths',                            'category' => 'break',    'room' => null,     'start_time' => '14:40', 'end_time' => '15:00'],
            ['title' => 'Platinum 4',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '15:00', 'end_time' => '15:30'],
            ['title' => 'Platinum 1',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '15:00', 'end_time' => '15:30'],
            ['title' => 'Platinum 8',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '15:00', 'end_time' => '15:30'],
            ['title' => 'Gold C1',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '15:00', 'end_time' => '15:30'],
            ['title' => 'Gold D1',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '15:00', 'end_time' => '15:30'],
            ['title' => 'Platinum 5',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Platinum 2',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Platinum 9',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Workshop A3',                                                  'category' => 'workshop','room' => 'Sumatra','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Workshop B3',                                                  'category' => 'workshop','room' => 'Java',   'start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Workshop C3',                                                  'category' => 'workshop','room' => 'Sulawesi','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Gold C2',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Gold D2',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '15:35', 'end_time' => '16:05'],
            ['title' => 'Platinum 6',                                                   'category' => 'platinum','room' => 'Ballroom A','start_time' => '16:05', 'end_time' => '16:35'],
            ['title' => 'Platinum 3',                                                   'category' => 'platinum','room' => 'Ballroom B','start_time' => '16:05', 'end_time' => '16:35'],
            ['title' => 'Platinum 8',                                                   'category' => 'platinum','room' => 'Ballroom C','start_time' => '16:05', 'end_time' => '16:35'],
            ['title' => 'Gold C3',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '16:05', 'end_time' => '16:35'],
            ['title' => 'Gold D3',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '16:05', 'end_time' => '16:35'],
            ['title' => 'Gold C4',                                                      'category' => 'gold',     'room' => 'Kalimantan','start_time' => '16:30', 'end_time' => '17:00'],
            ['title' => 'Gold D4',                                                      'category' => 'gold',     'room' => 'Maluku','start_time' => '16:30', 'end_time' => '17:00'],
        ];

        foreach ($agenda as $data) {
            \App\Models\AgendaItem::create($data);
        }

        // ── Time Slots ──
        $timeSlotData = [
            ['start_time' => '08:00', 'end_time' => '08:30', 'order' => 1],
            ['start_time' => '08:30', 'end_time' => '10:30', 'order' => 2],
            ['start_time' => '10:30', 'end_time' => '12:00', 'order' => 3],
            ['start_time' => '12:00', 'end_time' => '13:00', 'order' => 4],
            ['start_time' => '13:00', 'end_time' => '13:30', 'order' => 5],
            ['start_time' => '13:35', 'end_time' => '14:05', 'order' => 6],
            ['start_time' => '14:10', 'end_time' => '14:40', 'order' => 7],
            ['start_time' => '14:40', 'end_time' => '15:00', 'order' => 8],
            ['start_time' => '15:00', 'end_time' => '15:30', 'order' => 9],
            ['start_time' => '15:35', 'end_time' => '16:05', 'order' => 10],
            ['start_time' => '16:05', 'end_time' => '16:35', 'order' => 11],
            ['start_time' => '16:30', 'end_time' => '17:00', 'order' => 12],
        ];
        foreach ($timeSlotData as $d) {
            \App\Models\TimeSlot::create($d);
        }

        // ── Floors ──
        $floorIds = [];
        $floorData = [
            ['name' => 'Second Floor', 'order' => 1],
            ['name' => 'First Floor', 'order' => 2],
        ];
        foreach ($floorData as $d) {
            $floor = \App\Models\Floor::create($d);
            $floorIds[$floor->name] = $floor->id;
        }

        // ── Rooms ──
        $roomData = [
            ['name' => 'Ballroom A', 'floor_id' => $floorIds['Second Floor'], 'order' => 1],
            ['name' => 'Ballroom B', 'floor_id' => $floorIds['Second Floor'], 'order' => 2],
            ['name' => 'Ballroom C', 'floor_id' => $floorIds['Second Floor'], 'order' => 3],
            ['name' => 'Sumatra',    'floor_id' => $floorIds['First Floor'],  'order' => 4],
            ['name' => 'Java',       'floor_id' => $floorIds['First Floor'],  'order' => 5],
            ['name' => 'Sulawesi',   'floor_id' => $floorIds['First Floor'],  'order' => 6],
            ['name' => 'Kalimantan', 'floor_id' => $floorIds['First Floor'],  'order' => 7],
            ['name' => 'Maluku',     'floor_id' => $floorIds['First Floor'],  'order' => 8],
        ];
        foreach ($roomData as $d) {
            \App\Models\Room::create($d);
        }

        foreach ($registrants as $data) {
            \App\Models\Registrant::create($data);
        }
    }
}
