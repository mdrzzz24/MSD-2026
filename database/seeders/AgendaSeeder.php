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

        // ── Agenda Items ──
        $items = [
            ['id' => 1,  'start_time' => '08:00', 'end_time' => '08:30', 'title' => 'Registration, Morning Refreshment, Networking, and Exhibition Booths', 'category' => 'break',    'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 9, 'order' => 1],
            ['id' => 2,  'start_time' => '08:30', 'end_time' => '10:30', 'title' => 'General Sessions',                                                       'category' => 'general',  'room' => 'Ballroom A', 'rowspan' => 2, 'colspan' => 3, 'order' => 2],
            ['id' => 3,  'start_time' => '10:30', 'end_time' => '12:00', 'title' => 'Workshop A1',                                                            'category' => 'workshop', 'room' => 'Sumatra',    'rowspan' => 1, 'colspan' => 1, 'order' => 3],
            ['id' => 4,  'start_time' => '10:30', 'end_time' => '12:00', 'title' => 'Workshop B1',                                                            'category' => 'workshop', 'room' => 'Java',       'rowspan' => 1, 'colspan' => 1, 'order' => 4],
            ['id' => 5,  'start_time' => '10:30', 'end_time' => '12:00', 'title' => 'Workshop C1',                                                            'category' => 'workshop', 'room' => 'Sulawesi',   'rowspan' => 1, 'colspan' => 1, 'order' => 5],
            ['id' => 6,  'start_time' => '12:00', 'end_time' => '13:00', 'title' => 'Lunch, Networking, and Exhibition Booths',                                'category' => 'break',    'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 9, 'order' => 6],
            ['id' => 7,  'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Track A1',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 10],
            ['id' => 8,  'start_time' => '13:35', 'end_time' => '14:05', 'title' => 'Track A2',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 15],
            ['id' => 9,  'start_time' => '14:10', 'end_time' => '14:40', 'title' => 'Track A3',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 19],
            ['id' => 10, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Track B1',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 11],
            ['id' => 11, 'start_time' => '13:35', 'end_time' => '14:05', 'title' => 'Track B2',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 16],
            ['id' => 12, 'start_time' => '14:10', 'end_time' => '14:40', 'title' => 'Track B3',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 20],
            ['id' => 13, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Track C1',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 12],
            ['id' => 14, 'start_time' => '13:35', 'end_time' => '14:05', 'title' => 'Track C2',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 17],
            ['id' => 15, 'start_time' => '14:10', 'end_time' => '14:40', 'title' => 'Track C3',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 21],
            ['id' => 16, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Workshop A2',                                                            'category' => 'workshop', 'room' => 'Sumatra',    'rowspan' => 3, 'colspan' => 1, 'order' => 7],
            ['id' => 17, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Workshop B2',                                                            'category' => 'workshop', 'room' => 'Java',       'rowspan' => 3, 'colspan' => 1, 'order' => 8],
            ['id' => 18, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Workshop C2',                                                            'category' => 'workshop', 'room' => 'Sulawesi',   'rowspan' => 3, 'colspan' => 1, 'order' => 9],
            ['id' => 19, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Track D1',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 13],
            ['id' => 20, 'start_time' => '13:35', 'end_time' => '14:05', 'title' => 'Track D2',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 18],
            ['id' => 21, 'start_time' => '14:10', 'end_time' => '14:40', 'title' => 'Track D3',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 22],
            ['id' => 22, 'start_time' => '13:00', 'end_time' => '13:30', 'title' => 'Track E1',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 14],
            ['id' => 23, 'start_time' => '13:35', 'end_time' => '14:05', 'title' => 'Track E2',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 23],
            ['id' => 24, 'start_time' => '14:10', 'end_time' => '14:40', 'title' => 'Track E3',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 24],
            ['id' => 26, 'start_time' => '14:40', 'end_time' => '15:00', 'title' => 'Break Session, Exhibition Booths',                                        'category' => 'break',    'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 9, 'order' => 25],
            ['id' => 27, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Track A4',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 26],
            ['id' => 28, 'start_time' => '15:35', 'end_time' => '16:05', 'title' => 'Track A5',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 31],
            ['id' => 29, 'start_time' => '16:10', 'end_time' => '17:00', 'title' => 'Track A6',                                                               'category' => 'platinum', 'room' => 'Ballroom A', 'rowspan' => 1, 'colspan' => 1, 'order' => 36],
            ['id' => 30, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Track B4',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 27],
            ['id' => 31, 'start_time' => '15:35', 'end_time' => '16:05', 'title' => 'Track B5',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 32],
            ['id' => 32, 'start_time' => '16:10', 'end_time' => '17:00', 'title' => 'Track B6',                                                               'category' => 'platinum', 'room' => 'Ballroom B', 'rowspan' => 1, 'colspan' => 1, 'order' => 37],
            ['id' => 33, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Track C4',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 28],
            ['id' => 34, 'start_time' => '15:35', 'end_time' => '16:05', 'title' => 'Track C5',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 33],
            ['id' => 35, 'start_time' => '16:10', 'end_time' => '17:00', 'title' => 'Track C6',                                                               'category' => 'platinum', 'room' => 'Ballroom C', 'rowspan' => 1, 'colspan' => 1, 'order' => 38],
            ['id' => 36, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Workshop A3',                                                            'category' => 'workshop', 'room' => 'Sumatra',    'rowspan' => 3, 'colspan' => 1, 'order' => 29],
            ['id' => 37, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Workshop B3',                                                            'category' => 'workshop', 'room' => 'Java',       'rowspan' => 3, 'colspan' => 1, 'order' => 30],
            ['id' => 38, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Workshop C3',                                                            'category' => 'workshop', 'room' => 'Sulawesi',   'rowspan' => 3, 'colspan' => 1, 'order' => 31],
            ['id' => 39, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Track D4',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 32],
            ['id' => 40, 'start_time' => '15:35', 'end_time' => '16:05', 'title' => 'Track D5',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 34],
            ['id' => 41, 'start_time' => '16:10', 'end_time' => '17:00', 'title' => 'Track D6',                                                               'category' => 'gold',     'room' => 'Kalimantan', 'rowspan' => 1, 'colspan' => 1, 'order' => 39],
            ['id' => 42, 'start_time' => '15:00', 'end_time' => '15:30', 'title' => 'Track E4',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 33],
            ['id' => 43, 'start_time' => '15:35', 'end_time' => '16:05', 'title' => 'Track E5',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 35],
            ['id' => 44, 'start_time' => '16:10', 'end_time' => '17:00', 'title' => 'Track E6',                                                               'category' => 'gold',     'room' => 'Maluku',     'rowspan' => 1, 'colspan' => 1, 'order' => 40],
        ];

        foreach ($items as $data) {
            \App\Models\AgendaItem::create($data);
        }

        $this->command->info('Agenda structure (floors, rooms, time slots, agenda items) seeded successfully!');
    }
}
