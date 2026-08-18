<?php

namespace Database\Seeders;

use App\Models\Booth;
use Illuminate\Database\Seeder;

class BoothSeeder extends Seeder
{
    /**
     * Seed the booths table with the organizer's booth list.
     *
     * Idempotent & non-destructive: existing booths are never deleted or
     * overwritten (keyed on the booth name via firstOrCreate), so running this
     * on a database that already has booths only ADDS the missing ones.
     *
     * Run it standalone (won't touch any other data):
     *   php artisan db:seed --class=BoothSeeder
     */
    public function run(): void
    {
        // Booth names in display order. The source list had "Google Cloud" and
        // "Consultation Area L1" twice each — firstOrCreate collapses them to a
        // single booth per name (edit/duplicate manually via Admin → Booths if
        // more than one booth should share a name).
        $booths = [
            'Anaplan',
            'AWS (Amazon Web Services)',
            'Cloudera',
            'Google Cloud (P)',
            'Google Cloud (W)',
            'IBM Distributor',
            'Microsoft',
            'Palo Alto Networks',
            'Red Hat',
            'Salesforce',
            'Alibaba Cloud',
            'Cloudflare',
            'Confluent',
            'NetApp',
            'Sangfor',
            'SingleStore',
            'Workday',
            'BytePlus',
            'Cyble',
            'Datadog',
            'Dynatrace',
            'EDB PostgreSQL',
            'Fortinet',
            'HPE (Hewlett Packard Enterprise)',
            'HP',
            'Huawei',
            'Kong',
            'Lark',
            'Proofpoint',
            'Tenable',
            'ASUS Business',
            'FanRuan',
            'Snowflake',
            'Splunk | Cisco',
            'Consultation Area L1',
            'Consultation Area L2',
        ];

        $created = 0;
        $skipped = 0;

        foreach ($booths as $idx => $name) {
            // order = list position (0-based) so the Admin → Booths list follows
            // the organizer's order for newly created booths; existing booths
            // are left untouched.
            $booth = Booth::firstOrCreate(
                ['name' => $name],
                ['description' => null, 'is_active' => true, 'order' => $idx]
            );

            if ($booth->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command?->info("Booths seeded: {$created} created, {$skipped} already present.");
    }
}
