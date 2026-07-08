<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        // ── Disable FK checks for truncation ──
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \App\Models\AgendaItem::truncate();
        TimeSlot::truncate();
        Room::truncate();
        Floor::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── Time Slots ──
        $slots = [
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
            ['start_time' => '16:10', 'end_time' => '17:00', 'order' => 11],
        ];
        foreach ($slots as $d) {
            TimeSlot::create($d);
        }

        // ── Floors ──
        $floorIds = [];
        $floors = [
            ['name' => 'Second Floor', 'order' => 1],
            ['name' => 'First Floor',  'order' => 2],
            ['name' => 'Third Floor',  'order' => 3],
        ];
        foreach ($floors as $d) {
            $f = Floor::create($d);
            $floorIds[$f->name] = $f->id;
        }

        // ── Rooms ──
        $rooms = [
            ['name' => 'Ballroom A', 'floor_id' => $floorIds['Second Floor'], 'order' => 1],
            ['name' => 'Ballroom B', 'floor_id' => $floorIds['Second Floor'], 'order' => 2],
            ['name' => 'Ballroom C', 'floor_id' => $floorIds['Second Floor'], 'order' => 3],
            ['name' => 'Sumatra',    'floor_id' => $floorIds['First Floor'],  'order' => 4],
            ['name' => 'Java',       'floor_id' => $floorIds['First Floor'],  'order' => 5],
            ['name' => 'Sulawesi',   'floor_id' => $floorIds['First Floor'],  'order' => 6],
            ['name' => 'Kalimantan', 'floor_id' => $floorIds['First Floor'],  'order' => 7],
            ['name' => 'Maluku',     'floor_id' => $floorIds['First Floor'],  'order' => 8],
            ['name' => 'Denpasar',   'floor_id' => $floorIds['Third Floor'],  'order' => 9],
        ];
        foreach ($rooms as $d) {
            Room::create($d);
        }

        // Note: No agenda items seeded — table cells will show "—" automatically.

        $this->command->info('Agenda structure (floors, rooms, time slots) seeded successfully!');
    }
}
