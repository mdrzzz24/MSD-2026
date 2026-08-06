-- =====================================================================
-- MSD26 — FULL insert: General Sessions + Speakers (idempotent, 2026-08-07)
-- ---------------------------------------------------------------------
-- ONE script that inserts everything in one go, safe to re-run:
--   • Speakers   — inserted ONLY if a speaker with the same `name` does
--                  NOT exist yet (no duplicates; existing one is reused).
--   • Agenda     — inserted ONLY if no general session with the same
--                  (title + topic_headline) exists yet.
--   • Pivot      — linked using the resolved speaker/agenda ids, and only
--                  if that link is not already present.
--
-- Field mapping:
--   agenda_items.title          = Vendor Name
--   agenda_items.topic_headline = Session Title
--   agenda_items.description    = Session description
--   speakers.name/title/company = Speaker Name / Speaker title / Vendor
--   speakers.bio                = Short speaker bio
--   speakers.photo              = NULL (no headshot files provided)
--
-- Works in `mysql` CLI and phpMyAdmin. No stored procedures required.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1) SPEAKERS — insert only the ones that don't exist yet
-- ---------------------------------------------------------------------
INSERT INTO `speakers`
  (`name`, `title`, `company`, `photo`, `bio`, `is_active`, `created_at`, `updated_at`)
SELECT tmp.name, tmp.title, tmp.company, NULL, tmp.bio, 1, NOW(), NOW()
FROM (
  SELECT 'Johanes Andria' AS name, 'Head of Enterprise Sales' AS title, 'Red Hat' AS company,
         'Johanes currently leads the Enterprise Sales team at Red Hat. With a career spanning roles at IBM, EMC, and Nutanix, he specializes in driving digital transformation for Indonesia''s largest FSI, Telco, and BUMN institutions. He is a strategic partner to IT Senior leaders, bridging the gap between open-source innovation and complex business objectives' AS bio
  UNION ALL SELECT 'Andreas Diantoro', 'Area Vice President & President Director', 'Salesforce',
         'Andreas Diantoro is the Area Vice President and President Director of Salesforce Indonesia, where he leads the company''s strategic direction and drives business growth across the country. He bringing over three decades of experience in the technology industry.'
  UNION ALL SELECT 'Astri Anindita', 'Regional Vice President', 'Anaplan',
         'Work in progress'
  UNION ALL SELECT 'Gunawan Susanto', 'Country General Manager, Microsoft Indonesia', 'Microsoft',
         'Gunawan Susanto is Country General Manager of Microsoft Indonesia, leading the company''s mission to empower every person and organization in Indonesia to achieve more through AI and digital technology. With more than 20 years of leadership experience in the technology industry, Gunawan has worked closely with enterprises, startups, and public sector organizations to drive innovation, business transformation, and inclusive economic growth.'
  UNION ALL SELECT 'Rudy Tanuwidjaja (Acan)', 'Director of Solution Engineer', 'Cloudera',
         'Rudy Tanuwidjaya leads the Solutions Engineering team for Cloudera Indonesia specializing in helping enterprise organizations scale AI adoption and modernize their data architectures. He acts as a key technical guide for business-critical deployments across Indonesia, empowering clients to implement real-time analytics, machine learning, and secure, governed Private AI solutions directly where their data lives.'
  UNION ALL SELECT 'Arthur Siahaan', 'Solutions Consulting Manager, Palo Alto Networks, Indonesia', 'Palo Alto Networks',
         'Dr. Arthur Siahaan is a Manager, Solutions Consulting Palo Alto Networks Indonesia. In this role, Arthur is responsible for leading the solutions consulting team in promoting Palo Alto Networks platforms and the customer lifecycle journey, among others. With over 28 years of experience in technology industry, Arthur has held several managerial roles and technical expertise certification and Cisco Certified Design Expert (CCDE) #20140029 (active in 2014 till 2022).'
  UNION ALL SELECT 'Kitman Cheung', 'Chief Technology Officer & Director of Pre-sales Engineering IBM ASEAN', 'IBM',
         'Kitman Cheung is the Pre-Sales Engineering Leader for IBM ASEAN, bringing over 17 years of dedicated service to IBM. Throughout his tenure, Kitman has specialized in Analytics and, more recently, expanded his expertise to encompass AI, Automation, Security, and Hybrid Cloud technologies. He has held various technical positions across development, product management, partner ecosystem, and technical pre-sales organizations. In his current role, Kitman works with key IBM customers in ASEAN on their digital transformation initiatives. The main goals of his team is to help customers deliver innovation solution using the latest AI, Automation, Security and Hybrid Cloud technologies.'
  UNION ALL SELECT 'Anthony Amni', 'TBC', 'AWS (AMAZON WEB SERVICES)',
         'TBC'
  UNION ALL SELECT 'Karim Siregar', 'Country Director', 'Google Cloud Platform',
         'TBC'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `speakers` s WHERE s.name = tmp.name);

-- ---------------------------------------------------------------------
-- 2) Resolve speaker ids (existing or just-inserted) into variables
-- ---------------------------------------------------------------------
SET @spk_redhat     = (SELECT id FROM `speakers` WHERE name = 'Johanes Andria'),
    @spk_salesforce = (SELECT id FROM `speakers` WHERE name = 'Andreas Diantoro'),
    @spk_anaplan    = (SELECT id FROM `speakers` WHERE name = 'Astri Anindita'),
    @spk_microsoft  = (SELECT id FROM `speakers` WHERE name = 'Gunawan Susanto'),
    @spk_cloudera   = (SELECT id FROM `speakers` WHERE name = 'Rudy Tanuwidjaja (Acan)'),
    @spk_paloalto   = (SELECT id FROM `speakers` WHERE name = 'Arthur Siahaan'),
    @spk_ibm        = (SELECT id FROM `speakers` WHERE name = 'Kitman Cheung'),
    @spk_aws        = (SELECT id FROM `speakers` WHERE name = 'Anthony Amni'),
    @spk_gcp        = (SELECT id FROM `speakers` WHERE name = 'Karim Siregar');

-- ---------------------------------------------------------------------
-- 3) GENERAL SESSIONS — insert only the ones that don't exist yet
--    (matched by title + topic_headline, so it never duplicates
--     or overwrites an existing session)
-- ---------------------------------------------------------------------
INSERT INTO `agenda_items`
  (`title`, `topic_headline`, `description`, `key_highlights`, `category`,
   `agenda_type`, `workshop_id`, `track_id`, `room`, `start_time`, `end_time`,
   `date`, `order`, `rowspan`, `colspan`, `is_registrable`, `capacity`,
   `feedback_enabled`, `registration_open`, `created_at`, `updated_at`)
SELECT tmp.title, tmp.topic_headline, tmp.description, NULL, 'general',
       NULL, NULL, NULL, NULL, '00:00:00', '00:00:00',
       NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()
FROM (
  SELECT 'Red Hat' AS title, 'AI Democracy in the Republix of Open Source' AS topic_headline,
         'Many organizations are trapped into a vendor lock in when they run their AI projects. It leads to inflexibility and inability to control the cost for AI. This session will share about Red Hat approach from Open Source perspective to give freedom to the customer to build, run and scale AI on their own terms' AS description
  UNION ALL SELECT 'Salesforce',
         'Welcome to the Agentic Enterprise where Humans, agents & platforms drive customer success together',
         'Discover how AI agents, human collaboration, and data sync transform enterprise operations. Learn practical strategies to safely deploy Agentic AI, maintain strict local compliance, and leverage precision data—empowering your organization to accelerate decision-making, elevate customer success, and unlock sustainable growth only at Metrodata Solution Day 2026.'
  UNION ALL SELECT 'Anaplan',
         'Planning in the Age of AI: How Telkom Indonesia is Driving Smarter Decisions with Anaplan AI',
         'Work in progress'
  UNION ALL SELECT 'Microsoft',
         'Built to Win - Indonesia''s Moves from AI Adoption to Value',
         'AI ambition is everywhere; advantage is not. New Microsoft research reveals Indonesia already has twice the global share of advanced AI users — a head start few markets can claim. But a head start only counts when it''s converted. This keynote shows how Indonesia''s leading organizations turn ready talent into real outcomes: rearchitecting how work flows, pairing intelligence with trust, and putting agents to work alongside people — so AI is built, run, and scaled into measurable business value.'
  UNION ALL SELECT 'Cloudera',
         'Bringing AI to Data - Anywhere',
         'Cloudera''s "Bringing AI to Data – Anywhere" framework helps enterprises overcome AI adoption hurdles by deploying secure, governed private AI models and open data lakehouses directly where data resides—on-premises, multi-cloud, or at the edge.'
  UNION ALL SELECT 'Palo Alto Networks', 'TBC', 'TBC'
  UNION ALL SELECT 'IBM', 'To be confirm', 'To be confirm'
  UNION ALL SELECT 'AWS (AMAZON WEB SERVICES)', 'TBC', 'TBC'
  UNION ALL SELECT 'Google Cloud Platform', 'tbc', 'tbc'
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `agenda_items` ai
  WHERE ai.title = tmp.title AND ai.topic_headline = tmp.topic_headline
);

-- ---------------------------------------------------------------------
-- 4) Resolve agenda ids into variables
-- ---------------------------------------------------------------------
SET @agenda_redhat     = (SELECT id FROM `agenda_items` WHERE title = 'Red Hat'    AND topic_headline = 'AI Democracy in the Republix of Open Source'),
    @agenda_salesforce = (SELECT id FROM `agenda_items` WHERE title = 'Salesforce' AND topic_headline = 'Welcome to the Agentic Enterprise where Humans, agents & platforms drive customer success together'),
    @agenda_anaplan    = (SELECT id FROM `agenda_items` WHERE title = 'Anaplan'    AND topic_headline = 'Planning in the Age of AI: How Telkom Indonesia is Driving Smarter Decisions with Anaplan AI'),
    @agenda_microsoft  = (SELECT id FROM `agenda_items` WHERE title = 'Microsoft'  AND topic_headline = 'Built to Win - Indonesia''s Moves from AI Adoption to Value'),
    @agenda_cloudera   = (SELECT id FROM `agenda_items` WHERE title = 'Cloudera'   AND topic_headline = 'Bringing AI to Data - Anywhere'),
    @agenda_paloalto   = (SELECT id FROM `agenda_items` WHERE title = 'Palo Alto Networks' AND topic_headline = 'TBC'),
    @agenda_ibm        = (SELECT id FROM `agenda_items` WHERE title = 'IBM'        AND topic_headline = 'To be confirm'),
    @agenda_aws        = (SELECT id FROM `agenda_items` WHERE title = 'AWS (AMAZON WEB SERVICES)' AND topic_headline = 'TBC'),
    @agenda_gcp        = (SELECT id FROM `agenda_items` WHERE title = 'Google Cloud Platform' AND topic_headline = 'tbc');

-- ---------------------------------------------------------------------
-- 5) LINK speakers <-> sessions (only if the link is not already there)
-- ---------------------------------------------------------------------
INSERT INTO `agenda_item_speaker`
  (`agenda_item_id`, `speaker_id`, `order`, `key_highlights`, `presentation_title`,
   `presentation_description`, `created_at`, `updated_at`)
SELECT tmp.agenda_item_id, tmp.speaker_id, 1, NULL, NULL, NULL, NOW(), NOW()
FROM (
  SELECT @agenda_redhat     AS agenda_item_id, @spk_redhat     AS speaker_id
  UNION ALL SELECT @agenda_salesforce, @spk_salesforce
  UNION ALL SELECT @agenda_anaplan,    @spk_anaplan
  UNION ALL SELECT @agenda_microsoft,  @spk_microsoft
  UNION ALL SELECT @agenda_cloudera,   @spk_cloudera
  UNION ALL SELECT @agenda_paloalto,   @spk_paloalto
  UNION ALL SELECT @agenda_ibm,        @spk_ibm
  UNION ALL SELECT @agenda_aws,        @spk_aws
  UNION ALL SELECT @agenda_gcp,        @spk_gcp
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `agenda_item_speaker` a
  WHERE a.agenda_item_id = tmp.agenda_item_id
    AND a.speaker_id     = tmp.speaker_id
);

-- ---------------------------------------------------------------------
-- 6) Summary
-- ---------------------------------------------------------------------
SELECT 'speakers' AS `table`, COUNT(*) AS total FROM `speakers`
UNION ALL SELECT 'agenda_items (general)', COUNT(*) FROM `agenda_items` WHERE category = 'general'
UNION ALL SELECT 'agenda_item_speaker links', COUNT(*) FROM `agenda_item_speaker`;
