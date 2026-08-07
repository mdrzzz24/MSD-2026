<?php

namespace Database\Seeders;

use App\Models\AgendaItem;
use App\Models\Speaker;
use Illuminate\Database\Seeder;

/**
 * Seeds the 9 sponsor GENERAL SESSIONS + their speakers.
 *
 * IDEMPOTENT & NON-DESTRUCTIVE — safe to run on any database:
 *   - Speakers: `updateOrCreate` by name (fills/refreshes title/company/bio; never deletes).
 *   - Sessions: `firstOrCreate` by (title + topic_headline + category='general') — only created if missing.
 *   - Pivot:    `syncWithoutDetaching` — only adds missing speaker links.
 * Existing data (registrants, workshops, tracks, other sessions) is untouched.
 *
 * Field mapping (user-confirmed):
 *   agenda_items.title          = Vendor Name
 *   agenda_items.topic_headline = Session Title
 *   agenda_items.description    = Session description
 *   speakers.name/title/company = Speaker Name / Speaker title / Vendor
 *
 * Run:  php artisan db:seed --class=GeneralSessionSeeder
 */
class GeneralSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'vendor'         => 'Red Hat',
                'speaker_name'   => 'Johanes Andria',
                'speaker_title'  => 'Head of Enterprise Sales',
                'speaker_bio'    => "Johanes currently leads the Enterprise Sales team at Red Hat. With a career spanning roles at IBM, EMC, and Nutanix, he specializes in driving digital transformation for Indonesia's largest FSI, Telco, and BUMN institutions. He is a strategic partner to IT Senior leaders, bridging the gap between open-source innovation and complex business objectives",
                'session_title'  => 'AI Democracy in the Republix of Open Source',
                'session_desc'   => "Many organizations are trapped into a vendor lock in when they run their AI projects. It leads to inflexibility and inability to control the cost for AI. This session will share about Red Hat approach from Open Source perspective to give freedom to the customer to build, run and scale AI on their own terms",
            ],
            [
                'vendor'         => 'Salesforce',
                'speaker_name'   => 'Andreas Diantoro',
                'speaker_title'  => 'Area Vice President & President Director',
                'speaker_bio'    => "Andreas Diantoro is the Area Vice President and President Director of Salesforce Indonesia, where he leads the company's strategic direction and drives business growth across the country. He bringing over three decades of experience in the technology industry.",
                'session_title'  => 'Welcome to the Agentic Enterprise where Humans, agents & platforms drive customer success together',
                'session_desc'   => 'Discover how AI agents, human collaboration, and data sync transform enterprise operations. Learn practical strategies to safely deploy Agentic AI, maintain strict local compliance, and leverage precision data—empowering your organization to accelerate decision-making, elevate customer success, and unlock sustainable growth only at Metrodata Solution Day 2026.',
            ],
            [
                'vendor'         => 'Anaplan',
                'speaker_name'   => 'Astri Anindita',
                'speaker_title'  => 'Regional Vice President',
                'speaker_bio'    => 'Work in progress',
                'session_title'  => 'Planning in the Age of AI: How Telkom Indonesia is Driving Smarter Decisions with Anaplan AI',
                'session_desc'   => 'Work in progress',
            ],
            [
                'vendor'         => 'Microsoft',
                'speaker_name'   => 'Gunawan Susanto',
                'speaker_title'  => 'Country General Manager, Microsoft Indonesia',
                'speaker_bio'    => "Gunawan Susanto is Country General Manager of Microsoft Indonesia, leading the company's mission to empower every person and organization in Indonesia to achieve more through AI and digital technology. With more than 20 years of leadership experience in the technology industry, Gunawan has worked closely with enterprises, startups, and public sector organizations to drive innovation, business transformation, and inclusive economic growth.",
                'session_title'  => "Built to Win - Indonesia's Moves from AI Adoption to Value",
                'session_desc'   => "AI ambition is everywhere; advantage is not. New Microsoft research reveals Indonesia already has twice the global share of advanced AI users — a head start few markets can claim. But a head start only counts when it's converted. This keynote shows how Indonesia's leading organizations turn ready talent into real outcomes: rearchitecting how work flows, pairing intelligence with trust, and putting agents to work alongside people — so AI is built, run, and scaled into measurable business value.",
            ],
            [
                'vendor'         => 'Cloudera',
                'speaker_name'   => 'Rudy Tanuwidjaja (Acan)',
                'speaker_title'  => 'Director of Solution Engineer',
                'speaker_bio'    => "Rudy Tanuwidjaya leads the Solutions Engineering team for Cloudera Indonesia specializing in helping enterprise organizations scale AI adoption and modernize their data architectures. He acts as a key technical guide for business-critical deployments across Indonesia, empowering clients to implement real-time analytics, machine learning, and secure, governed Private AI solutions directly where their data lives.",
                'session_title'  => 'Bringing AI to Data - Anywhere',
                'session_desc'   => "Cloudera's \"Bringing AI to Data – Anywhere\" framework helps enterprises overcome AI adoption hurdles by deploying secure, governed private AI models and open data lakehouses directly where data resides—on-premises, multi-cloud, or at the edge.",
            ],
            [
                'vendor'         => 'Palo Alto Networks',
                'speaker_name'   => 'Arthur Siahaan',
                'speaker_title'  => 'Solutions Consulting Manager, Palo Alto Networks, Indonesia',
                'speaker_bio'    => "Dr. Arthur Siahaan is a Manager, Solutions Consulting Palo Alto Networks Indonesia. In this role, Arthur is responsible for leading the solutions consulting team in promoting Palo Alto Networks platforms and the customer lifecycle journey, among others. With over 28 years of experience in technology industry, Arthur has held several managerial roles and technical expertise certification and Cisco Certified Design Expert (CCDE) #20140029 (active in 2014 till 2022).",
                'session_title'  => 'TBC',
                'session_desc'   => 'TBC',
            ],
            [
                'vendor'         => 'IBM',
                'speaker_name'   => 'Kitman Cheung',
                'speaker_title'  => 'Chief Technology Officer & Director of Pre-sales Engineering IBM ASEAN',
                'speaker_bio'    => "Kitman Cheung is the Pre-Sales Engineering Leader for IBM ASEAN, bringing over 17 years of dedicated service to IBM. Throughout his tenure, Kitman has specialized in Analytics and, more recently, expanded his expertise to encompass AI, Automation, Security, and Hybrid Cloud technologies. He has held various technical positions across development, product management, partner ecosystem, and technical pre-sales organizations. In his current role, Kitman works with key IBM customers in ASEAN on their digital transformation initiatives. The main goals of his team is to help customers deliver innovation solution using the latest AI, Automation, Security and Hybrid Cloud technologies.",
                'session_title'  => 'To be confirm',
                'session_desc'   => 'To be confirm',
            ],
            [
                'vendor'         => 'AWS (AMAZON WEB SERVICES)',
                'speaker_name'   => 'Anthony Amni',
                'speaker_title'  => 'TBC',
                'speaker_bio'    => 'TBC',
                'session_title'  => 'TBC',
                'session_desc'   => 'TBC',
            ],
            [
                'vendor'         => 'Google Cloud Platform',
                'speaker_name'   => 'Karim Siregar',
                'speaker_title'  => 'Country Director',
                'speaker_bio'    => 'TBC',
                'session_title'  => 'tbc',
                'session_desc'   => 'tbc',
            ],
        ];

        $count = ['sessions' => 0, 'speakers' => 0];

        foreach ($sessions as $s) {
            $speaker = Speaker::updateOrCreate(
                ['name' => $s['speaker_name']],
                [
                    'title'     => $s['speaker_title'],
                    'company'   => $s['vendor'],
                    'bio'       => $s['speaker_bio'],
                    'is_active' => true,
                ]
            );
            if ($speaker->wasRecentlyCreated) {
                $count['speakers']++;
            }

            $item = AgendaItem::firstOrCreate(
                [
                    'title'          => $s['vendor'],
                    'topic_headline' => $s['session_title'],
                    'category'       => 'general',
                ],
                [
                    'description'    => $s['session_desc'],
                    'key_highlights' => null,
                    'agenda_type'    => null,
                    'workshop_id'    => null,
                    'track_id'       => null,
                    'room'           => null,
                    'start_time'     => '00:00:00',
                    'end_time'       => '00:00:00',
                    'date'           => null,
                    'order'          => 0,
                    'is_registrable' => false,
                ]
            );
            if ($item->wasRecentlyCreated) {
                $count['sessions']++;
            }

            $item->speakers()->syncWithoutDetaching([$speaker->id => ['order' => 1]]);
        }

        $this->command?->info("GeneralSessionSeeder: {$count['sessions']} session(s) created, {$count['speakers']} speaker(s) created. (existing kept, nothing deleted)");
    }
}
