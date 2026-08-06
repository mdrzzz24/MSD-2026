-- =====================================================================
-- MSD26 — FULL insert: Track Sessions + Speakers (idempotent, 2026-08-07)
-- ---------------------------------------------------------------------
-- ONE script that inserts everything in one go, safe to re-run:
--   • Speakers     — inserted ONLY if a speaker with the same `name` does
--                    NOT exist yet (existing one is reused, not duplicated).
--   • Track agenda — inserted ONLY if a session with the same
--                    (track + title + topic_headline) does not exist yet.
--   • Pivot        — linked by joining resolved speaker/agenda ids, and
--                    only if that link is not already present.
--
-- Field mapping (confirmed with user):
--   agenda_items.title          = Session Title
--   agenda_items.topic_headline = Topic Headline
--   agenda_items.description    = Session description
--   agenda_items.category='track', agenda_type='track', track_id = existing vendor track
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
  SELECT 'Sandra Agustina' AS name, 'Principal Solution Consultant, Anaplan' AS title, 'Anaplan' AS company,
         'Sandra Agustina currently holds the esteemed position of Principal Solutions Architect at Anaplan, utilizing her expertise to advance business enhancement through the development of comprehensive solutions tailored to address diverse customer needs. With a wealth of experience in the realm of technology solutions, Sandra adeptly leverages her knowledge to drive substantial improvements in planning and budgeting for a wide range of clients. Her unwavering dedication to continuous learning and her exceptional communication skills render her an invaluable asset to any technology-driven endeavor' AS bio
  UNION ALL SELECT 'Magdalena Cynthia', 'Presales Support Manager PT Metrodata Electronics Tbk', 'Anaplan',
         'Magdalena Cynthia is an experienced and highly dynamic professional with a strong background in Supply Chain Management within the pharmaceuticals and tobacco industries. In 2023, she joined Metrodata as an Anaplan Consultant, bringing 2 years of experience in Anaplan consulting and implementation, specializing in Consolidated Financial Reporting and Management Reporting. Currently, Cynthia works as a Presales Consultant, delivering Anaplan''s value proposition to help customers enhance their business agility. In this role, she collaborates closely with sales teams and principals to identify customer needs and tailor solutions that address each client''s unique business challenges.'
  UNION ALL SELECT 'Nicolaus Bintang Ardana', 'Presales Anaplan PT Metrodata Electronics Tbk', 'Anaplan',
         'Nicolaus Bintang Ardana, or simply Bintang, has been with Metrodata for the past three years. During his first two years, he worked as an Anaplan Implementation Consultant, supporting projects for Pertamina Hulu Energi across budgeting, financial consolidation, and management reporting. He currently serves as a Presales Support, focusing on Anaplan Financial Consolidation and Reporting (FCR) to help organizations modernize and streamline their financial consolidation processes'
  UNION ALL SELECT 'Hinwoto Jun', 'Senior Partner Solution Architect', 'AWS (AMAZON WEB SERVICES)',
         'a Senior Solutions Architect at AWS Indonesia, helping enterprises and partners build, migrate, and modernize on the cloud with AI-powered solutions like Amazon Bedrock, Amazon Quick and Kiro'
  UNION ALL SELECT 'NUR MUHAMMAD ZAKI BUDIPRASETYA', 'AWS Solutions Architect for Enterprise Customer', 'AWS (AMAZON WEB SERVICES)',
         'A Cloud Solutions Architect with over eight years of experience as a .NET developer, leading teams, executing massive cloud migrations, optimizing costs, and bridging business needs with diverse AWS solutions'
  UNION ALL SELECT 'Glen Johanes', 'Senior AWS Solutions Architect', 'AWS (AMAZON WEB SERVICES)',
         'TBC'
  UNION ALL SELECT 'ZEIN RASYID HIMAMI', 'Solutions Architect for AI & Advanced Analytics Solution', 'AWS (AMAZON WEB SERVICES)',
         'A talented Data Scientist and AI Engineer who builds highly scalable artificial intelligence systems, consistently focusing on driving meaningful business impact and continuous technological innovation'
  UNION ALL SELECT 'Renaldi Azhar', 'Senior Solution Architect, BytePlus', 'BytePlus',
         'Senior Solution Architect at BytePlus, specializes in designing scalable AI architectures. He empowers enterprises to drive innovation and business growth through advanced AI solutions.'
  UNION ALL SELECT 'Eduard Bangga', 'Principal Solution Architect', 'Cloudera',
         'Eduard Bangga is an accomplished AI and Cloud Specialist currently modernizing data architecture at Cloudera. He brings over 9 years of experience in data and cloud solutions. Prior to his current role as a Solutions Engineer at Cloudera, Eduard served as the Director, Head of Data & Analytics at Data Labs Analytics, where he led a team to deliver data solutions and drive business performance through Big Data and Data Lakehouse implementations. Eduard is a professional with a strong background in solution architecture, big data, and cloud technologies.'
  UNION ALL SELECT 'Triano Nurhikmat', 'Solution Engineer', 'Cloudera',
         'TBC'
  UNION ALL SELECT 'Engin Cukuroglu', 'Head of Solutions Engineering Asia, Confluent', 'Confluent, an IBM Company',
         'Engin Cukuroglu is a Solutions Engineering Leader at Confluent with expertise in distributed systems, AI, data science, and scalable analytics, leveraging a PhD in Computational Science and extensive Apache Kafka experience.'
  UNION ALL SELECT 'Caroline Parapat', 'Enterprise Sales Manager, Cyble', 'Cyble Inc',
         'Caroline Parapat is the Enterprise Sales Manager for Indonesia at Cyble. She specializes in delivering AI-powered cyber threat intelligence, dark web monitoring, and digital risk protection to secure Indonesian enterprises.'
  UNION ALL SELECT 'Mujoko', 'Partner Solutions Architect, Datadog', 'Datadog',
         'Mujoko, Partner Solution Architect at Datadog, has 17+ years architecting enterprise solutions across telco, cloud, and observability domains.'
  UNION ALL SELECT 'Daniel Wardhana', 'Senior Customer Success Engineer, Dynatrace', 'Dynatrace',
         'Daniel Wardhana is a Senior Customer Success Engineer at Dynatrace, helping leading financial institutions across Southeast Asia improve observability, digital resilience, operational performance, and incident management.'
  UNION ALL SELECT 'Muhamad Haiqal', 'Senior Sales Engineer, EnterpriseDB', 'EnterpriseDB',
         'Haiqal is an experienced technology and data enthusiast with over 14 years of experience, currently specializing in the dynamic field of Data and AI area, especially on Data Management and Sustainability solutions portfolio. He has proven track record in various facets of data management, including data integration, data quality, and data governance. Passionate about leveraging cutting-edge technologies to drive innovation and optimize business processes. A results-driven professional dedicated to staying at the forefront of the evolving data landscape.'
  UNION ALL SELECT 'Erwin Darmawan', 'Security Consultant, Fortinet', 'Fortinet',
         'Erwin Darmawan as Security Consultant from Fortinet Indonesia has been a cybersecurity professional with 16 years of experience since graduating from Institut Teknologi Indonesia. He started his career as an System Engineer, then shifted to becoming a consultant in order to garner more experience in various industry verticals, and finally joined Fortinet in 2025 as a System engineer. He also holds several vendor certificates such as Cisco, F5 and Fortinet'
  UNION ALL SELECT 'Dedy Setiawan', 'Customer Solutions Consultant', 'Google Cloud Platform',
         'As a Customer Solutions Consultant for Google Cloud in Jakarta, Indonesia, Dedy Setiawan brings over 20 years of experience as an accomplished IT professional. He is passionate about leveraging Google Cloud for seamless and effective Infrastructure Modernization to help organizations achieve their business objectives.'
  UNION ALL SELECT 'Cliff Tangel', 'Customer Solutions Consultant', 'Google Cloud Platform',
         'Cliff Tangel is a Customer Solutions Consultant at Google Cloud with a proven track record of designing and automating cloud-native infrastructure. Based in Jakarta, Cliff leverages his deep technical roots in banking enterprise infrastructure having previously worked as an IT Specialist at BCA and an Automation Engineer at DBS Bank to help organizations modernize on Google Cloud Platform (GCP). He is a proud alumnus of the elite Bangkit Academy program and specializes in cloud architecture, application development, and technical automation.'
  UNION ALL SELECT 'Christopher Yonathan', 'Senior Tech Consultant, HP Indonesia', 'HP Indonesia',
         'Christopher Yonathan is a Senior Tech Consultant at HP Indonesia with 8 years'' experience and a master''s in cyber security from University of Warwick, UK, focused on enterprise security solutions.'
  UNION ALL SELECT 'Pradita Chandra Kurniawan', 'Technology Architect Compute Presales, HPE', 'HPE',
         'HPE Indonesia''s Compute Ambassador, driving enterprise digital transformation through next-generation server architectures, hybrid cloud solutions, and scalable operational AI infrastructure.'
  UNION ALL SELECT 'Dwina Agustin Putri', 'Solution Architect Huawei Cloud Indonesia', 'Huawei Cloud Indonesia',
         'Dwina Agustin Putri has experience in AI infrastructure, cloud architecture, and enterprise AI solutions, supporting government, carrier, and enterprise organizations in accelerating AI adoption and digital transformation'
  UNION ALL SELECT 'Hendra Tanto', 'Principal Solution Engineer for Hashicorp, IBM Indonesia', 'IBM',
         'to be confirm'
  UNION ALL SELECT 'Akhmad Makki', 'Integration Solution Strategist IBM Asean', 'IBM',
         'to be confirm'
  UNION ALL SELECT 'Degui Xu', 'Director of Solutions Engineering, Kong', 'Kong',
         'Degui Xu is a domain expert in the API space, including API management, integration and other middleware solutions. He has worked closely with customers and partners across the Asia Pacific region to drive their digital transformation journey leveraging Kong''s technologies. Prior to Kong, Degui held architect roles at companies such as Pivotal, Mulesoft and Red Hat Asia Pacific. He holds a Bachelor''s Degree in Computer Science from National University of Singapore.'
  UNION ALL SELECT 'Dimas Susilo', 'Lark APAC Solution Engineer', 'Lark',
         'Dimas Susilo is a Solution Engineer at Lark, specialising in digital transformation, workflow redesign, and AI-powered operations.TBC'
  UNION ALL SELECT 'Adelia Chindranata', 'Copilot Solution Engineer, Microsoft Indonesia', 'Microsoft',
         'Adelia Chindranata is a Copilot and Agents Solution Engineer at Microsoft Indonesia, leading technical solutioning and adoption for Microsoft 365 Copilot and agentic AI across Indonesia''s largest enterprises. A frequent speaker at Microsoft and partner forums across ASEAN, she makes complex AI architecture practical, secure, and usable.'
  UNION ALL SELECT 'Syamil Fakhruddin', 'Technical Consultant Manager, at PT Mitra Integrasi Informatika (Metrodata Group)', 'Microsoft',
         'TBC'
  UNION ALL SELECT 'Suwandi Ongko', 'Sr. Architect, Palo Alto Networks, ASEAN', 'Palo Alto Networks',
         'TBC'
  UNION ALL SELECT 'Slamet Firmansyah', 'Sr. Domain Consultant, Palo Alto Networks, Indonesia', 'Palo Alto Networks',
         'TBC'
  UNION ALL SELECT 'Yeo Zhen Hong', 'Senior Channels Sales Engineer, Proofpoint', 'Proofpoint',
         'Zhen Hong is a cybersecurity professional with over 16 years of experience in enterprise security, data protection, email security, and solution engineering. Throughout his career, he has partnered with leading cybersecurity vendors to help organizations across the Asia-Pacific region strengthen cyber resilience through strategic security initiatives, trusted advisory, and innovative technologies.'
  UNION ALL SELECT 'Rakhmad Azhari', 'Sr. Specialist Solution Architect', 'Red Hat',
         'Rakhmad is a Senior Specialist Solution Architect at Red Hat Indonesia with over 15 years of experience in enterprise technology. He specializes in digital transformation for financial institutions and large enterprises, with deep expertise in cloud-native application development, application modernization, and hybrid cloud AI infrastructure. A passionate advocate for open-source innovation, he stays at the forefront of AI infrastructure trends, including large-language-model serving and intelligent data architectures. Rakhmad brings a seasoned practitioner''s perspective to conversations about responsible and effective AI adoption at scale in regulated industries.'
  UNION ALL SELECT 'Christian Suryanto', 'Senior Specialist Solution Architect', 'Red Hat',
         'Christian serves as Senior Specialist Solution Architect for OpenShift at Red Hat, helping enterprises navigate container platform adoption, modern virtualization alternatives, and AI-ready infrastructure. Coming from over a decade in enterprise infrastructure across Indonesia''s banking and public sectors, he bridges deep technical expertise with the business outcomes leadership actually cares about.'
  UNION ALL SELECT 'Raditya Putra', 'Service Cloud AE', 'Salesforce',
         'Raditya Putra, Salesforce Service Cloud Account Executive, helps enterprises modernize contact centers and IT service operations on one platform. He''ll showcase how Service Cloud''s CCaaS and ITSM capabilities unify service delivery for greater efficiency and impact.'
  UNION ALL SELECT 'Juneidi Tsai', 'Strategic Solution Engineer', 'Salesforce',
         ''
  UNION ALL SELECT 'Lukman Hakim', 'Regional Sales Executive Data & AI Salesforce', 'Salesforce',
         'Specialized in digital transformation, Lukman Hakim, Salesforce Regional Sales Executive Data & AI, strives to drive adoption of Agentic AI and cloud technologies to unify data, accelerate growth, and unlock enterprise value across diverse industries.'
  UNION ALL SELECT 'Novan Tambunan', 'Security Engineer, Tenable', 'Tenable',
         'Novan Tambunan is a Security Engineer at Tenable, bringing extensive experience in securing enterprise and government environments. Prior to joining Tenable, he held key security engineering roles at McAfee and Symantec, where he contributed to large-scale cybersecurity initiatives.'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `speakers` s WHERE s.name = tmp.name);

-- ---------------------------------------------------------------------
-- 2) TRACK SESSIONS — insert only the ones that don't exist yet
--    (matched by existing vendor track + title + topic_headline)
-- ---------------------------------------------------------------------
INSERT INTO `agenda_items`
  (`title`, `topic_headline`, `description`, `key_highlights`, `category`,
   `agenda_type`, `workshop_id`, `track_id`, `room`, `start_time`, `end_time`,
   `date`, `order`, `rowspan`, `colspan`, `is_registrable`, `capacity`,
   `feedback_enabled`, `registration_open`, `created_at`, `updated_at`)
SELECT tmp.title, tmp.topic_headline, tmp.description, NULL, 'track',
       'track', NULL, t.id, NULL, '00:00:00', '00:00:00',
       NULL, 0, 1, 1, 0, 0, 0, 1, NOW(), NOW()
FROM (
  SELECT 'Anaplan' AS track_name, 'Transforming Trade Promotion Planning with Anaplan AI' AS title, 'Planning + AI' AS topic_headline,
         'Live demo of Anaplan Trade Promotion and AI-Powered Model Building' AS description
  UNION ALL SELECT 'Anaplan', 'Modern Financial Consolidation & Reporting with AI', 'Planning + AI',
         'Learn how modern tools simplify financial consolidation and enable faster, more accurate reporting'
  UNION ALL SELECT 'AWS', 'The AI-Powered Enterprise: Building Faster & Work Smarter with Kiro and Amazon Quick', 'AWS IN AI ERA',
         'See how Kiro''s spec-driven AI development and Amazon Quick''s intelligent work companion turn ideas into shipped application and smarter workflows. A live look at agentic coding and AI-powered productivity — built for real enterprise teams'
  UNION ALL SELECT 'AWS', 'Business-Ready AI Architecture : Leveraging Amazon Bedrock and Agent-Core for Competitive Advantage in 2026', 'AWS IN AI ERA',
         'Aligning Amazon Bedrock and AgentCore implementation with the upcoming macroeconomic landscape. This presentation demonstrates how AI agents can mitigate business risks, accelerate executive decision-making, and unlock new revenue streams through infrastructure that is built to last'
  UNION ALL SELECT 'Byteplus', 'From Agents to Creative Intelligence: BytePlus ArkClaw & Lumina', 'Cloud and AI',
         'Enhance your business efficiency and drive innovation. This session explores the capabilities of ArkClaw AI Agents in process automation, along with the power of Lumina Generative AI to create impactful, high-quality content that delivers real business value.'
  UNION ALL SELECT 'Cloudera', 'Cloudera Data Lineage : Transforming Data Chaos into Clarity', 'Hybrid IT Infrastructure',
         'Cloudera Data Lineage (Octopai) maps data journeys across systems, revealing origins, transformations, and dependencies with precision. By automating lineage detection and visualization, it cuts through data chaos, enabling teams to trust and understand their data. The result: faster troubleshooting, better governance, and crystal-clear insight into how data flows across the enterprise'
  UNION ALL SELECT 'Cloudera', 'Building Agentic AI Applications with Cloudera AI', 'Data & AI',
         'Discover how to build intelligent, autonomous agents with Cloudera AI''s unified platform. From data orchestration to real-time decision-making, Cloudera empowers developers as well as business users to create scalable, secure, and compliant agentic AI applications using high code as well as low code tools. Accelerate AI innovation using trusted enterprise-grade tools from development to production designed to support adaptive, context-aware systems across on premises, multi-cloud or even hybrid environments.'
  UNION ALL SELECT 'Confluent', 'Real-Time Context Engine in Confluent Cloud', 'Data & AI',
         'In this session, discover how Confluent Cloud acts as a Real-Time Context Engine, seamlessly connecting distributed enterprise data silos to deliver instantly updated, high-fidelity data streams. Learn how to continuously feed your AI models, applications, and analytics engines with the freshest operational data, enabling smarter automated decision-making and hyper-personalized customer experiences at scale.'
  UNION ALL SELECT 'Cyble', 'Real-Time Cyber Defense: Protecting Critical Enterprises at Every Layer of the Attack Surface via AI-Driven Intelligence.', 'Cybersecurity',
         'In an era where cyber threats evolve at hypersonic speed, traditional perimeter security is no longer enough to protect high-value assets. This session explores how Cyble''s AI-Native Security Cloud powers real-time, AI-driven decisions for the world''s most critical governments and enterprises. Discover how to eliminate blind spots and secure your entire digital infrastructure against sophisticated risks at every single layer of your external attack surface.'
  UNION ALL SELECT 'Datadog', 'Winning with AI: From Alert Overload to Automated Investigation', 'Data & AI',
         'As on-call teams drown in alerts and manual triage, AI is reshaping incident response, from reactive troubleshooting to autonomous investigation. This session explores how AI-driven root cause analysis is cutting toil, speeding recovery, and helping engineering teams scale reliable operations with measurable business impact.'
  UNION ALL SELECT 'Dynatrace', 'From Monitoring to Operational Intelligence: Enhancing Digital Resilience with Observability and AI', 'AI Workloads & Modern Cloud Observability',
         'Learn how modern organizations use AI-powered observability to improve service reliability, accelerate incident resolution, and gain actionable insights across complex digital environments. Discover practical strategies to enhance operational efficiency, customer experience, and business resilience.'
  UNION ALL SELECT 'EDB Postgre', 'Postgres as Your AI Data Platform: Architecture, Vectors, and What to Watch Out For', 'Data & AI',
         'We cover the architecture decisions that matter most, from data sovereignty and latency trade-offs to the operational challenges of running AI workloads alongside transactional ones. Leave with a clear picture of where Postgres fits in the modern AI stack.'
  UNION ALL SELECT 'Fortinet', 'AI Driven CyberSecurity', 'Cybersecurity',
         'As cyber threats become more sophisticated, organizations can no longer rely solely on traditional security approaches. Artificial Intelligence (AI) is transforming cybersecurity by enabling faster threat detection, automated response, predictive analytics, and continuous risk assessment. AI-driven cybersecurity leverages machine learning, behavioral analysis, and real-time data processing to identify anomalies, detect advanced attacks, and reduce response times across complex IT environments.'
  UNION ALL SELECT 'Google Cloud', 'Fueling the Future: Elevating Customer Experience with Data and AI', 'Data & AI',
         'In today''s hyper-connected marketplace, traditional customer service is no longer enough. Modern consumers demand instant, deeply personalized, and frictionless interactions. To deliver this at scale, organizations must shift from reactive support to proactive engagement'
  UNION ALL SELECT 'Google Cloud', 'Peeling the layers: Agentic Development - Learnings & Best Practices from Google Cloud', 'Data & AI',
         'Discover Google Cloud''s latest updates in enterprise agentic systems, alongside implementations experience & lessons learned in the rapidly evolving space.'
  UNION ALL SELECT 'HP Inc', 'Accelerating AI Value, Securely: Inside HP Wolf Security''s Full-Stack Defense', 'Cybersecurity',
         'As organizations build, run, and scale AI to drive real business impact, security can''t be an afterthought. This session unpacks HP Wolf Security''s full-stack approach: hardware-enforced isolation, self-healing firmware, and AI-driven threat detection; showing how security becomes a foundation for measurable, trustworthy AI-powered operations.'
  UNION ALL SELECT 'HPE', 'Accelerating Sovereign Enterprise AI: Scaling On-Premise Private Cloud AI from Pilot to Production', 'Hybrid IT Infrastructure',
         'HPE Private Cloud AI, co-engineered with NVIDIA, delivers a turnkey "AI in a box" solution. Discover how to accelerate time-to-value, ensure data sovereignty, and seamlessly scale enterprise AI workloads from pilot to production'
  UNION ALL SELECT 'Huawei Cloud', 'Building for the Agentic AI Era: Infrastructure, Models, and Practical Use Cases', 'AI Infrastructure',
         'Discover the essential building blocks of the Agentic AI era, from AI infrastructure and Model-as-a-Service to AI-assisted software development. Learn how these technologies enable scalable AI applications, streamline development, and unlock practical enterprise use cases across industries'
  UNION ALL SELECT 'IBM', 'Securing the Rise of Non-Human Identities in the AI Era', 'Cybersecurity',
         'Seiring adopsi AI, cloud, dan otomatisasi, jumlah Non-Human Identities kini mencapai 50 kali lebih banyak dibanding identitas manusia. Sesi ini membahas tantangan dan strategi mengamankan identitas mesin untuk memperkuat keamanan enterprise di era AI.'
  UNION ALL SELECT 'IBM', 'Extending Your Integration Platform with AI Gateway', 'Automation',
         'Integrasi enterprise kini berkembang, tidak lagi sekadar menghubungkan aplikasi dan API, tetapi juga mengorkestrasi layanan berbasis AI. Sesi ini membahas bagaimana AI Gateway dapat memperluas platform webMethods dan IBM Integration yang sudah ada dengan fitur keamanan, tata kelola, dan observabilitas khusus AI—tanpa mengganggu arsitektur Anda saat ini.'
  UNION ALL SELECT 'KONG', 'From Prototype to Production: Securing, Scaling, and Governing Enterprise AI with Kong', 'Data & AI',
         NULL
  UNION ALL SELECT 'Lark', 'Closing AI Value Gap - Becoming AI-first organization', 'Data & AI',
         'Closing the AI Value Gap Despite growing AI investments, many organisations struggle to realise measurable returns. Discover how leading enterprises are closing the AI Value Gap by redesigning workflows, connecting operations, and enabling human-AI collaboration to turn AI into real business impact.'
  UNION ALL SELECT 'Microsoft', 'An AI Built for You', 'Data & AI',
         'Step into the future of work—where AI doesn''t just assist, it acts. Discover how Work IQ, multimodal AI, and agentic systems transform everyday tasks into seamless outcomes, powered by a secure, unified platform and Copilot Cowork—unlocking a new era of human and AI teaming at scale'
  UNION ALL SELECT 'Microsoft', 'From AI Vision to Action with Microsoft AI', 'Data & AI',
         'Session to provide a clear understanding of Microsoft AI, its positioning within the Microsoft AI ecosystem, and its enterprise value through real customer stories that demonstrate how organizations across different industries turn AI strategy into measurable business outcomes.'
  UNION ALL SELECT 'Palo Alto', 'The Resilient AI Enterprise: Secure AI by Design for Competitive Edge', 'Cybersecurity',
         'Unlock the power of the AI revolution while mitigating its risks. Discover how to secure your enterprise from AI-driven threats, govern internal AI agents, and build resilient, autonomous security operations. Join us to learn how to innovate responsibly and maintain a competitive edge in the era of AI.'
  UNION ALL SELECT 'Palo Alto', 'Enter a New Era of Security Platform : Autonomous SOC AI-Driven Platform to Stop Tomorrow''s Threat in AI Era', 'Cybersecurity',
         'The cybersecurity landscape is rapidly evolving, presenting organizations with unprecedented challenges. Today''s threat actors are more sophisticated, using advanced techniques and AI to bypass traditional security measures. As a security professional, you''re likely experiencing firsthand how the needs of your security operations center (SOC) have changed dramatically. The old ways of detecting and responding to threats are no longer sufficient in an era where breaches can occur in a matter of hours —down from 24 hours just a year ago— and regulatory requirements are becoming increasingly stringent.'
  UNION ALL SELECT 'Proofpoint', 'The Evolution of Insider Risk, and What The Future Holds', 'Cybersecurity',
         'Explore how AI, automation, and agentic workflows are transforming insider risk into a trusted authority challenge. Learn why organizations must evolve from reactive investigations to proactive, behavior-driven programs by correlating intent, identity, data access, and AI governance to reduce business risk before incidents occur.'
  UNION ALL SELECT 'Red Hat', 'From Pilot to Production: Closing the AI Platform Gap', 'Hybrid IT Infrastructure',
         'Moving from pilot to production requires shifting from fragmented, insecure tools to a unified, governed platform that standardizes security policies for all agents and models. The market has evolved from simple human-in-the-loop models to autonomous AI agents that require robust security frameworks to operate safely. To mitigate risks like agent misbehavior, supply chain attacks, and data leakage, Red Hat implements security-in-depth across the AI stack: - Isolation: Agent execution sandboxes are used to isolate blast radiuses and contain potential issues. - Governance: Automated vulnerability scanning for agents and models, combined with AI guardrails to filter problematic inputs and outputs. - Zero-Trust Identity: Implementation of agent identity zero-trust to ensure authenticity and prevent unauthorized actions. - Auditability: End-to-end tracing and observability are integrated to provide clear audit trails for AI actions. Red Hat provides a unified platform to manage the complete lifecycle of AI agents, including: Centralized management of Agent lifecycle, prompts, and Model Context Protocol (MCP).'
  UNION ALL SELECT 'Red Hat', 'Sovereign AI in Practice: From Model to Deployment', 'Data & AI',
         'Many organizations want to adopt AI without losing control — over their data, their models, or their infrastructure. This session looks at what it actually takes to turn a raw model into a working, sovereign AI deployment: moving from an isolated proof of concept to something the rest of the organization can actually rely on, and knowing when it''s worth building versus leveraging proven, pre-integrated solutions for common use cases. Along the way, we''ll unpack what "sovereign" really means in practice — open source foundations, no vendor lock-in, and full control over where your models and data live — with real examples on the Red Hat AI stack.'
  UNION ALL SELECT 'Salesforce', 'Transforming Customer & IT Service with Salesforce Service Cloud: CCaaS and ITSM in the Age of AI', 'Business Application',
         'This session explores how Salesforce Service Cloud empowers organizations to modernize both their contact center and IT service operations on a single, unified platform. We''ll dive into how Service Cloud''s CCaaS capabilities — including omni-channel routing, voice integration, and real-time analytics — work alongside ITSM workflows such as incident and change management to streamline service delivery across the enterprise.'
  UNION ALL SELECT 'Salesforce', 'Pay-As-You-Execute: How to Leverage Agentic AI and Message Credits for Maximum Business Benefit', 'Business Application',
         'For years, enterprise software relied on predictable, per-user subscription models. Today, we are abruptly entering the Consumables Era. As infrastructure shifts from humans storing data to autonomous agents executing workflows, the core unit of business cost has changed. We are now paying for execution: LLM API tokens, adaptive compute budgets, and WhatsApp messaging credits. In this session, we will break down how to architect your systems for this new paradigm. We will explore how Agentic AI natively utilizes consumable resources to execute complex, multi-step tasks—such as automated customer operations over WhatsApp—and how enterprise leaders can strategically optimize these consumption patterns to turn variable execution costs into a massive bottom-line advantage.'
  UNION ALL SELECT 'Tenable', 'From Visibility to Velocity: CTEM as the New Cyber Risk Mandate', 'AI-Driven Cyberscurity',
         'What board-level accountability looks like in an AI-driven threat landscape & how to shift from vulnerability counting to exposure command.'
) AS tmp
JOIN `tracks` t ON t.name = tmp.track_name
WHERE NOT EXISTS (
  SELECT 1 FROM `agenda_items` ai
  WHERE ai.track_id = t.id
    AND ai.title = tmp.title
    AND ai.topic_headline = tmp.topic_headline
);

-- ---------------------------------------------------------------------
-- 3) LINK speakers <-> sessions (only if the link is not already there)
--    Agenda item is matched back by (title + topic_headline + track),
--    speaker is matched by name — so existing rows are reused safely.
-- ---------------------------------------------------------------------
INSERT INTO `agenda_item_speaker`
  (`agenda_item_id`, `speaker_id`, `order`, `key_highlights`, `presentation_title`,
   `presentation_description`, `created_at`, `updated_at`)
SELECT ai.id, s.id, tmp.ord, NULL, NULL, NULL, NOW(), NOW()
FROM (
  SELECT 'Sandra Agustina' AS speaker_name, 'Transforming Trade Promotion Planning with Anaplan AI' AS session_title, 'Planning + AI' AS session_topic, 1 AS ord
  UNION ALL SELECT 'Magdalena Cynthia', 'Transforming Trade Promotion Planning with Anaplan AI', 'Planning + AI', 2
  UNION ALL SELECT 'Nicolaus Bintang Ardana', 'Modern Financial Consolidation & Reporting with AI', 'Planning + AI', 1
  UNION ALL SELECT 'Hinwoto Jun', 'The AI-Powered Enterprise: Building Faster & Work Smarter with Kiro and Amazon Quick', 'AWS IN AI ERA', 1
  UNION ALL SELECT 'NUR MUHAMMAD ZAKI BUDIPRASETYA', 'The AI-Powered Enterprise: Building Faster & Work Smarter with Kiro and Amazon Quick', 'AWS IN AI ERA', 2
  UNION ALL SELECT 'Glen Johanes', 'Business-Ready AI Architecture : Leveraging Amazon Bedrock and Agent-Core for Competitive Advantage in 2026', 'AWS IN AI ERA', 1
  UNION ALL SELECT 'ZEIN RASYID HIMAMI', 'Business-Ready AI Architecture : Leveraging Amazon Bedrock and Agent-Core for Competitive Advantage in 2026', 'AWS IN AI ERA', 2
  UNION ALL SELECT 'Renaldi Azhar', 'From Agents to Creative Intelligence: BytePlus ArkClaw & Lumina', 'Cloud and AI', 1
  UNION ALL SELECT 'Eduard Bangga', 'Cloudera Data Lineage : Transforming Data Chaos into Clarity', 'Hybrid IT Infrastructure', 1
  UNION ALL SELECT 'Triano Nurhikmat', 'Building Agentic AI Applications with Cloudera AI', 'Data & AI', 1
  UNION ALL SELECT 'Engin Cukuroglu', 'Real-Time Context Engine in Confluent Cloud', 'Data & AI', 1
  UNION ALL SELECT 'Caroline Parapat', 'Real-Time Cyber Defense: Protecting Critical Enterprises at Every Layer of the Attack Surface via AI-Driven Intelligence.', 'Cybersecurity', 1
  UNION ALL SELECT 'Mujoko', 'Winning with AI: From Alert Overload to Automated Investigation', 'Data & AI', 1
  UNION ALL SELECT 'Daniel Wardhana', 'From Monitoring to Operational Intelligence: Enhancing Digital Resilience with Observability and AI', 'AI Workloads & Modern Cloud Observability', 1
  UNION ALL SELECT 'Muhamad Haiqal', 'Postgres as Your AI Data Platform: Architecture, Vectors, and What to Watch Out For', 'Data & AI', 1
  UNION ALL SELECT 'Erwin Darmawan', 'AI Driven CyberSecurity', 'Cybersecurity', 1
  UNION ALL SELECT 'Dedy Setiawan', 'Fueling the Future: Elevating Customer Experience with Data and AI', 'Data & AI', 1
  UNION ALL SELECT 'Cliff Tangel', 'Peeling the layers: Agentic Development - Learnings & Best Practices from Google Cloud', 'Data & AI', 1
  UNION ALL SELECT 'Christopher Yonathan', 'Accelerating AI Value, Securely: Inside HP Wolf Security''s Full-Stack Defense', 'Cybersecurity', 1
  UNION ALL SELECT 'Pradita Chandra Kurniawan', 'Accelerating Sovereign Enterprise AI: Scaling On-Premise Private Cloud AI from Pilot to Production', 'Hybrid IT Infrastructure', 1
  UNION ALL SELECT 'Dwina Agustin Putri', 'Building for the Agentic AI Era: Infrastructure, Models, and Practical Use Cases', 'AI Infrastructure', 1
  UNION ALL SELECT 'Hendra Tanto', 'Securing the Rise of Non-Human Identities in the AI Era', 'Cybersecurity', 1
  UNION ALL SELECT 'Akhmad Makki', 'Extending Your Integration Platform with AI Gateway', 'Automation', 1
  UNION ALL SELECT 'Degui Xu', 'From Prototype to Production: Securing, Scaling, and Governing Enterprise AI with Kong', 'Data & AI', 1
  UNION ALL SELECT 'Dimas Susilo', 'Closing AI Value Gap - Becoming AI-first organization', 'Data & AI', 1
  UNION ALL SELECT 'Adelia Chindranata', 'An AI Built for You', 'Data & AI', 1
  UNION ALL SELECT 'Syamil Fakhruddin', 'From AI Vision to Action with Microsoft AI', 'Data & AI', 1
  UNION ALL SELECT 'Suwandi Ongko', 'The Resilient AI Enterprise: Secure AI by Design for Competitive Edge', 'Cybersecurity', 1
  UNION ALL SELECT 'Slamet Firmansyah', 'Enter a New Era of Security Platform : Autonomous SOC AI-Driven Platform to Stop Tomorrow''s Threat in AI Era', 'Cybersecurity', 1
  UNION ALL SELECT 'Yeo Zhen Hong', 'The Evolution of Insider Risk, and What The Future Holds', 'Cybersecurity', 1
  UNION ALL SELECT 'Rakhmad Azhari', 'From Pilot to Production: Closing the AI Platform Gap', 'Hybrid IT Infrastructure', 1
  UNION ALL SELECT 'Christian Suryanto', 'Sovereign AI in Practice: From Model to Deployment', 'Data & AI', 1
  UNION ALL SELECT 'Raditya Putra', 'Transforming Customer & IT Service with Salesforce Service Cloud: CCaaS and ITSM in the Age of AI', 'Business Application', 1
  UNION ALL SELECT 'Juneidi Tsai', 'Transforming Customer & IT Service with Salesforce Service Cloud: CCaaS and ITSM in the Age of AI', 'Business Application', 2
  UNION ALL SELECT 'Lukman Hakim', 'Pay-As-You-Execute: How to Leverage Agentic AI and Message Credits for Maximum Business Benefit', 'Business Application', 1
  UNION ALL SELECT 'Novan Tambunan', 'From Visibility to Velocity: CTEM as the New Cyber Risk Mandate', 'AI-Driven Cyberscurity', 1
) AS tmp
JOIN `speakers` s ON s.name = tmp.speaker_name
JOIN `agenda_items` ai ON ai.title = tmp.session_title
                      AND ai.topic_headline = tmp.session_topic
                      AND ai.category = 'track'
WHERE NOT EXISTS (
  SELECT 1 FROM `agenda_item_speaker` a
  WHERE a.agenda_item_id = ai.id AND a.speaker_id = s.id
);

-- ---------------------------------------------------------------------
-- 4) Summary
-- ---------------------------------------------------------------------
SELECT 'speakers' AS `table`, COUNT(*) AS total FROM `speakers`
UNION ALL SELECT 'agenda_items (track)', COUNT(*) FROM `agenda_items` WHERE category = 'track'
UNION ALL SELECT 'agenda_item_speaker links', COUNT(*) FROM `agenda_item_speaker`;
