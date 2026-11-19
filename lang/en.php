<?php

return [
    'ui' => [
        'docs'               => 'Docs',
        'search'             => 'Search',
        'search_placeholder' => 'Search the documentation…',
        'search_empty'       => 'No results for',
        'search_hint'        => 'to navigate',
        'menu'               => 'Menu',
        'theme'              => 'Appearance',
        'theme_system'       => 'System',
        'theme_light'        => 'Light',
        'theme_dark'         => 'Dark',
        'language'           => 'Language',
        'previous'           => 'Previous',
        'next'               => 'Next',
        'edit'               => 'Edit this page on GitHub',
        'on_this_page'       => 'On this page',
        'copy'               => 'Copy',
        'copied'             => 'Copied',
        'skip'               => 'Skip to content',
        'get_started'        => 'Get started',
        'all_docs'           => 'All documentation',
        'ecosystem'          => 'Ecosystem',
        'community'          => 'Community',
    ],

    'nav' => [
        'docs'         => 'Documentation',
        'ecosystem'    => 'Ecosystem',
        'github'       => 'GitHub',
        'open_source'  => 'Open source',
        'github_desc'  => 'Code, issues and releases',
        'packagist'    => 'Packagist',
        'packagist_desc' => 'Composer packages',
        'marketplace'  => 'VS Code Marketplace',
        'marketplace_desc' => 'LTE extension',
    ],

    'landing' => [
        'meta_title'       => 'Luany — the PHP framework with nothing to hide',
        'meta_description' => 'Luany is a PHP framework with an explicit request lifecycle, templates checked before they run, a complete ORM and a CLI that scaffolds whole features.',

        'title'     => 'The PHP framework with nothing',
        'accent'    => 'to hide.',
        'lead'      => 'An explicit request lifecycle, templates checked before they run, a complete ORM and a CLI that scaffolds whole features.',
        'cta'       => 'Get started',
        'cta_alt'   => 'View on GitHub',

        'mk_problems' => '0 problems',
        'mk_toast'    => 'view:check passed',
        'mk_toast_sub'=> '24 views · 0 problems',

        'marquee' => ['Routing', 'Middleware', 'LTE templates', 'ORM', 'Migrations', 'Validation', 'Sessions', 'CSRF', 'PSR-7 · PSR-15', 'CLI', 'Live reload', 'VS Code'],

        'pillars_eyebrow' => 'The framework',
        'pillars_title'   => 'From HTTP request to database, without magic.',
        'pillars_lead'    => 'Three small, documented, replaceable layers. Read the source in an afternoon — build on it for years.',
        'pillars' => [
            ['key' => 'http', 'title' => 'HTTP', 'text' => 'Routes, middleware and controllers in one explicit pipeline, from request to response.',
             'links' => [['Routing', 'routing'], ['Middleware', 'middleware'], ['Controllers', 'controllers'], ['Validation', 'validation']]],
            ['key' => 'views', 'title' => 'Views', 'text' => 'LTE templates with a real syntax tree, checked before they run.',
             'links' => [['Template syntax', 'lte-syntax'], ['Layouts & components', 'lte-layouts'], ['Directives reference', 'lte-directives'], ['Template errors', 'lte-errors']]],
            ['key' => 'data', 'title' => 'Data', 'text' => 'Query builder, an ORM with relationships and events, portable migrations.',
             'links' => [['Query builder', 'query-builder'], ['Models', 'models'], ['Relationships', 'relationships'], ['Migrations', 'migrations']]],
        ],

        'tools_eyebrow' => 'Tooling',
        'tools_title'   => 'Tools that do the work for you.',
        'tools_lead'    => 'From the first command to CI: scaffold, check and fix without leaving your flow.',
        'tools' => [
            'cli'    => ['chip' => 'luany/cli 1.7', 'title' => 'Luany CLI', 'text' => 'Creates projects, scaffolds complete features, runs migrations and the dev server with live reload.', 'slug' => 'cli', 'link' => 'CLI commands'],
            'vscode' => ['chip' => 'Extension 1.2', 'title' => 'LTE for VS Code', 'text' => 'Highlighting, snippets, the Luany icon on .lte files and errors right in the editor.', 'slug' => 'vscode', 'link' => 'Install the extension'],
            'ci'     => ['chip' => 'GitHub Actions', 'title' => 'Views checked in CI', 'text' => 'luany view:check compiles every view and annotates each error on the right line of the pull request.', 'slug' => 'view-check', 'link' => 'Set up view:check'],
        ],

        'process_eyebrow' => 'Getting started',
        'process_title'   => 'From zero to production, in one clear flow.',
        'steps' => [
            ['cmd' => 'luany new my-app',        'title' => 'Create',   'text' => 'Pick SQLite, MySQL or PostgreSQL. Migrations run and the app works immediately.'],
            ['cmd' => 'luany make:feature Post', 'title' => 'Scaffold', 'text' => 'Model, migration, validated controller, four views and routes — in one command.'],
            ['cmd' => 'luany dev',               'title' => 'Iterate',  'text' => 'Live reload for views, CSS injected without a refresh, errors in the editor.'],
            ['cmd' => 'luany view:check',        'title' => 'Ship',     'text' => 'Views checked in CI, routes cached, deployed behind Nginx or Apache.'],
        ],

        'stats' => [
            ['value' => 1622, 'text' => 'automated tests across the five ecosystem packages.'],
            ['value' => 3,    'text' => 'databases — SQLite, MySQL and PostgreSQL — tested on every change.'],
            ['value' => 76,   'text' => 'documentation pages, in English and Portuguese.'],
        ],

        'eco_eyebrow' => 'Ecosystem',
        'eco_title'   => 'Independent packages, released together.',
        'eco_docs'    => 'Docs',

        'final_title' => 'Build your next application with Luany.',
        'final_lead'  => 'Install the CLI, create a project and open the docs side by side.',
    ],

    'footer' => [
        'tagline'    => 'The PHP framework with nothing to hide — an explicit lifecycle, checked templates and a productive CLI.',
        'built_with' => 'This site is built with Luany',
        'docs'       => 'Documentation',
        'ecosystem'  => 'Ecosystem',
        'community'  => 'Community',
        'issues'     => 'Report an issue',
        'license'    => 'Released under the MIT license.',
        'maker'      => 'A SysCommFlow product · Luanda, Angola',
    ],

    'errors' => [
        '404_title' => 'Page not found',
        '404_text'  => 'This page does not exist — or moved when the docs were reorganised.',
        '500_title' => 'Something went wrong',
        '500_text'  => 'The error was logged. Please try again in a moment.',
        'home'      => 'Back home',
    ],
];
