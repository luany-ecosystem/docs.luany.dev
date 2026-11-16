<?php

return [
    'ui' => [
        'docs'               => 'Docs',
        'search'             => 'Search docs',
        'search_placeholder' => 'Search the documentation…',
        'search_empty'       => 'No results for',
        'search_hint'        => 'to navigate',
        'menu'               => 'Menu',
        'theme_light'        => 'Switch to light theme',
        'theme_dark'         => 'Switch to dark theme',
        'language'           => 'Language',
        'previous'           => 'Previous',
        'next'               => 'Next',
        'edit'               => 'Edit this page on GitHub',
        'on_this_page'       => 'On this page',
        'copy'               => 'Copy',
        'copied'             => 'Copied',
        'skip'               => 'Skip to content',
    ],

    'nav' => [
        ['label' => 'Get started', 'slug' => 'installation'],
        ['label' => 'Routing',     'slug' => 'routing'],
        ['label' => 'Templates',   'slug' => 'lte-syntax'],
        ['label' => 'Database',    'slug' => 'models'],
        ['label' => 'CLI',         'slug' => 'cli'],
    ],

    'landing' => [
        'meta_title'       => 'Luany — the PHP framework with nothing to hide',
        'meta_description' => 'Luany is a PHP framework with an explicit request lifecycle, templates checked before they run, a complete ORM and a CLI that scaffolds whole features.',

        'eyebrow'   => 'Luany 1.x · PHP 8.2+ · MIT',
        'title_a'   => 'The PHP framework',
        'title_b'   => 'with nothing to hide.',
        'lead'      => 'An explicit request lifecycle, templates checked before they run, a complete ORM and a CLI that scaffolds whole features — so you ship fast and always know what your app is doing.',
        'cta'       => 'Get started',
        'cta_alt'   => 'View on GitHub',
        'install'   => 'Install',

        'stage_route'    => 'routes/http.php',
        'stage_view'     => 'views/pages/posts/index.lte',
        'stage_check'    => 'luany view:check',
        'stage_ok'       => '24 views · 0 problems',

        'strip' => 'One ecosystem, released together',

        'code_title'  => 'Everything a web app needs. Nothing you can\'t read.',
        'code_lead'   => 'Every layer is small, documented and replaceable — from the router to the ORM. Read the source in an afternoon; build on it for years.',
        'code_points' => [
            'Routing with groups, names, model binding and numeric parameters',
            'LTE templates compiled to PHP and checked for errors first',
            'ORM with relationships, eager loading, events and soft deletes',
            'Portable migrations for SQLite, MySQL and PostgreSQL',
            'Validation with database and file-upload rules',
        ],
        'code_cta'    => 'Explore the docs',
        'tabs' => [
            'routing'    => 'Routing',
            'controller' => 'Controllers',
            'view'       => 'Templates',
            'model'      => 'ORM',
            'migration'  => 'Migrations',
            'cli'        => 'CLI',
        ],

        'features_title' => 'Built to be understood',
        'features_lead'  => 'The things that slow teams down — magic, surprises, late errors — are designed out.',
        'features' => [
            'lifecycle' => [
                'title' => 'A lifecycle you can trace',
                'text'  => 'Bootstrap, boot, handle, send, terminate. Every request walks the same explicit pipeline — no hidden hooks.',
                'link'  => 'request-lifecycle',
            ],
            'templates' => [
                'title' => 'Templates that fail early',
                'text'  => 'LTE builds a real syntax tree and lints the PHP inside it. Unclosed blocks and typos surface at compile time — and in your editor.',
                'link'  => 'lte-errors',
            ],
            'scaffold' => [
                'title' => 'One command, a whole feature',
                'text'  => 'make:feature writes the model, migration, validated controller, four views and the routes. Edit, don\'t boilerplate.',
                'link'  => 'make-feature',
            ],
            'orm' => [
                'title' => 'An ORM that speaks relationships',
                'text'  => 'hasMany, belongsToMany, morphs and through-relations, eager loading without N+1, casts, events and observers.',
                'link'  => 'relationships',
            ],
            'standards' => [
                'title' => 'Standards inside',
                'text'  => 'PSR-7, PSR-11, PSR-15 and PSR-17 bridges: bring the middleware and libraries you already trust.',
                'link'  => 'psr',
            ],
            'security' => [
                'title' => 'Secure by default',
                'text'  => 'CSRF on every form, escaped output, hardened sessions, verified uploads and safe error pages in production.',
                'link'  => 'security',
            ],
        ],

        'steps_title' => 'From zero to production',
        'steps' => [
            ['cmd' => 'luany new my-app',            'title' => 'Create',  'text' => 'Pick SQLite, MySQL or PostgreSQL. Migrations run and the app works immediately.', 'link' => 'installation'],
            ['cmd' => 'luany make:feature Post',     'title' => 'Scaffold','text' => 'A complete, validated CRUD feature in one command — then shape it your way.',      'link' => 'make-feature'],
            ['cmd' => 'luany dev',                   'title' => 'Iterate', 'text' => 'Live reload for views, CSS injection without a refresh, errors in the editor.',  'link' => 'dev-server'],
            ['cmd' => 'luany view:check',            'title' => 'Ship',    'text' => 'Check every view in CI, cache the routes, deploy behind Nginx or Apache.',        'link' => 'deployment'],
        ],

        'eco_title' => 'The ecosystem',
        'eco_lead'  => 'Independent packages with their own tests and releases. Use the full stack or only the pieces you need.',
        'eco_docs'  => 'Docs',

        'final_title' => 'Start building with Luany.',
        'final_lead'  => 'Install the CLI, create a project and open the docs side by side.',
    ],

    'footer' => [
        'tagline'   => 'The PHP framework with nothing to hide.',
        'docs'      => 'Documentation',
        'ecosystem' => 'Ecosystem',
        'community' => 'Community',
        'issues'    => 'Report an issue',
        'license'   => 'Released under the MIT license.',
    ],

    'errors' => [
        '404_title' => 'Page not found',
        '404_text'  => 'This page does not exist — or moved when the docs were reorganised.',
        '500_title' => 'Something went wrong',
        '500_text'  => 'The error was logged. Please try again in a moment.',
        'home'      => 'Back to the docs',
    ],
];
