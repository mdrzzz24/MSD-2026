<?php

namespace Database\Seeders;

use App\Models\AgendaItem;
use App\Models\Speaker;
use App\Models\Track;
use Illuminate\Database\Seeder;

/**
 * Seeds the 32 TRACK SESSIONS + their speakers (linked to existing vendor tracks).
 *
 * IDEMPOTENT & NON-DESTRUCTIVE — safe to run on any database:
 *   - Tracks:   matched by `name` (Anaplan, AWS, Byteplus, ...); NOT created here — the
 *               vendor tracks must already exist (they do: ids 38-60). Missing → warned + skipped.
 *   - Speakers: `updateOrCreate` by name (fills/refreshes title/company/bio; never deletes).
 *   - Sessions: `firstOrCreate` by (track_id + title + topic_headline + category='track') — only created if missing.
 *   - Pivot:    `syncWithoutDetaching` — only adds missing speaker links.
 * Existing data (registrants, workshops, other sessions) is untouched.
 *
 * Field mapping (user-confirmed):
 *   agenda_items.title          = Session Title
 *   agenda_items.topic_headline = Topic Headline
 *   agenda_items.description    = Session description
 *   agenda_items.category='track', agenda_type='track', track_id = existing vendor track
 *
 * Run:  php artisan db:seed --class=TrackSessionSeeder
 */
class TrackSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            // ── Anaplan ──
            [
                'track' => 'Anaplan', 'vendor' => 'Anaplan',
                'title' => 'Transforming Trade Promotion Planning with Anaplan AI',
                'topic_headline' => 'Planning + AI',
                'description' => 'Live demo of Anaplan Trade Promotion and AI-Powered Model Building',
                'speakers' => [
                    ['name' => 'Sandra Agustina', 'title' => 'Principal Solution Consultant, Anaplan', 'bio' => "Sandra Agustina currently holds the esteemed position of Principal Solutions Architect at Anaplan, utilizing her expertise to advance business enhancement through the development of comprehensive solutions tailored to address diverse customer needs. With a wealth of experience in the realm of technology solutions, Sandra adeptly leverages her knowledge to drive substantial improvements in planning and budgeting for a wide range of clients. Her unwavering dedication to continuous learning and her exceptional communication skills render her an invaluable asset to any technology-driven endeavor"],
                    ['name' => 'Magdalena Cynthia', 'title' => 'Presales Support Manager PT Metrodata Electronics Tbk', 'bio' => "Magdalena Cynthia is an experienced and highly dynamic professional with a strong background in Supply Chain Management within the pharmaceuticals and tobacco industries. In 2023, she joined Metrodata as an Anaplan Consultant, bringing 2 years of experience in Anaplan consulting and implementation, specializing in Consolidated Financial Reporting and Management Reporting. Currently, Cynthia works as a Presales Consultant, delivering Anaplan's value proposition to help customers enhance their business agility. In this role, she collaborates closely with sales teams and principals to identify customer needs and tailor solutions that address each client's unique business challenges."],
                ],
            ],
            [
                'track' => 'Anaplan', 'vendor' => 'Anaplan',
                'title' => 'Modern Financial Consolidation & Reporting with AI',
                'topic_headline' => 'Planning + AI',
                'description' => 'Learn how modern tools simplify financial consolidation and enable faster, more accurate reporting',
                'speakers' => [
                    ['name' => 'Nicolaus Bintang Ardana', 'title' => 'Presales Anaplan PT Metrodata Electronics Tbk', 'bio' => "Nicolaus Bintang Ardana, or simply Bintang, has been with Metrodata for the past three years. During his first two years, he worked as an Anaplan Implementation Consultant, supporting projects for Pertamina Hulu Energi across budgeting, financial consolidation, and management reporting. He currently serves as a Presales Support, focusing on Anaplan Financial Consolidation and Reporting (FCR) to help organizations modernize and streamline their financial consolidation processes"],
                ],
            ],
            // ── AWS ──
            [
                'track' => 'AWS', 'vendor' => 'AWS (AMAZON WEB SERVICES)',
                'title' => 'The AI-Powered Enterprise: Building Faster & Work Smarter with Kiro and Amazon Quick',
                'topic_headline' => 'AWS IN AI ERA',
                'description' => "See how Kiro's spec-driven AI development and Amazon Quick's intelligent work companion turn ideas into shipped application and smarter workflows. A live look at agentic coding and AI-powered productivity — built for real enterprise teams",
                'speakers' => [
                    ['name' => 'Hinwoto Jun', 'title' => 'Senior Partner Solution Architect', 'bio' => 'a Senior Solutions Architect at AWS Indonesia, helping enterprises and partners build, migrate, and modernize on the cloud with AI-powered solutions like Amazon Bedrock, Amazon Quick and Kiro'],
                    ['name' => 'NUR MUHAMMAD ZAKI BUDIPRASETYA', 'title' => 'AWS Solutions Architect for Enterprise Customer', 'bio' => 'A Cloud Solutions Architect with over eight years of experience as a .NET developer, leading teams, executing massive cloud migrations, optimizing costs, and bridging business needs with diverse AWS solutions'],
                ],
            ],
            [
                'track' => 'AWS', 'vendor' => 'AWS (AMAZON WEB SERVICES)',
                'title' => 'Business-Ready AI Architecture : Leveraging Amazon Bedrock and Agent-Core for Competitive Advantage in 2026',
                'topic_headline' => 'AWS IN AI ERA',
                'description' => 'Aligning Amazon Bedrock and AgentCore implementation with the upcoming macroeconomic landscape. This presentation demonstrates how AI agents can mitigate business risks, accelerate executive decision-making, and unlock new revenue streams through infrastructure that is built to last',
                'speakers' => [
                    ['name' => 'Glen Johanes', 'title' => 'Senior AWS Solutions Architect', 'bio' => 'TBC'],
                    ['name' => 'ZEIN RASYID HIMAMI', 'title' => 'Solutions Architect for AI & Advanced Analytics Solution', 'bio' => 'A talented Data Scientist and AI Engineer who builds highly scalable artificial intelligence systems, consistently focusing on driving meaningful business impact and continuous technological innovation'],
                ],
            ],
            // ── BytePlus ──
            [
                'track' => 'Byteplus', 'vendor' => 'BytePlus',
                'title' => 'From Agents to Creative Intelligence: BytePlus ArkClaw & Lumina',
                'topic_headline' => 'Cloud and AI',
                'description' => 'Enhance your business efficiency and drive innovation. This session explores the capabilities of ArkClaw AI Agents in process automation, along with the power of Lumina Generative AI to create impactful, high-quality content that delivers real business value.',
                'speakers' => [
                    ['name' => 'Renaldi Azhar', 'title' => 'Senior Solution Architect, BytePlus', 'bio' => 'Senior Solution Architect at BytePlus, specializes in designing scalable AI architectures. He empowers enterprises to drive innovation and business growth through advanced AI solutions.'],
                ],
            ],
            // ── Cloudera ──
            [
                'track' => 'Cloudera', 'vendor' => 'Cloudera',
                'title' => 'Cloudera Data Lineage : Transforming Data Chaos into Clarity',
                'topic_headline' => 'Hybrid IT Infrastructure',
                'description' => 'Cloudera Data Lineage (Octopai) maps data journeys across systems, revealing origins, transformations, and dependencies with precision. By automating lineage detection and visualization, it cuts through data chaos, enabling teams to trust and understand their data. The result: faster troubleshooting, better governance, and crystal-clear insight into how data flows across the enterprise',
                'speakers' => [
                    ['name' => 'Eduard Bangga', 'title' => 'Principal Solution Architect', 'bio' => 'Eduard Bangga is an accomplished AI and Cloud Specialist currently modernizing data architecture at Cloudera. He brings over 9 years of experience in data and cloud solutions. Prior to his current role as a Solutions Engineer at Cloudera, Eduard served as the Director, Head of Data & Analytics at Data Labs Analytics, where he led a team to deliver data solutions and drive business performance through Big Data and Data Lakehouse implementations. Eduard is a professional with a strong background in solution architecture, big data, and cloud technologies.'],
                ],
            ],
            [
                'track' => 'Cloudera', 'vendor' => 'Cloudera',
                'title' => 'Building Agentic AI Applications with Cloudera AI',
                'topic_headline' => 'Data & AI',
                'description' => "Discover how to build intelligent, autonomous agents with Cloudera AI's unified platform. From data orchestration to real-time decision-making, Cloudera empowers developers as well as business users to create scalable, secure, and compliant agentic AI applications using high code as well as low code tools. Accelerate AI innovation using trusted enterprise-grade tools from development to production designed to support adaptive, context-aware systems across on premises, multi-cloud or even hybrid environments.",
                'speakers' => [
                    ['name' => 'Triano Nurhikmat', 'title' => 'Solution Engineer', 'bio' => 'TBC'],
                ],
            ],
            // ── Confluent ──
            [
                'track' => 'Confluent', 'vendor' => 'Confluent, an IBM Company',
                'title' => 'Real-Time Context Engine in Confluent Cloud',
                'topic_headline' => 'Data & AI',
                'description' => 'In this session, discover how Confluent Cloud acts as a Real-Time Context Engine, seamlessly connecting distributed enterprise data silos to deliver instantly updated, high-fidelity data streams. Learn how to continuously feed your AI models, applications, and analytics engines with the freshest operational data, enabling smarter automated decision-making and hyper-personalized customer experiences at scale.',
                'speakers' => [
                    ['name' => 'Engin Cukuroglu', 'title' => 'Head of Solutions Engineering Asia, Confluent', 'bio' => 'Engin Cukuroglu is a Solutions Engineering Leader at Confluent with expertise in distributed systems, AI, data science, and scalable analytics, leveraging a PhD in Computational Science and extensive Apache Kafka experience.'],
                ],
            ],
            // ── Cyble ──
            [
                'track' => 'Cyble', 'vendor' => 'Cyble Inc',
                'title' => 'Real-Time Cyber Defense: Protecting Critical Enterprises at Every Layer of the Attack Surface via AI-Driven Intelligence.',
                'topic_headline' => 'Cybersecurity',
                'description' => "In an era where cyber threats evolve at hypersonic speed, traditional perimeter security is no longer enough to protect high-value assets. This session explores how Cyble's AI-Native Security Cloud powers real-time, AI-driven decisions for the world's most critical governments and enterprises. Discover how to eliminate blind spots and secure your entire digital infrastructure against sophisticated risks at every single layer of your external attack surface.",
                'speakers' => [
                    ['name' => 'Caroline Parapat', 'title' => 'Enterprise Sales Manager, Cyble', 'bio' => 'Caroline Parapat is the Enterprise Sales Manager for Indonesia at Cyble. She specializes in delivering AI-powered cyber threat intelligence, dark web monitoring, and digital risk protection to secure Indonesian enterprises.'],
                ],
            ],
            // ── Datadog ──
            [
                'track' => 'Datadog', 'vendor' => 'Datadog',
                'title' => 'Winning with AI: From Alert Overload to Automated Investigation',
                'topic_headline' => 'Data & AI',
                'description' => 'As on-call teams drown in alerts and manual triage, AI is reshaping incident response, from reactive troubleshooting to autonomous investigation. This session explores how AI-driven root cause analysis is cutting toil, speeding recovery, and helping engineering teams scale reliable operations with measurable business impact.',
                'speakers' => [
                    ['name' => 'Mujoko', 'title' => 'Partner Solutions Architect, Datadog', 'bio' => 'Mujoko, Partner Solution Architect at Datadog, has 17+ years architecting enterprise solutions across telco, cloud, and observability domains.'],
                ],
            ],
            // ── Dynatrace ──
            [
                'track' => 'Dynatrace', 'vendor' => 'Dynatrace',
                'title' => 'From Monitoring to Operational Intelligence: Enhancing Digital Resilience with Observability and AI',
                'topic_headline' => 'AI Workloads & Modern Cloud Observability',
                'description' => 'Learn how modern organizations use AI-powered observability to improve service reliability, accelerate incident resolution, and gain actionable insights across complex digital environments. Discover practical strategies to enhance operational efficiency, customer experience, and business resilience.',
                'speakers' => [
                    ['name' => 'Daniel Wardhana', 'title' => 'Senior Customer Success Engineer, Dynatrace', 'bio' => 'Daniel Wardhana is a Senior Customer Success Engineer at Dynatrace, helping leading financial institutions across Southeast Asia improve observability, digital resilience, operational performance, and incident management.'],
                ],
            ],
            // ── EnterpriseDB ──
            [
                'track' => 'EDB Postgre', 'vendor' => 'EnterpriseDB',
                'title' => 'Postgres as Your AI Data Platform: Architecture, Vectors, and What to Watch Out For',
                'topic_headline' => 'Data & AI',
                'description' => 'We cover the architecture decisions that matter most, from data sovereignty and latency trade-offs to the operational challenges of running AI workloads alongside transactional ones. Leave with a clear picture of where Postgres fits in the modern AI stack.',
                'speakers' => [
                    ['name' => 'Muhamad Haiqal', 'title' => 'Senior Sales Engineer, EnterpriseDB', 'bio' => 'Haiqal is an experienced technology and data enthusiast with over 14 years of experience, currently specializing in the dynamic field of Data and AI area, especially on Data Management and Sustainability solutions portfolio. He has proven track record in various facets of data management, including data integration, data quality, and data governance. Passionate about leveraging cutting-edge technologies to drive innovation and optimize business processes. A results-driven professional dedicated to staying at the forefront of the evolving data landscape.'],
                ],
            ],
            // ── Fortinet ──
            [
                'track' => 'Fortinet', 'vendor' => 'Fortinet',
                'title' => 'AI Driven CyberSecurity',
                'topic_headline' => 'Cybersecurity',
                'description' => 'As cyber threats become more sophisticated, organizations can no longer rely solely on traditional security approaches. Artificial Intelligence (AI) is transforming cybersecurity by enabling faster threat detection, automated response, predictive analytics, and continuous risk assessment. AI-driven cybersecurity leverages machine learning, behavioral analysis, and real-time data processing to identify anomalies, detect advanced attacks, and reduce response times across complex IT environments.',
                'speakers' => [
                    ['name' => 'Erwin Darmawan', 'title' => 'Security Consultant, Fortinet', 'bio' => 'Erwin Darmawan as Security Consultant from Fortinet Indonesia has been a cybersecurity professional with 16 years of experience since graduating from Institut Teknologi Indonesia. He started his career as an System Engineer, then shifted to becoming a consultant in order to garner more experience in various industry verticals, and finally joined Fortinet in 2025 as a System engineer. He also holds several vendor certificates such as Cisco, F5 and Fortinet'],
                ],
            ],
            // ── Google Cloud ──
            [
                'track' => 'Google Cloud', 'vendor' => 'Google Cloud Platform',
                'title' => 'Fueling the Future: Elevating Customer Experience with Data and AI',
                'topic_headline' => 'Data & AI',
                'description' => "In today's hyper-connected marketplace, traditional customer service is no longer enough. Modern consumers demand instant, deeply personalized, and frictionless interactions. To deliver this at scale, organizations must shift from reactive support to proactive engagement",
                'speakers' => [
                    ['name' => 'Dedy Setiawan', 'title' => 'Customer Solutions Consultant', 'bio' => 'As a Customer Solutions Consultant for Google Cloud in Jakarta, Indonesia, Dedy Setiawan brings over 20 years of experience as an accomplished IT professional. He is passionate about leveraging Google Cloud for seamless and effective Infrastructure Modernization to help organizations achieve their business objectives.'],
                ],
            ],
            [
                'track' => 'Google Cloud', 'vendor' => 'Google Cloud Platform',
                'title' => 'Peeling the layers: Agentic Development - Learnings & Best Practices from Google Cloud',
                'topic_headline' => 'Data & AI',
                'description' => "Discover Google Cloud's latest updates in enterprise agentic systems, alongside implementations experience & lessons learned in the rapidly evolving space.",
                'speakers' => [
                    ['name' => 'Cliff Tangel', 'title' => 'Customer Solutions Consultant', 'bio' => 'Cliff Tangel is a Customer Solutions Consultant at Google Cloud with a proven track record of designing and automating cloud-native infrastructure. Based in Jakarta, Cliff leverages his deep technical roots in banking enterprise infrastructure having previously worked as an IT Specialist at BCA and an Automation Engineer at DBS Bank to help organizations modernize on Google Cloud Platform (GCP). He is a proud alumnus of the elite Bangkit Academy program and specializes in cloud architecture, application development, and technical automation.'],
                ],
            ],
            // ── HP ──
            [
                'track' => 'HP Inc', 'vendor' => 'HP Indonesia',
                'title' => "Accelerating AI Value, Securely: Inside HP Wolf Security's Full-Stack Defense",
                'topic_headline' => 'Cybersecurity',
                'description' => "As organizations build, run, and scale AI to drive real business impact, security can't be an afterthought. This session unpacks HP Wolf Security's full-stack approach: hardware-enforced isolation, self-healing firmware, and AI-driven threat detection; showing how security becomes a foundation for measurable, trustworthy AI-powered operations.",
                'speakers' => [
                    ['name' => 'Christopher Yonathan', 'title' => 'Senior Tech Consultant, HP Indonesia', 'bio' => "Christopher Yonathan is a Senior Tech Consultant at HP Indonesia with 8 years' experience and a master's in cyber security from University of Warwick, UK, focused on enterprise security solutions."],
                ],
            ],
            // ── HPE ──
            [
                'track' => 'HPE', 'vendor' => 'HPE',
                'title' => 'Accelerating Sovereign Enterprise AI: Scaling On-Premise Private Cloud AI from Pilot to Production',
                'topic_headline' => 'Hybrid IT Infrastructure',
                'description' => 'HPE Private Cloud AI, co-engineered with NVIDIA, delivers a turnkey "AI in a box" solution. Discover how to accelerate time-to-value, ensure data sovereignty, and seamlessly scale enterprise AI workloads from pilot to production',
                'speakers' => [
                    ['name' => 'Pradita Chandra Kurniawan', 'title' => 'Technology Architect Compute Presales, HPE', 'bio' => "HPE Indonesia's Compute Ambassador, driving enterprise digital transformation through next-generation server architectures, hybrid cloud solutions, and scalable operational AI infrastructure."],
                ],
            ],
            // ── Huawei Cloud ──
            [
                'track' => 'Huawei Cloud', 'vendor' => 'Huawei Cloud Indonesia',
                'title' => 'Building for the Agentic AI Era: Infrastructure, Models, and Practical Use Cases',
                'topic_headline' => 'AI Infrastructure',
                'description' => 'Discover the essential building blocks of the Agentic AI era, from AI infrastructure and Model-as-a-Service to AI-assisted software development. Learn how these technologies enable scalable AI applications, streamline development, and unlock practical enterprise use cases across industries',
                'speakers' => [
                    ['name' => 'Dwina Agustin Putri', 'title' => 'Solution Architect Huawei Cloud Indonesia', 'bio' => 'Dwina Agustin Putri has experience in AI infrastructure, cloud architecture, and enterprise AI solutions, supporting government, carrier, and enterprise organizations in accelerating AI adoption and digital transformation'],
                ],
            ],
            // ── IBM ──
            [
                'track' => 'IBM', 'vendor' => 'IBM',
                'title' => 'Securing the Rise of Non-Human Identities in the AI Era',
                'topic_headline' => 'Cybersecurity',
                'description' => 'Seiring adopsi AI, cloud, dan otomatisasi, jumlah Non-Human Identities kini mencapai 50 kali lebih banyak dibanding identitas manusia. Sesi ini membahas tantangan dan strategi mengamankan identitas mesin untuk memperkuat keamanan enterprise di era AI.',
                'speakers' => [
                    ['name' => 'Hendra Tanto', 'title' => 'Principal Solution Engineer for Hashicorp, IBM Indonesia', 'bio' => 'to be confirm'],
                ],
            ],
            [
                'track' => 'IBM', 'vendor' => 'IBM',
                'title' => 'Extending Your Integration Platform with AI Gateway',
                'topic_headline' => 'Automation',
                'description' => 'Integrasi enterprise kini berkembang, tidak lagi sekadar menghubungkan aplikasi dan API, tetapi juga mengorkestrasi layanan berbasis AI. Sesi ini membahas bagaimana AI Gateway dapat memperluas platform webMethods dan IBM Integration yang sudah ada dengan fitur keamanan, tata kelola, dan observabilitas khusus AI—tanpa mengganggu arsitektur Anda saat ini.',
                'speakers' => [
                    ['name' => 'Akhmad Makki', 'title' => 'Integration Solution Strategist IBM Asean', 'bio' => 'to be confirm'],
                ],
            ],
            // ── Kong ──
            [
                'track' => 'KONG', 'vendor' => 'Kong',
                'title' => 'From Prototype to Production: Securing, Scaling, and Governing Enterprise AI with Kong',
                'topic_headline' => 'Data & AI',
                'description' => '',
                'speakers' => [
                    ['name' => 'Degui Xu', 'title' => 'Director of Solutions Engineering, Kong', 'bio' => "Degui Xu is a domain expert in the API space, including API management, integration and other middleware solutions. He has worked closely with customers and partners across the Asia Pacific region to drive their digital transformation journey leveraging Kong's technologies. Prior to Kong, Degui held architect roles at companies such as Pivotal, Mulesoft and Red Hat Asia Pacific. He holds a Bachelor's Degree in Computer Science from National University of Singapore."],
                ],
            ],
            // ── Lark ──
            [
                'track' => 'Lark', 'vendor' => 'Lark',
                'title' => 'Closing AI Value Gap - Becoming AI-first organization',
                'topic_headline' => 'Data & AI',
                'description' => 'Closing the AI Value Gap Despite growing AI investments, many organisations struggle to realise measurable returns. Discover how leading enterprises are closing the AI Value Gap by redesigning workflows, connecting operations, and enabling human-AI collaboration to turn AI into real business impact.',
                'speakers' => [
                    ['name' => 'Dimas Susilo', 'title' => 'Lark APAC Solution Engineer', 'bio' => 'Dimas Susilo is a Solution Engineer at Lark, specialising in digital transformation, workflow redesign, and AI-powered operations.TBC'],
                ],
            ],
            // ── Microsoft ──
            [
                'track' => 'Microsoft', 'vendor' => 'Microsoft',
                'title' => 'An AI Built for You',
                'topic_headline' => 'Data & AI',
                'description' => "Step into the future of work—where AI doesn't just assist, it acts. Discover how Work IQ, multimodal AI, and agentic systems transform everyday tasks into seamless outcomes, powered by a secure, unified platform and Copilot Cowork—unlocking a new era of human and AI teaming at scale",
                'speakers' => [
                    ['name' => 'Adelia Chindranata', 'title' => 'Copilot Solution Engineer, Microsoft Indonesia', 'bio' => "Adelia Chindranata is a Copilot and Agents Solution Engineer at Microsoft Indonesia, leading technical solutioning and adoption for Microsoft 365 Copilot and agentic AI across Indonesia's largest enterprises. A frequent speaker at Microsoft and partner forums across ASEAN, she makes complex AI architecture practical, secure, and usable."],
                ],
            ],
            [
                'track' => 'Microsoft', 'vendor' => 'Microsoft',
                'title' => 'From AI Vision to Action with Microsoft AI',
                'topic_headline' => 'Data & AI',
                'description' => 'Session to provide a clear understanding of Microsoft AI, its positioning within the Microsoft AI ecosystem, and its enterprise value through real customer stories that demonstrate how organizations across different industries turn AI strategy into measurable business outcomes.',
                'speakers' => [
                    ['name' => 'Syamil Fakhruddin', 'title' => 'Technical Consultant Manager, at PT Mitra Integrasi Informatika (Metrodata Group)', 'bio' => 'TBC'],
                ],
            ],
            // ── Palo Alto ──
            [
                'track' => 'Palo Alto', 'vendor' => 'Palo Alto Networks',
                'title' => 'The Resilient AI Enterprise: Secure AI by Design for Competitive Edge',
                'topic_headline' => 'Cybersecurity',
                'description' => 'Unlock the power of the AI revolution while mitigating its risks. Discover how to secure your enterprise from AI-driven threats, govern internal AI agents, and build resilient, autonomous security operations. Join us to learn how to innovate responsibly and maintain a competitive edge in the era of AI.',
                'speakers' => [
                    ['name' => 'Suwandi Ongko', 'title' => 'Sr. Architect, Palo Alto Networks, ASEAN', 'bio' => 'TBC'],
                ],
            ],
            [
                'track' => 'Palo Alto', 'vendor' => 'Palo Alto Networks',
                'title' => "Enter a New Era of Security Platform : Autonomous SOC AI-Driven Platform to Stop Tomorrow's Threat in AI Era",
                'topic_headline' => 'Cybersecurity',
                'description' => "The cybersecurity landscape is rapidly evolving, presenting organizations with unprecedented challenges. Today's threat actors are more sophisticated, using advanced techniques and AI to bypass traditional security measures. As a security professional, you're likely experiencing firsthand how the needs of your security operations center (SOC) have changed dramatically. The old ways of detecting and responding to threats are no longer sufficient in an era where breaches can occur in a matter of hours —down from 24 hours just a year ago— and regulatory requirements are becoming increasingly stringent.",
                'speakers' => [
                    ['name' => 'Slamet Firmansyah', 'title' => 'Sr. Domain Consultant, Palo Alto Networks, Indonesia', 'bio' => 'TBC'],
                ],
            ],
            // ── Proofpoint ──
            [
                'track' => 'Proofpoint', 'vendor' => 'Proofpoint',
                'title' => 'The Evolution of Insider Risk, and What The Future Holds',
                'topic_headline' => 'Cybersecurity',
                'description' => 'Explore how AI, automation, and agentic workflows are transforming insider risk into a trusted authority challenge. Learn why organizations must evolve from reactive investigations to proactive, behavior-driven programs by correlating intent, identity, data access, and AI governance to reduce business risk before incidents occur.',
                'speakers' => [
                    ['name' => 'Yeo Zhen Hong', 'title' => 'Senior Channels Sales Engineer, Proofpoint', 'bio' => 'Zhen Hong is a cybersecurity professional with over 16 years of experience in enterprise security, data protection, email security, and solution engineering. Throughout his career, he has partnered with leading cybersecurity vendors to help organizations across the Asia-Pacific region strengthen cyber resilience through strategic security initiatives, trusted advisory, and innovative technologies.'],
                ],
            ],
            // ── Red Hat ──
            [
                'track' => 'Red Hat', 'vendor' => 'Red Hat',
                'title' => 'From Pilot to Production: Closing the AI Platform Gap',
                'topic_headline' => 'Hybrid IT Infrastructure',
                'description' => 'Moving from pilot to production requires shifting from fragmented, insecure tools to a unified, governed platform that standardizes security policies for all agents and models. The market has evolved from simple human-in-the-loop models to autonomous AI agents that require robust security frameworks to operate safely. To mitigate risks like agent misbehavior, supply chain attacks, and data leakage, Red Hat implements security-in-depth across the AI stack: - Isolation: Agent execution sandboxes are used to isolate blast radiuses and contain potential issues. - Governance: Automated vulnerability scanning for agents and models, combined with AI guardrails to filter problematic inputs and outputs. - Zero-Trust Identity: Implementation of agent identity zero-trust to ensure authenticity and prevent unauthorized actions. - Auditability: End-to-end tracing and observability are integrated to provide clear audit trails for AI actions. Red Hat provides a unified platform to manage the complete lifecycle of AI agents, including: Centralized management of Agent lifecycle, prompts, and Model Context Protocol (MCP).',
                'speakers' => [
                    ['name' => 'Rakhmad Azhari', 'title' => 'Sr. Specialist Solution Architect', 'bio' => "Rakhmad is a Senior Specialist Solution Architect at Red Hat Indonesia with over 15 years of experience in enterprise technology. He specializes in digital transformation for financial institutions and large enterprises, with deep expertise in cloud-native application development, application modernization, and hybrid cloud AI infrastructure. A passionate advocate for open-source innovation, he stays at the forefront of AI infrastructure trends, including large-language-model serving and intelligent data architectures. Rakhmad brings a seasoned practitioner's perspective to conversations about responsible and effective AI adoption at scale in regulated industries."],
                ],
            ],
            [
                'track' => 'Red Hat', 'vendor' => 'Red Hat',
                'title' => 'Sovereign AI in Practice: From Model to Deployment',
                'topic_headline' => 'Data & AI',
                'description' => "Many organizations want to adopt AI without losing control — over their data, their models, or their infrastructure. This session looks at what it actually takes to turn a raw model into a working, sovereign AI deployment: moving from an isolated proof of concept to something the rest of the organization can actually rely on, and knowing when it's worth building versus leveraging proven, pre-integrated solutions for common use cases. Along the way, we'll unpack what \"sovereign\" really means in practice — open source foundations, no vendor lock-in, and full control over where your models and data live — with real examples on the Red Hat AI stack.",
                'speakers' => [
                    ['name' => 'Christian Suryanto', 'title' => 'Senior Specialist Solution Architect', 'bio' => 'Christian serves as Senior Specialist Solution Architect for OpenShift at Red Hat, helping enterprises navigate container platform adoption, modern virtualization alternatives, and AI-ready infrastructure. Coming from over a decade in enterprise infrastructure across Indonesia\'s banking and public sectors, he bridges deep technical expertise with the business outcomes leadership actually cares about.'],
                ],
            ],
            // ── Salesforce ──
            [
                'track' => 'Salesforce', 'vendor' => 'Salesforce',
                'title' => 'Transforming Customer & IT Service with Salesforce Service Cloud: CCaaS and ITSM in the Age of AI',
                'topic_headline' => 'Business Application',
                'description' => "This session explores how Salesforce Service Cloud empowers organizations to modernize both their contact center and IT service operations on a single, unified platform. We'll dive into how Service Cloud's CCaaS capabilities — including omni-channel routing, voice integration, and real-time analytics — work alongside ITSM workflows such as incident and change management to streamline service delivery across the enterprise.",
                'speakers' => [
                    ['name' => 'Raditya Putra', 'title' => 'Service Cloud AE', 'bio' => "Raditya Putra, Salesforce Service Cloud Account Executive, helps enterprises modernize contact centers and IT service operations on one platform. He'll showcase how Service Cloud's CCaaS and ITSM capabilities unify service delivery for greater efficiency and impact."],
                    ['name' => 'Juneidi Tsai', 'title' => 'Strategic Solution Engineer', 'bio' => ''],
                ],
            ],
            [
                'track' => 'Salesforce', 'vendor' => 'Salesforce',
                'title' => 'Pay-As-You-Execute: How to Leverage Agentic AI and Message Credits for Maximum Business Benefit',
                'topic_headline' => 'Business Application',
                'description' => 'For years, enterprise software relied on predictable, per-user subscription models. Today, we are abruptly entering the Consumables Era. As infrastructure shifts from humans storing data to autonomous agents executing workflows, the core unit of business cost has changed. We are now paying for execution: LLM API tokens, adaptive compute budgets, and WhatsApp messaging credits. In this session, we will break down how to architect your systems for this new paradigm. We will explore how Agentic AI natively utilizes consumable resources to execute complex, multi-step tasks—such as automated customer operations over WhatsApp—and how enterprise leaders can strategically optimize these consumption patterns to turn variable execution costs into a massive bottom-line advantage.',
                'speakers' => [
                    ['name' => 'Lukman Hakim', 'title' => 'Regional Sales Executive Data & AI Salesforce', 'bio' => 'Specialized in digital transformation, Lukman Hakim, Salesforce Regional Sales Executive Data & AI, strives to drive adoption of Agentic AI and cloud technologies to unify data, accelerate growth, and unlock enterprise value across diverse industries.'],
                ],
            ],
            // ── Tenable ──
            [
                'track' => 'Tenable', 'vendor' => 'Tenable',
                'title' => 'From Visibility to Velocity: CTEM as the New Cyber Risk Mandate',
                'topic_headline' => 'AI-Driven Cyberscurity',
                'description' => 'What board-level accountability looks like in an AI-driven threat landscape & how to shift from vulnerability counting to exposure command.',
                'speakers' => [
                    ['name' => 'Novan Tambunan', 'title' => 'Security Engineer, Tenable', 'bio' => 'Novan Tambunan is a Security Engineer at Tenable, bringing extensive experience in securing enterprise and government environments. Prior to joining Tenable, he held key security engineering roles at McAfee and Symantec, where he contributed to large-scale cybersecurity initiatives.'],
                ],
            ],
        ];

        $trackIds = Track::pluck('id', 'name');
        $count = ['sessions' => 0, 'speakers' => 0, 'skipped' => 0];

        foreach ($sessions as $s) {
            $trackId = $trackIds[$s['track']] ?? null;
            if (!$trackId) {
                $this->command?->warn("TrackSessionSeeder: track '{$s['track']}' not found — skipping session '{$s['title']}'.");
                $count['skipped']++;
                continue;
            }

            $sync = [];
            foreach ($s['speakers'] as $i => $sp) {
                $speaker = Speaker::updateOrCreate(
                    ['name' => $sp['name']],
                    [
                        'title'     => $sp['title'],
                        'company'   => $s['vendor'],
                        'bio'       => $sp['bio'],
                        'is_active' => true,
                    ]
                );
                if ($speaker->wasRecentlyCreated) {
                    $count['speakers']++;
                }
                $sync[$speaker->id] = ['order' => $i + 1];
            }

            $item = AgendaItem::firstOrCreate(
                [
                    'title'          => $s['title'],
                    'topic_headline' => $s['topic_headline'],
                    'track_id'       => $trackId,
                    'category'       => 'track',
                ],
                [
                    'description'    => $s['description'] !== '' ? $s['description'] : null,
                    'key_highlights' => null,
                    'agenda_type'    => 'track',
                    'workshop_id'    => null,
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

            $item->speakers()->syncWithoutDetaching($sync);
        }

        $this->command?->info("TrackSessionSeeder: {$count['sessions']} session(s) created, {$count['speakers']} speaker(s) created, {$count['skipped']} skipped (missing track). (existing kept, nothing deleted)");
    }
}
