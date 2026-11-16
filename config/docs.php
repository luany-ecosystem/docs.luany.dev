<?php

return [
    // Checkout of github.com/luany-ecosystem/docs (git submodule at content/).
    'path' => env('DOCS_PATH', base_path('content')),

    'default_locale' => 'en',
    'default_page'   => 'installation',

    'github' => 'https://github.com/luany-ecosystem',
    'edit_url' => 'https://github.com/luany-ecosystem/docs/edit/main/{locale}/{slug}.md',
];
