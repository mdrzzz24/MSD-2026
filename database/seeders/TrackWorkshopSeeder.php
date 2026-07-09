<?php

namespace Database\Seeders;

use App\Models\AgendaItem;
use App\Models\Track;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrackWorkshopSeeder extends Seeder
{
    public function run(): void
    {
        // ── Disable FK checks for truncation ──
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Detach links first, then truncate
        AgendaItem::query()->update(['track_id' => null, 'workshop_id' => null]);
        Track::truncate();
        Workshop::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ═══════════════════════════════════════════════════
        // 1. TRACKS
        // ═══════════════════════════════════════════════════
        $tracks = [
            'Track A' => ['description' => 'Platinum Track — Ballroom A', 'is_active' => true],
            'Track B' => ['description' => 'Platinum Track — Ballroom B', 'is_active' => true],
            'Track C' => ['description' => 'Platinum Track — Ballroom C', 'is_active' => true],
            'Track D' => ['description' => 'Gold Track — Kalimantan',     'is_active' => true],
            'Track E' => ['description' => 'Gold Track — Maluku',         'is_active' => true],
        ];

        $trackMap = []; // title => Track model
        foreach ($tracks as $title => $data) {
            $trackMap[$title] = Track::create([
                'title'       => $title,
                'description' => $data['description'],
                'is_active'   => $data['is_active'],
            ]);
        }

        // ── Link agenda items to tracks ──
        // Track A → sessions in Ballroom A with platinum category
        $this->linkToTrack('Track A', 'Ballroom A', 'platinum');
        // Track B → sessions in Ballroom B with platinum category
        $this->linkToTrack('Track B', 'Ballroom B', 'platinum');
        // Track C → sessions in Ballroom C with platinum category
        $this->linkToTrack('Track C', 'Ballroom C', 'platinum');
        // Track D → sessions in Kalimantan with gold category
        $this->linkToTrack('Track D', 'Kalimantan', 'gold');
        // Track E → sessions in Maluku with gold category
        $this->linkToTrack('Track E', 'Maluku', 'gold');

        // ═══════════════════════════════════════════════════
        // 2. WORKSHOPS
        // ═══════════════════════════════════════════════════
        $workshops = [
            'Workshop A' => ['room' => 'Sumatra',   'description' => 'Workshop sessions in Sumatra room'],
            'Workshop B' => ['room' => 'Java',      'description' => 'Workshop sessions in Java room'],
            'Workshop C' => ['room' => 'Sulawesi',  'description' => 'Workshop sessions in Sulawesi room'],
        ];

        $workshopMap = []; // title => Workshop model
        foreach ($workshops as $title => $data) {
            $workshopMap[$title] = Workshop::create([
                'title'             => $title,
                'description'       => $data['description'],
                'room'              => $data['room'],
                'capacity'          => 30,
                'registration_open' => true,
            ]);
        }

        // ── Link agenda items to workshops ──
        $this->linkToWorkshop('Workshop A', 'Sumatra');
        $this->linkToWorkshop('Workshop B', 'Java');
        $this->linkToWorkshop('Workshop C', 'Sulawesi');

        $this->command->info('Tracks and Workshops seeded and linked to agenda items successfully!');
    }

    /**
     * Link all agenda items in a given room + category to a track.
     */
    private function linkToTrack(string $trackTitle, string $room, string $category): void
    {
        $track = Track::where('title', $trackTitle)->first();
        if (!$track) {
            $this->command->warn("Track \"{$trackTitle}\" not found, skipping.");
            return;
        }

        $count = AgendaItem::where('room', $room)
            ->where('category', $category)
            ->update(['track_id' => $track->id]);

        $this->command->line("  Linked {$count} items to {$trackTitle}");
    }

    /**
     * Link all agenda items in a given room to a workshop.
     */
    private function linkToWorkshop(string $workshopTitle, string $room): void
    {
        $workshop = Workshop::where('title', $workshopTitle)->first();
        if (!$workshop) {
            $this->command->warn("Workshop \"{$workshopTitle}\" not found, skipping.");
            return;
        }

        $count = AgendaItem::where('room', $room)
            ->where('category', 'workshop')
            ->update(['workshop_id' => $workshop->id]);

        $this->command->line("  Linked {$count} items to {$workshopTitle}");
    }
}
