<?php

/*
|--------------------------------------------------------------------------
| Dynamic marketing page defaults
|--------------------------------------------------------------------------
| Single source of truth for the built-in content of the website's dynamic
| pages (why-us, services, careers, become-executive, blogs, faqs).
|
|  - WebsitePageSeeder seeds the website_pages table from this array.
|  - The website blades use these values as a fallback when no DB row exists
|    (or a key is missing), so the pages always render fully.
*/

return [

    'why-us' => [
        'tag'      => 'Why EDYONE LMS',
        'title'    => 'The smarter choice for modern schools',
        'subtitle' => 'Hundreds of schools across India trust EDYONE LMS to run admissions, academics, fees and communication on a single affordable platform. Here is what sets us apart.',
        'items'    => [
            ['icon' => '💰', 'title' => 'Genuinely Affordable', 'desc' => 'Transparent, per-student pricing with no hidden setup fees. Built so that schools of every size can afford world-class technology.'],
            ['icon' => '🧩', 'title' => 'All-in-One Platform', 'desc' => 'Admissions, attendance, timetable, exams, fees, study material and parent communication — everything in one login instead of five different tools.'],
            ['icon' => '📱', 'title' => 'Mobile First', 'desc' => 'Dedicated apps for admins, teachers, students and parents — so your whole school stays connected from any phone, anywhere.'],
            ['icon' => '💳', 'title' => 'Online Fee Collection', 'desc' => 'Collect fees online with instant receipts and reconciliation. Parents pay in seconds, your accounts team saves hours every week.'],
            ['icon' => '🛟', 'title' => 'Real Human Support', 'desc' => 'Onboarding, training and ongoing help from a team that actually understands schools — over call, chat and WhatsApp.'],
            ['icon' => '🇮🇳', 'title' => 'Built for India', 'desc' => 'Designed around Indian school workflows, boards, fee structures and languages — not a foreign product forced to fit.'],
        ],
    ],

    'services' => [
        'tag'      => 'Our Services',
        'title'    => 'Everything your school needs, end to end',
        'subtitle' => 'From setup to daily operations, EDYONE LMS gives schools a complete digital toolkit — backed by hands-on services so you are never left figuring it out alone.',
        'items'    => [
            ['icon' => '🎓', 'title' => 'School Management System', 'desc' => 'Manage students, staff, classes, sections and the full academic year from one powerful dashboard.'],
            ['icon' => '📝', 'title' => 'Admissions & Enquiries', 'desc' => 'Capture enquiries, run online admissions and convert leads into enrolled students with less paperwork.'],
            ['icon' => '🗓️', 'title' => 'Attendance & Timetable', 'desc' => 'Daily attendance, automated timetables and arrangement management for teachers and classes.'],
            ['icon' => '📊', 'title' => 'Exams & Report Cards', 'desc' => 'Set up exams, enter marks and generate professional report cards in a few clicks.'],
            ['icon' => '💳', 'title' => 'Fee Management', 'desc' => 'Define fee structures, collect payments online, and track dues with automatic receipts and reminders.'],
            ['icon' => '📚', 'title' => 'Digital Content & Quizzes', 'desc' => 'Share syllabus, study material, books and quizzes so learning continues beyond the classroom.'],
            ['icon' => '🔔', 'title' => 'Notifications & Communication', 'desc' => 'Reach parents and staff instantly with announcements, push notifications and calendar updates.'],
            ['icon' => '🆔', 'title' => 'ID Cards & Documents', 'desc' => 'Generate student and staff ID cards and essential documents directly from the platform.'],
            ['icon' => '🤝', 'title' => 'Onboarding & Training', 'desc' => 'Guided setup, data migration and staff training so your school goes live smoothly and quickly.'],
        ],
    ],

    'careers' => [
        'tag'      => 'Careers & Partnerships',
        'title'    => 'Earn up to ₹1 Lakh+ a month with EDYONE',
        'subtitle' => 'Join EDYONE as a partner and help schools across India go digital. Work remotely, on your own schedule, with a simple joining process and fast payouts — earn attractive recurring income for every school you bring on board.',
        'jobs' => [
            ['role' => 'Business Development Executive', 'department' => 'Sales', 'location' => 'Field / Remote', 'type' => 'Full-time'],
            ['role' => 'School Partnership Associate', 'department' => 'Partnerships', 'location' => 'Remote', 'type' => 'Full-time / Part-time'],
            ['role' => 'Customer Support Associate', 'department' => 'Support', 'location' => 'Aligarh, UP', 'type' => 'Full-time'],
            ['role' => 'School Onboarding Specialist', 'department' => 'Operations', 'location' => 'Hybrid', 'type' => 'Full-time'],
        ],
    ],

    'become-executive' => [
        'tag'      => 'Partner Program',
        'title'    => 'Become an EDYONE Executive',
        'subtitle' => 'Partner with EDYONE LMS to bring affordable school technology to institutions in your region — and earn attractive recurring income while you do it.',
        'benefits' => [
            ['icon' => '💸', 'title' => 'Attractive Commissions', 'desc' => 'Earn competitive payouts on every school you onboard, plus recurring income as they renew.'],
            ['icon' => '📈', 'title' => 'Ready Demand', 'desc' => 'Schools everywhere need affordable digital tools — you bring a product that practically sells itself.'],
            ['icon' => '🎒', 'title' => 'Full Sales Kit', 'desc' => 'Get brochures, demos, pricing and live training so you can pitch with confidence from day one.'],
            ['icon' => '🛟', 'title' => 'Dedicated Support', 'desc' => 'Our team handles onboarding and technical support, so you can focus on building relationships.'],
            ['icon' => '⏱️', 'title' => 'Flexible & Independent', 'desc' => 'Work your own hours, in your own region — full-time or alongside your existing work.'],
            ['icon' => '🏅', 'title' => 'Recognition & Rewards', 'desc' => 'Top-performing executives unlock higher tiers, bonuses and exclusive incentives.'],
        ],
        'steps' => [
            ['title' => 'Apply', 'desc' => 'Fill in a short form and tell us about your region and experience.'],
            ['title' => 'Onboard', 'desc' => 'Get trained on the product, pricing and sales material.'],
            ['title' => 'Pitch', 'desc' => 'Introduce EDYONE LMS to schools and book demos with our support.'],
            ['title' => 'Earn', 'desc' => 'Get paid for every school you bring on board — and keep earning.'],
        ],
    ],

    'blogs' => [
        'tag'      => 'The EDYONE Blog',
        'title'    => 'Ideas & insights for modern schools',
        'subtitle' => 'Welcome to the EDYONE blog — your go-to space for practical ideas, product updates and real stories from schools across India. We share hands-on tips on attendance, fees, exams, admissions and parent communication, alongside guides that help school leaders, teachers and administrators run their institutions better, teach smarter and engage families more effectively. Whether you are just going digital or looking to get more out of your LMS, there is something here to help your school grow.',
        // Blog posts are now stored in the `blogs` table and managed from the
        // super-admin Blogs screen (no longer config-driven).
    ],

    'faqs' => [
        'tag'      => 'Help Center',
        'title'    => 'Frequently asked questions',
        'subtitle' => "Everything you need to know about EDYONE LMS. Can't find your answer? Our team is just a message away.",
        'faqs'     => [
            ['question' => 'What is EDYONE LMS?', 'answer' => 'EDYONE LMS is an affordable, all-in-one Learning Management System for schools. It covers admissions, attendance, timetable, exams, fees, study material and parent communication — all from one platform, with apps for admins, teachers, students and parents.'],
            ['question' => 'How much does it cost?', 'answer' => 'Pricing is simple and transparent, designed to be affordable for schools of every size. Visit our Pricing page or request a demo and we will share a plan tailored to your student count.'],
            ['question' => 'Is there a mobile app?', 'answer' => 'Yes. EDYONE LMS has dedicated mobile apps for Android and iOS, so admins, teachers, students and parents can stay connected from anywhere.'],
            ['question' => 'Can parents pay fees online?', 'answer' => 'Absolutely. Parents can pay fees securely online and receive instant digital receipts, while your accounts team gets automatic reconciliation and dues tracking.'],
            ['question' => 'How long does it take to set up?', 'answer' => 'Most schools go live within a few days. Our onboarding team helps you import your data, configure classes and fees, and trains your staff so the transition is smooth.'],
            ['question' => "Is my school's data secure?", 'answer' => 'Yes. Your data is stored securely, access is role-based, and payments are processed through trusted, secure gateways. Your information is never shared without your consent.'],
            ['question' => 'Do you provide training and support?', 'answer' => 'Of course. We provide hands-on onboarding, staff training, and ongoing support over call, chat and WhatsApp so you are never left on your own.'],
            ['question' => 'How do I get started?', 'answer' => 'Simply request a free demo or contact us. We will walk you through the platform and help you choose the right plan.'],
        ],
    ],

];
