<?php

return [
    // Checkout of github.com/luany-ecosystem/docs (git submodule at content/).
    'path' => env('DOCS_PATH', base_path('content')),

    'default_locale' => 'en',
    'default_page'   => 'installation',

    'github' => 'https://github.com/luany-ecosystem',
    'edit_url' => 'https://github.com/luany-ecosystem/docs/edit/main/{locale}/{slug}.md',

    // Ecosystem shown on the landing page — update the versions on release.
    'packages' => [
        ['name' => 'luany/framework', 'version' => '1.4', 'repo' => 'luany-framework', 'docs' => 'request-lifecycle',
         'desc' => ['en' => 'Container, HTTP kernel, validation, sessions, errors and helpers.', 'pt' => 'Container, kernel HTTP, validação, sessões, erros e helpers.']],
        ['name' => 'luany/core', 'version' => '1.4', 'repo' => 'luany-core', 'docs' => 'routing',
         'desc' => ['en' => 'Router, middleware pipeline, HTTP messages and PSR-7/15/17 bridges.', 'pt' => 'Router, pipeline de middleware, mensagens HTTP e pontes PSR-7/15/17.']],
        ['name' => 'luany/lte', 'version' => '1.3', 'repo' => 'luany-lte', 'docs' => 'lte-syntax',
         'desc' => ['en' => 'Template engine with a real syntax tree, checked before it runs.', 'pt' => 'Motor de templates com árvore sintática real, verificado antes de executar.']],
        ['name' => 'luany/database', 'version' => '1.8', 'repo' => 'luany-database', 'docs' => 'models',
         'desc' => ['en' => 'Query builder, ORM with relationships and events, portable migrations.', 'pt' => 'Query builder, ORM com relações e eventos, migrations portáveis.']],
        ['name' => 'luany/cli', 'version' => '1.6', 'repo' => 'luany-cli', 'docs' => 'cli',
         'desc' => ['en' => 'New projects, generators, migrations, live reload and view checks.', 'pt' => 'Novos projetos, geradores, migrations, live reload e verificação de vistas.']],
        ['name' => 'LTE for VS Code', 'version' => '1.2', 'repo' => 'luany-lte-vscode', 'docs' => 'vscode',
         'desc' => ['en' => 'Highlighting, snippets, the Luany file icon and errors in the editor.', 'pt' => 'Realce, snippets, o ícone Luany e erros no editor.']],
    ],
];
