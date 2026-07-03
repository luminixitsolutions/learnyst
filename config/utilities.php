<?php

return [
    'hub' => [
        [
            'slug' => 'copy-product',
            'title' => 'Copy Product',
            'description' => 'Create copy of course, mocktest or test series to a new product or existing product within school or sub-school.',
            'note' => 'Note: Copy of course will be created as encrypted course',
            'route' => 'admin.utilities.copy-product',
            'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
        ],
    ],
    'copy_types' => [
        [
            'slug' => 'course',
            'title' => 'Copy Course',
            'description' => 'Start copying a course',
            'button' => 'Copy course',
            'route' => 'admin.utilities.copy-course',
            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        ],
        [
            'slug' => 'mock-test',
            'title' => 'Copy Mock-Test',
            'description' => 'Start copying a mock-test',
            'button' => 'Copy mock-test',
            'route' => 'admin.utilities.copy-mock-test',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ],
        [
            'slug' => 'test-series',
            'title' => 'Copy Test Series',
            'description' => 'Start copying a test series',
            'button' => 'Copy test series',
            'route' => 'admin.utilities.copy-test-series',
            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        ],
    ],
];
