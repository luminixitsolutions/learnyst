<?php

return [
    'admin' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'permission' => 'dashboard.view',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'group' => 'Website',
            'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
            'items' => [
                ['label' => 'Institute Profile', 'route' => 'admin.company-profile.edit', 'permission' => 'settings.view'],
                ['label' => 'My Profile Page', 'route' => 'admin.company-profile.preview', 'permission' => 'settings.view', 'target' => '_blank'],
                ['label' => 'Website Builder', 'route' => 'admin.website-builder.index', 'permission' => 'settings.view', 'active_routes' => [
                    'admin.website-builder.*',
                ]],
                ['label' => 'Testimonials', 'route' => 'admin.company-page.testimonials', 'permission' => 'settings.view'],
                ['label' => 'Reviews', 'route' => 'admin.company-page.reviews', 'permission' => 'settings.view'],
                ['label' => 'Enquiries', 'route' => 'admin.company-page.enquiries', 'permission' => 'settings.view'],
                ['label' => 'Gallery', 'route' => 'admin.company-page.gallery', 'permission' => 'settings.view'],
                ['label' => 'Videos', 'route' => 'admin.company-page.videos', 'permission' => 'settings.view'],
                ['label' => 'Blogs', 'route' => 'admin.company-page.blogs', 'permission' => 'settings.view'],
                ['label' => 'Team', 'route' => 'admin.company-page.team', 'permission' => 'settings.view'],
                ['label' => 'Homepage Sections', 'route' => 'admin.website-sections.index', 'permission' => 'settings.view'],
                ['label' => 'Preview Website', 'route' => 'admin.website-sections.preview', 'permission' => 'settings.view', 'hidden' => true],
                ['label' => 'Theme Settings', 'route' => 'admin.settings.index', 'permission' => 'settings.view', 'hidden' => true],
                ['label' => 'Social Links', 'route' => 'admin.settings.social', 'permission' => 'settings.view'],
            ],
        ],
        [
            'group' => 'Course Management',
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'items' => [
                ['label' => 'Curriculum Builder', 'route' => 'admin.courses.index', 'permission' => 'products.view', 'active_routes' => [
                    'admin.courses.builder',
                    'admin.courses.edit',
                    'admin.courses.create',
                    'admin.courses.settings.*',
                    'admin.courses.sections.*',
                    'admin.lessons.*',
                ]],
                ['label' => 'Quizzes', 'route' => 'admin.quizzes.index', 'permission' => 'products.view'],
                ['label' => 'Assignments', 'route' => 'admin.assignments.index', 'permission' => 'products.view'],
                ['label' => 'Live Classes', 'route' => 'admin.live-classes.index', 'permission' => 'products.view'],
            ],
        ],
        [
            'group' => 'Products',
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'items' => [
                ['label' => 'Courses', 'route' => 'admin.courses.index', 'permission' => 'products.view', 'active_routes' => ['admin.courses.index']],
                ['label' => 'Mock test', 'route' => 'admin.mock-tests.index', 'permission' => 'products.view'],
                ['label' => 'Test Series', 'route' => 'admin.test-series.index', 'permission' => 'products.view'],
                ['label' => 'Bundles', 'route' => 'admin.bundles.index', 'permission' => 'products.view'],
                ['label' => 'Batch', 'route' => 'admin.batches.index', 'permission' => 'products.view'],
                ['label' => 'Poll', 'route' => 'admin.polls.index', 'permission' => 'products.view'],
                ['label' => 'Tracks', 'route' => 'admin.tracks.index', 'permission' => 'products.view'],
                ['label' => 'Code', 'route' => 'admin.code.index', 'permission' => 'products.view', 'hidden' => true],
                ['label' => 'More Products', 'route' => 'admin.more-products.index', 'permission' => 'products.view'],
                ['label' => 'Question Pool', 'route' => 'admin.question-pools.index', 'permission' => 'products.view'],
                ['label' => 'All Questions', 'route' => 'admin.questions.index', 'permission' => 'products.view'],
                ['label' => 'Classification', 'route' => 'admin.classification.index', 'permission' => 'products.view'],
                ['label' => 'Utilities', 'route' => 'admin.utilities.index', 'permission' => 'products.view'],
            ],
        ],
        [
            'group' => 'Users',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'items' => [
                ['label' => 'Learners', 'route' => 'admin.learners.index', 'permission' => 'learners.view'],
                ['label' => 'Groups', 'route' => 'admin.groups.index', 'permission' => 'learners.view'],
                ['label' => 'Instructors', 'route' => 'admin.instructors.index', 'permission' => 'instructors.view'],
                ['label' => 'Sub-Admins', 'route' => 'admin.sub-admins.index', 'permission' => 'sub_admins.view'],
                ['label' => 'Alumni', 'route' => 'admin.alumni.index', 'permission' => 'alumni.view'],
                ['label' => 'Parent Links', 'route' => 'admin.parent-links.index', 'permission' => 'parent.view'],
                ['label' => 'Contacts', 'route' => 'admin.contacts.index', 'permission' => 'learners.view', 'badge' => 'New'],
                ['label' => 'Legal Documents', 'route' => 'admin.legal-documents.index', 'permission' => 'learners.view', 'badge' => 'New'],
            ],
        ],
        [
            'group' => 'Sales',
            'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            'items' => [
                ['label' => 'Orders', 'route' => 'admin.orders.index', 'permission' => 'sales.view'],
                ['label' => 'Payments', 'route' => 'admin.payments.index', 'permission' => 'sales.view'],
                ['label' => 'Payment / Razorpay', 'route' => 'admin.settings.index', 'params' => ['tab' => 'payment'], 'permission' => 'settings.view'],
                ['label' => 'Checkout Consent', 'route' => 'admin.checkout-consents.index', 'permission' => 'sales.view'],
                ['label' => 'Wallets', 'route' => 'admin.wallets.index', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.wallets.*',
                ]],
                ['label' => 'Referrals', 'route' => 'admin.referrals.index', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.referrals.*',
                ]],
                ['label' => 'Affiliates', 'route' => 'admin.affiliates.index', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.affiliates.*',
                ]],
                ['label' => 'Subscriptions', 'route' => 'admin.subscriptions.plans', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.subscriptions.*',
                ]],
                ['label' => 'GST Invoices', 'route' => 'admin.gst-invoices.index', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.gst-invoices.*',
                    'admin.orders.gst-invoice',
                ]],
            ],
        ],
        [
            'group' => 'Marketing',
            'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
            'items' => [
                ['label' => 'Campaigns', 'route' => 'admin.marketing.campaigns', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.marketing.campaigns',
                    'admin.marketing.campaigns.*',
                ]],
                ['label' => 'Leads', 'route' => 'admin.marketing.leads', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.marketing.leads',
                    'admin.marketing.leads.*',
                ]],
                ['label' => 'Coupons', 'route' => 'admin.marketing.coupons', 'permission' => 'sales.view', 'active_routes' => [
                    'admin.marketing.coupons',
                    'admin.marketing.coupons.*',
                ]],
                ['label' => 'Segments', 'route' => 'admin.segments.index', 'permission' => 'sales.view', 'active_routes' => [
                    'admin.segments.*',
                ]],
                ['label' => 'Automations', 'route' => 'admin.automations.index', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => [
                    'admin.automations.*',
                ]],
                ['label' => 'Landing Pages', 'route' => 'admin.landing-pages.index', 'permission' => 'sales.view', 'active_routes' => [
                    'admin.landing-pages.*',
                ]],
                ['label' => 'Webinar Registrations', 'route' => 'admin.webinar-registrations.index', 'permission' => 'sales.view', 'active_routes' => [
                    'admin.webinar-registrations.*',
                ]],
            ],
        ],
        [
            'group' => 'CRM',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'items' => [
                ['label' => 'Pipeline', 'route' => 'admin.crm.pipeline', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => ['admin.crm.pipeline']],
                ['label' => 'Leads', 'route' => 'admin.crm.leads', 'permission' => 'sales.view', 'active_routes' => ['admin.crm.leads', 'admin.crm.leads.*']],
                ['label' => 'Follow-ups', 'route' => 'admin.crm.follow-ups', 'permission' => 'sales.view', 'active_routes' => ['admin.crm.follow-ups*']],
                ['label' => 'Call Logs', 'route' => 'admin.crm.call-logs', 'permission' => 'sales.view'],
                ['label' => 'Counselor Dashboard', 'route' => 'admin.crm.counselor', 'permission' => 'sales.view'],
                ['label' => 'Analytics', 'route' => 'admin.crm.analytics', 'permission' => 'sales.view'],
            ],
        ],
        [
            'group' => 'Teaching',
            'icon' => 'M12 14l9-5-9-5-9 5 9 5z|M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
            'items' => [
                ['label' => 'Batches', 'route' => 'admin.batches.index', 'permission' => 'batches.view'],
                ['label' => 'Certificates', 'route' => 'admin.certificates.index', 'permission' => 'products.view'],
                ['label' => 'Certificate Renewal', 'route' => 'admin.certificates.renewals.index', 'permission' => 'certificates_renewal.view'],
                ['label' => 'Exam Proctoring', 'route' => 'admin.proctoring.index', 'permission' => 'proctoring.view'],
                ['label' => 'XP Rules', 'route' => 'admin.gamification.rules', 'permission' => 'products.view', 'badge' => 'New', 'active_routes' => [
                    'admin.gamification.rules*',
                ]],
                ['label' => 'Badges', 'route' => 'admin.gamification.badges', 'permission' => 'products.view', 'active_routes' => [
                    'admin.gamification.badges*',
                ]],
                ['label' => 'Challenges', 'route' => 'admin.gamification.challenges', 'permission' => 'products.view', 'active_routes' => [
                    'admin.gamification.challenges*',
                ]],
                ['label' => 'Leaderboard', 'route' => 'admin.gamification.leaderboard', 'permission' => 'products.view', 'active_routes' => [
                    'admin.gamification.leaderboard',
                ]],
            ],
        ],
        [
            'group' => 'Community',
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'items' => [
                ['label' => 'Communities', 'route' => 'admin.communities.index', 'permission' => 'community.view'],
                ['label' => 'Discussions', 'route' => 'admin.discussions.index', 'permission' => 'community.view'],
            ],
        ],
        [
            'group' => 'Admin Management',
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'items' => [
                ['label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'sub_admins.manage'],
                ['label' => 'Permissions', 'route' => 'admin.permissions.index', 'permission' => 'sub_admins.manage'],
            ],
        ],
        [
            'group' => 'Insights',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.insights.dashboard', 'permission' => 'insights.view'],
                ['label' => 'School Vitals', 'route' => 'admin.insights.school-vitals', 'permission' => 'insights.view'],
                ['label' => 'Sales Insight', 'route' => 'admin.insights.sales.index', 'permission' => 'insights.view'],
                ['label' => 'Live Dashboard', 'route' => 'admin.insights.live.index', 'permission' => 'insights.view'],
                ['label' => 'Marketing Insight', 'route' => 'admin.insights.marketing.index', 'permission' => 'insights.view'],
                ['label' => 'Messenger Insight', 'route' => 'admin.insights.messenger.index', 'permission' => 'insights.view'],
            ],
        ],
        [
            'group' => 'Reports',
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'items' => [
                ['label' => 'All Reports', 'route' => 'admin.reports.index', 'permission' => 'reports.view'],
                ['label' => 'Learner Enrollments', 'route' => 'admin.reports.enrollments', 'permission' => 'reports.view'],
                ['label' => 'Transactions', 'route' => 'admin.reports.transactions', 'permission' => 'reports.view'],
                ['label' => 'Payment Gateways', 'route' => 'admin.reports.payment-gateways', 'permission' => 'reports.view'],
                ['label' => 'School Payouts', 'route' => 'admin.reports.school-payouts', 'permission' => 'reports.view'],
                ['label' => 'Product Progress', 'route' => 'admin.reports.progress.index', 'permission' => 'reports.view'],
                ['label' => 'Bundle Progress', 'route' => 'admin.reports.bundle-progress', 'permission' => 'reports.view'],
                ['label' => 'Custom Product Progress', 'route' => 'admin.reports.custom-product-progress', 'permission' => 'reports.view'],
                ['label' => 'Test Series Scores', 'route' => 'admin.reports.test-series-scores', 'permission' => 'reports.view'],
                ['label' => 'Batches Report', 'route' => 'admin.reports.batches', 'permission' => 'reports.view'],
                ['label' => 'Sales Reports', 'route' => 'admin.reports.sales.index', 'permission' => 'reports.view'],
                ['label' => 'Learners Report', 'route' => 'admin.reports.learners', 'permission' => 'reports.view'],
                ['label' => 'Course Report', 'route' => 'admin.reports.courses', 'permission' => 'reports.view'],
                ['label' => 'Bundle Report', 'route' => 'admin.reports.bundles', 'permission' => 'reports.view'],
                ['label' => 'Zoom Insights', 'route' => 'admin.reports.zoom-insights', 'permission' => 'reports.view'],
                ['label' => 'Live Class Attendance', 'route' => 'admin.reports.live-class-attendance', 'permission' => 'reports.view'],
                ['label' => 'Resource Usage', 'route' => 'admin.reports.resource-usage', 'permission' => 'reports.view'],
                ['label' => 'Super Live Lessons', 'route' => 'admin.reports.super-live-lessons', 'permission' => 'reports.view'],
                ['label' => 'Certificates Report', 'route' => 'admin.reports.certificates', 'permission' => 'reports.view'],
            ],
        ],
        [
            'group' => 'Finance',
            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.finance.dashboard', 'permission' => 'sales.view', 'badge' => 'New', 'active_routes' => ['admin.finance.dashboard']],
                ['label' => 'Ledger', 'route' => 'admin.finance.ledger', 'permission' => 'sales.view', 'active_routes' => ['admin.finance.ledger*']],
                ['label' => 'Accounts', 'route' => 'admin.finance.accounts', 'permission' => 'sales.view'],
                ['label' => 'Cash Book', 'route' => 'admin.finance.cash-book', 'permission' => 'sales.view'],
                ['label' => 'Bank Book', 'route' => 'admin.finance.bank-book', 'permission' => 'sales.view'],
                ['label' => 'Receipts', 'route' => 'admin.finance.receipts', 'permission' => 'sales.view', 'active_routes' => ['admin.finance.receipts*']],
                ['label' => 'GST Invoices', 'route' => 'admin.finance.invoices', 'permission' => 'sales.view'],
                ['label' => 'Profit & Loss', 'route' => 'admin.finance.profit-loss', 'permission' => 'sales.view'],
                ['label' => 'Balance Sheet', 'route' => 'admin.finance.balance-sheet', 'permission' => 'sales.view'],
                ['label' => 'Tax Export (CSV)', 'route' => 'admin.finance.tax-export', 'permission' => 'sales.view'],
            ],
        ],
        [
            'group' => 'HR',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'items' => [
                ['label' => 'Employees', 'route' => 'admin.hr.employees', 'permission' => 'learners.view', 'badge' => 'New', 'active_routes' => ['admin.hr.employees*']],
                ['label' => 'Attendance', 'route' => 'admin.hr.attendance', 'permission' => 'learners.view'],
                ['label' => 'Leave', 'route' => 'admin.hr.leaves', 'permission' => 'learners.view', 'active_routes' => ['admin.hr.leaves*']],
                ['label' => 'Payroll', 'route' => 'admin.hr.payroll', 'permission' => 'learners.view', 'active_routes' => ['admin.hr.payroll*', 'admin.hr.slips*']],
            ],
        ],
        [
            'group' => 'Branches / Franchise',
            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'items' => [
                ['label' => 'Branches', 'route' => 'admin.branches.index', 'permission' => 'settings.view', 'badge' => 'New', 'active_routes' => ['admin.branches.index', 'admin.branches.show']],
                ['label' => 'Branch Reports', 'route' => 'admin.branches.reports', 'permission' => 'reports.view'],
            ],
        ],
        [
            'group' => 'Placements',
            'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'items' => [
                ['label' => 'Recruiters', 'route' => 'admin.placements.companies', 'permission' => 'learners.view', 'badge' => 'New', 'active_routes' => ['admin.placements.companies*']],
                ['label' => 'Jobs & Internships', 'route' => 'admin.placements.jobs', 'permission' => 'learners.view', 'active_routes' => ['admin.placements.jobs*']],
                ['label' => 'Applications', 'route' => 'admin.placements.applications', 'permission' => 'learners.view'],
                ['label' => 'Reports', 'route' => 'admin.placements.reports', 'permission' => 'reports.view'],
            ],
        ],
        [
            'group' => 'Digital Library',
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'items' => [
                ['label' => 'Library Manager', 'route' => 'admin.library.index', 'permission' => 'products.view', 'badge' => 'New', 'active_routes' => ['admin.library.*']],
            ],
        ],
        [
            'group' => 'Settings',
            'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
            'items' => [
                ['label' => 'Institute Profile', 'route' => 'admin.company-profile.edit', 'permission' => 'settings.view'],
                ['label' => 'Website Settings', 'route' => 'admin.settings.index', 'permission' => 'settings.view'],
                ['label' => 'Payment / Razorpay', 'route' => 'admin.settings.index', 'params' => ['tab' => 'payment'], 'permission' => 'settings.view'],
                ['label' => 'Social Links', 'route' => 'admin.settings.social', 'permission' => 'settings.view'],
                ['label' => 'Sidebar Settings', 'route' => 'admin.settings.sidebar', 'permission' => 'settings.view'],
                ['label' => 'Compliance Center', 'route' => 'admin.compliance.index', 'permission' => 'compliance.view'],
                ['label' => 'Notification Center', 'route' => 'admin.notifications.index', 'permission' => 'notifications.view'],
                ['label' => 'White Label (Web)', 'route' => 'admin.whitelabel.edit', 'permission' => 'settings.view', 'badge' => 'New', 'active_routes' => [
                    'admin.whitelabel.*',
                ]],
            ],
        ],
        [
            'group' => 'AI Center',
            'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
            'items' => [
                ['label' => 'AI Center', 'route' => 'admin.ai.index', 'permission' => 'products.view', 'badge' => 'New', 'active_routes' => [
                    'admin.ai.*',
                ]],
            ],
        ],
        [
            'group' => 'Integrations',
            'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
            'items' => [
                ['label' => 'Integrations', 'route' => 'admin.integrations.index', 'permission' => 'settings.view', 'badge' => 'New', 'active_routes' => [
                    'admin.integrations.*',
                ]],
                ['label' => 'Security', 'route' => 'admin.security.index', 'permission' => 'settings.view', 'active_routes' => [
                    'admin.security.*',
                ]],
            ],
        ],
    ],
];
