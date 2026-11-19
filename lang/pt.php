<?php

return [
    'ui' => [
        'docs'               => 'Documentação',
        'search'             => 'Pesquisar',
        'search_placeholder' => 'Pesquisar na documentação…',
        'search_empty'       => 'Sem resultados para',
        'search_hint'        => 'para navegar',
        'menu'               => 'Menu',
        'theme'              => 'Aparência',
        'theme_system'       => 'Sistema',
        'theme_light'        => 'Claro',
        'theme_dark'         => 'Escuro',
        'language'           => 'Idioma',
        'previous'           => 'Anterior',
        'next'               => 'Seguinte',
        'edit'               => 'Editar esta página no GitHub',
        'on_this_page'       => 'Nesta página',
        'copy'               => 'Copiar',
        'copied'             => 'Copiado',
        'skip'               => 'Saltar para o conteúdo',
        'get_started'        => 'Começar',
        'all_docs'           => 'Toda a documentação',
        'ecosystem'          => 'Ecossistema',
        'community'          => 'Comunidade',
    ],

    'nav' => [
        'docs'         => 'Documentação',
        'ecosystem'    => 'Ecossistema',
        'github'       => 'GitHub',
        'open_source'  => 'Open source',
        'github_desc'  => 'Código, issues e releases',
        'packagist'    => 'Packagist',
        'packagist_desc' => 'Pacotes Composer',
        'marketplace'  => 'VS Code Marketplace',
        'marketplace_desc' => 'Extensão LTE',
    ],

    'landing' => [
        'meta_title'       => 'Luany — o framework PHP sem caixas negras',
        'meta_description' => 'O Luany é um framework PHP com ciclo de vida explícito, templates verificados antes de executar, um ORM completo e uma CLI que gera features inteiras.',

        'title'     => 'O framework PHP sem caixas',
        'accent'    => 'negras.',
        'lead'      => 'Ciclo de vida explícito, templates verificados antes de executar, um ORM completo e uma CLI que gera features inteiras.',
        'cta'       => 'Começar',
        'cta_alt'   => 'Ver no GitHub',

        'mk_problems' => '0 problemas',
        'mk_toast'    => 'view:check concluído',
        'mk_toast_sub'=> '24 vistas · 0 problemas',

        'marquee' => ['Rotas', 'Middleware', 'Templates LTE', 'ORM', 'Migrations', 'Validação', 'Sessões', 'CSRF', 'PSR-7 · PSR-15', 'CLI', 'Live reload', 'VS Code'],

        'pillars_eyebrow' => 'O framework',
        'pillars_title'   => 'Do pedido HTTP à base de dados, sem magia.',
        'pillars_lead'    => 'Três camadas pequenas, documentadas e substituíveis. Lês o código numa tarde — e constróis sobre ele durante anos.',
        'pillars' => [
            ['key' => 'http', 'title' => 'HTTP', 'text' => 'Rotas, middleware e controllers num pipeline explícito, do pedido à resposta.',
             'links' => [['Rotas', 'routing'], ['Middleware', 'middleware'], ['Controllers', 'controllers'], ['Validação', 'validation']]],
            ['key' => 'views', 'title' => 'Vistas', 'text' => 'Templates LTE com árvore sintática real, verificados antes de executar.',
             'links' => [['Sintaxe dos templates', 'lte-syntax'], ['Layouts e componentes', 'lte-layouts'], ['Referência de diretivas', 'lte-directives'], ['Erros de template', 'lte-errors']]],
            ['key' => 'data', 'title' => 'Dados', 'text' => 'Query builder, ORM com relações e eventos, migrations portáveis.',
             'links' => [['Query builder', 'query-builder'], ['Modelos', 'models'], ['Relações', 'relationships'], ['Migrations', 'migrations']]],
        ],

        'tools_eyebrow' => 'Ferramentas',
        'tools_title'   => 'Ferramentas que trabalham por ti.',
        'tools_lead'    => 'Do primeiro comando ao CI: gerar, verificar e corrigir sem sair do teu fluxo.',
        'tools' => [
            'cli'    => ['chip' => 'luany/cli', 'title' => 'Luany CLI', 'text' => 'Cria projetos, gera features completas, corre migrations e o servidor com live reload.', 'slug' => 'cli', 'link' => 'Comandos da CLI'],
            'vscode' => ['chip' => 'Extensão', 'title' => 'LTE para VS Code', 'text' => 'Realce, snippets, o ícone Luany nos ficheiros .lte e erros diretamente no editor.', 'slug' => 'vscode', 'link' => 'Instalar a extensão'],
            'ci'     => ['chip' => 'GitHub Actions', 'title' => 'Vistas verificadas no CI', 'text' => 'O luany view:check compila todas as vistas e anota cada erro na linha certa do pull request.', 'slug' => 'view-check', 'link' => 'Configurar o view:check'],
        ],

        'process_eyebrow' => 'Como começar',
        'process_title'   => 'Do zero à produção, num fluxo claro.',
        'steps' => [
            ['cmd' => 'luany new my-app',        'title' => 'Criar',    'text' => 'Escolhe SQLite, MySQL ou PostgreSQL. As migrations correm e a aplicação funciona de imediato.'],
            ['cmd' => 'luany make:feature Post', 'title' => 'Gerar',    'text' => 'Modelo, migration, controller validado, quatro vistas e rotas — num só comando.'],
            ['cmd' => 'luany dev',               'title' => 'Iterar',   'text' => 'Live reload das vistas, CSS injetado sem recarregar e erros no editor.'],
            ['cmd' => 'luany view:check',        'title' => 'Publicar', 'text' => 'Vistas verificadas no CI, rotas em cache e deploy com Nginx ou Apache.'],
        ],

        'stats' => [
            ['value' => 1622, 'text' => 'testes automatizados nos cinco pacotes do ecossistema.'],
            ['value' => 3,    'text' => 'bases de dados — SQLite, MySQL e PostgreSQL — testadas em cada alteração.'],
            ['value' => 76,   'text' => 'páginas de documentação, em português e em inglês.'],
        ],

        'eco_eyebrow' => 'Ecossistema',
        'eco_title'   => 'Pacotes independentes, lançados em conjunto.',
        'eco_docs'    => 'Documentação',

        'final_title' => 'Constrói a tua próxima aplicação com o Luany.',
        'final_lead'  => 'Instala a CLI, cria um projeto e abre a documentação ao lado.',
    ],

    'footer' => [
        'tagline'    => 'O framework PHP sem caixas negras — ciclo de vida explícito, templates verificados e uma CLI produtiva.',
        'built_with' => 'Este site é feito com Luany',
        'docs'       => 'Documentação',
        'ecosystem'  => 'Ecossistema',
        'community'  => 'Comunidade',
        'issues'     => 'Reportar um problema',
        'license'    => 'Distribuído sob a licença MIT.',
        'maker'      => 'Um produto SysCommFlow · Luanda, Angola',
    ],

    'errors' => [
        '404_title' => 'Página não encontrada',
        '404_text'  => 'Esta página não existe — ou mudou de sítio quando a documentação foi reorganizada.',
        '500_title' => 'Algo correu mal',
        '500_text'  => 'O erro ficou registado. Tenta novamente daqui a pouco.',
        'home'      => 'Voltar ao início',
    ],
];
