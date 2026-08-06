-- =====================================================================
-- MSD26 — General Sessions & Speakers insert (2026-08-07)
-- ---------------------------------------------------------------------
-- "Query version" of the data inserted via the temp Eloquent script.
-- Matches exactly what is already in the LOCAL DB:
--   agenda_items  id 145-153  (category = 'general')
--   speakers      id 20-28
--   agenda_item_speaker       (order = 1)
--
-- Mapping used:
--   agenda_items.title          = Vendor Name
--   agenda_items.topic_headline = Session Title  (spreadsheet col)
--   agenda_items.description    = Session description
--   speakers.name/title/company = Speaker Name / Speaker title / Vendor
--   speakers.bio                = Short speaker bio
--   speakers.photo              = NULL (no headshot files provided)
--
-- NOTE: This is a ONE-TIME insert with explicit IDs. Do NOT re-run on a
--       DB that already has these rows (would hit duplicate keys).
--       On a fresh DB you may drop the explicit `id` columns to let
--       auto-increment assign them.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1) Speakers
-- ---------------------------------------------------------------------
INSERT INTO `speakers`
  (`id`, `name`, `title`, `company`, `photo`, `bio`, `is_active`, `created_at`, `updated_at`)
VALUES
  (20, 'Johanes Andria', 'Head of Enterprise Sales', 'Red Hat', NULL,
   'Johanes currently leads the Enterprise Sales team at Red Hat. With a career spanning roles at IBM, EMC, and Nutanix, he specializes in driving digital transformation for Indonesia''s largest FSI, Telco, and BUMN institutions. He is a strategic partner to IT Senior leaders, bridging the gap between open-source innovation and complex business objectives',
   1, NOW(), NOW()),

  (21, 'Andreas Diantoro', 'Area Vice President & President Director', 'Salesforce', NULL,
   'Andreas Diantoro is the Area Vice President and President Director of Salesforce Indonesia, where he leads the company''s strategic direction and drives business growth across the country. He bringing over three decades of experience in the technology industry.',
   1, NOW(), NOW()),

  (22, 'Astri Anindita', 'Regional Vice President', 'Anaplan', NULL,
   'Work in progress',
   1, NOW(), NOW()),

  (23, 'Gunawan Susanto', 'Country General Manager, Microsoft Indonesia', 'Microsoft', NULL,
   'Gunawan Susanto is Country General Manager of Microsoft Indonesia, leading the company''s mission to empower every person and organization in Indonesia to achieve more through AI and digital technology. With more than 20 years of leadership experience in the technology industry, Gunawan has worked closely with enterprises, startups, and public sector organizations to drive innovation, business transformation, and inclusive economic growth.',
   1, NOW(), NOW()),

  (24, 'Rudy Tanuwidjaja (Acan)', 'Director of Solution Engineer', 'Cloudera', NULL,
   'Rudy Tanuwidjaya leads the Solutions Engineering team for Cloudera Indonesia specializing in helping enterprise organizations scale AI adoption and modernize their data architectures. He acts as a key technical guide for business-critical deployments across Indonesia, empowering clients to implement real-time analytics, machine learning, and secure, governed Private AI solutions directly where their data lives.',
   1, NOW(), NOW()),

  (25, 'Arthur Siahaan', 'Solutions Consulting Manager, Palo Alto Networks, Indonesia', 'Palo Alto Networks', NULL,
   'Dr. Arthur Siahaan is a Manager, Solutions Consulting Palo Alto Networks Indonesia. In this role, Arthur is responsible for leading the solutions consulting team in promoting Palo Alto Networks platforms and the customer lifecycle journey, among others. With over 28 years of experience in technology industry, Arthur has held several managerial roles and technical expertise certification and Cisco Certified Design Expert (CCDE) #20140029 (active in 2014 till 2022).',
   1, NOW(), NOW()),

  (26, 'Kitman Cheung', 'Chief Technology Officer & Director of Pre-sales Engineering IBM ASEAN', 'IBM', NULL,
   'Kitman Cheung is the Pre-Sales Engineering Leader for IBM ASEAN, bringing over 17 years of dedicated service to IBM. Throughout his tenure, Kitman has specialized in Analytics and, more recently, expanded his expertise to encompass AI, Automation, Security, and Hybrid Cloud technologies. He has held various technical positions across development, product management, partner ecosystem, and technical pre-sales organizations. In his current role, Kitman works with key IBM customers in ASEAN on their digital transformation initiatives. The main goals of his team is to help customers deliver innovation solution using the latest AI, Automation, Security and Hybrid Cloud technologies.',
   1, NOW(), NOW()),

  (27, 'Anthony Amni', 'TBC', 'AWS (AMAZON WEB SERVICES)', NULL,
   'TBC',
   1, NOW(), NOW()),

  (28, 'Karim Siregar', 'Country Director', 'Google Cloud Platform', NULL,
   'TBC',
   1, NOW(), NOW());

-- ---------------------------------------------------------------------
-- 2) General Session agenda items
-- ---------------------------------------------------------------------
INSERT INTO `agenda_items`
  (`id`, `title`, `topic_headline`, `description`, `key_highlights`, `category`,
   `agenda_type`, `workshop_id`, `track_id`, `room`, `start_time`, `end_time`,
   `date`, `order`, `rowspan`, `colspan`, `is_registrable`, `capacity`,
   `feedback_enabled`, `registration_open`, `created_at`, `updated_at`)
VALUES
  (145, 'Red Hat', 'AI Democracy in the Republix of Open Source',
   'Many organizations are trapped into a vendor lock in when they run their AI projects. It leads to inflexibility and inability to control the cost for AI. This session will share about Red Hat approach from Open Source perspective to give freedom to the customer to build, run and scale AI on their own terms',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (146, 'Salesforce',
   'Welcome to the Agentic Enterprise where Humans, agents & platforms drive customer success together',
   'Discover how AI agents, human collaboration, and data sync transform enterprise operations. Learn practical strategies to safely deploy Agentic AI, maintain strict local compliance, and leverage precision data—empowering your organization to accelerate decision-making, elevate customer success, and unlock sustainable growth only at Metrodata Solution Day 2026.',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (147, 'Anaplan',
   'Planning in the Age of AI: How Telkom Indonesia is Driving Smarter Decisions with Anaplan AI',
   'Work in progress',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (148, 'Microsoft', 'Built to Win - Indonesia''s Moves from AI Adoption to Value',
   'AI ambition is everywhere; advantage is not. New Microsoft research reveals Indonesia already has twice the global share of advanced AI users — a head start few markets can claim. But a head start only counts when it''s converted. This keynote shows how Indonesia''s leading organizations turn ready talent into real outcomes: rearchitecting how work flows, pairing intelligence with trust, and putting agents to work alongside people — so AI is built, run, and scaled into measurable business value.',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (149, 'Cloudera', 'Bringing AI to Data - Anywhere',
   'Cloudera''s "Bringing AI to Data – Anywhere" framework helps enterprises overcome AI adoption hurdles by deploying secure, governed private AI models and open data lakehouses directly where data resides—on-premises, multi-cloud, or at the edge.',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (150, 'Palo Alto Networks', 'TBC', 'TBC',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (151, 'IBM', 'To be confirm', 'To be confirm',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (152, 'AWS (AMAZON WEB SERVICES)', 'TBC', 'TBC',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()),

  (153, 'Google Cloud Platform', 'tbc', 'tbc',
   NULL, 'general', NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
   NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW());

-- ---------------------------------------------------------------------
-- 3) Link speakers to sessions (agenda_item_speaker)
-- ---------------------------------------------------------------------
INSERT INTO `agenda_item_speaker`
  (`agenda_item_id`, `speaker_id`, `order`, `key_highlights`, `presentation_title`,
   `presentation_description`, `created_at`, `updated_at`)
VALUES
  (145, 20, 1, NULL, NULL, NULL, NOW(), NOW()),
  (146, 21, 1, NULL, NULL, NULL, NOW(), NOW()),
  (147, 22, 1, NULL, NULL, NULL, NOW(), NOW()),
  (148, 23, 1, NULL, NULL, NULL, NOW(), NOW()),
  (149, 24, 1, NULL, NULL, NULL, NOW(), NOW()),
  (150, 25, 1, NULL, NULL, NULL, NOW(), NOW()),
  (151, 26, 1, NULL, NULL, NULL, NOW(), NOW()),
  (152, 27, 1, NULL, NULL, NULL, NOW(), NOW()),
  (153, 28, 1, NULL, NULL, NULL, NOW(), NOW());
