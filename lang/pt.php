<?php

return [
    'ui' => [
        'docs'               => 'Documentação',
        'search'             => 'Pesquisar',
        'search_placeholder' => 'Pesquisar na documentação…',
        'search_empty'       => 'Sem resultados para',
        'search_hint'        => 'para navegar',
        'menu'               => 'Menu',
        'theme_light'        => 'Mudar para tema claro',
        'theme_dark'         => 'Mudar para tema escuro',
        'language'           => 'Idioma',
        'previous'           => 'Anterior',
        'next'               => 'Seguinte',
        'edit'               => 'Editar esta página no GitHub',
        'on_this_page'       => 'Nesta página',
        'copy'               => 'Copiar',
        'copied'             => 'Copiado',
        'skip'               => 'Saltar para o conteúdo',
    ],

    'nav' => [
        ['label' => 'Começar',     'slug' => 'installation'],
        ['label' => 'Rotas',       'slug' => 'routing'],
        ['label' => 'Templates',   'slug' => 'lte-syntax'],
        ['label' => 'Base de dados', 'slug' => 'models'],
        ['label' => 'CLI',         'slug' => 'cli'],
    ],

    'landing' => [
        'meta_title'       => 'Luany — o framework PHP sem caixas negras',
        'meta_description' => 'O Luany é um framework PHP com ciclo de vida explícito, templates verificados antes de executar, um ORM completo e uma CLI que gera features inteiras.',

        'eyebrow'   => 'Luany 1.x · PHP 8.2+ · MIT',
        'title_a'   => 'O framework PHP',
        'title_b'   => 'sem caixas negras.',
        'lead'      => 'Ciclo de vida explícito, templates verificados antes de executar, um ORM completo e uma CLI que gera features inteiras — para entregares depressa e saberes sempre o que a tua aplicação está a fazer.',
        'cta'       => 'Começar',
        'cta_alt'   => 'Ver no GitHub',
        'install'   => 'Instalar',

        'stage_route'    => 'routes/http.php',
        'stage_view'     => 'views/pages/posts/index.lte',
        'stage_check'    => 'luany view:check',
        'stage_ok'       => '24 vistas · 0 problemas',

        'strip' => 'Um ecossistema, lançado em conjunto',

        'code_title'  => 'Tudo o que uma aplicação web precisa. Nada que não consigas ler.',
        'code_lead'   => 'Cada camada é pequena, documentada e substituível — do router ao ORM. Lês o código numa tarde; constróis sobre ele durante anos.',
        'code_points' => [
            'Rotas com grupos, nomes, model binding e parâmetros numéricos',
            'Templates LTE compilados para PHP e verificados antes de executar',
            'ORM com relações, eager loading, eventos e soft deletes',
            'Migrations portáveis entre SQLite, MySQL e PostgreSQL',
            'Validação com regras de base de dados e de upload de ficheiros',
        ],
        'code_cta'    => 'Explorar a documentação',
        'tabs' => [
            'routing'    => 'Rotas',
            'controller' => 'Controllers',
            'view'       => 'Templates',
            'model'      => 'ORM',
            'migration'  => 'Migrations',
            'cli'        => 'CLI',
        ],

        'features_title' => 'Feito para ser compreendido',
        'features_lead'  => 'O que trava as equipas — magia, surpresas, erros tardios — ficou de fora por desenho.',
        'features' => [
            'lifecycle' => [
                'title' => 'Um ciclo de vida que se segue',
                'text'  => 'Bootstrap, boot, handle, send, terminate. Cada pedido percorre o mesmo pipeline explícito — sem hooks escondidos.',
                'link'  => 'request-lifecycle',
            ],
            'templates' => [
                'title' => 'Templates que falham cedo',
                'text'  => 'O LTE constrói uma árvore sintática real e valida o PHP lá dentro. Blocos por fechar e gralhas aparecem na compilação — e no editor.',
                'link'  => 'lte-errors',
            ],
            'scaffold' => [
                'title' => 'Um comando, uma feature inteira',
                'text'  => 'O make:feature escreve o modelo, a migration, o controller com validação, quatro vistas e as rotas. Editas, não repetes boilerplate.',
                'link'  => 'make-feature',
            ],
            'orm' => [
                'title' => 'Um ORM que fala de relações',
                'text'  => 'hasMany, belongsToMany, relações polimórficas e through, eager loading sem N+1, casts, eventos e observers.',
                'link'  => 'relationships',
            ],
            'standards' => [
                'title' => 'Padrões lá dentro',
                'text'  => 'Pontes PSR-7, PSR-11, PSR-15 e PSR-17: traz o middleware e as bibliotecas em que já confias.',
                'link'  => 'psr',
            ],
            'security' => [
                'title' => 'Seguro por omissão',
                'text'  => 'CSRF em todos os formulários, output escapado, sessões robustas, uploads verificados e páginas de erro seguras em produção.',
                'link'  => 'security',
            ],
        ],

        'steps_title' => 'Do zero à produção',
        'steps' => [
            ['cmd' => 'luany new my-app',            'title' => 'Criar',     'text' => 'Escolhe SQLite, MySQL ou PostgreSQL. As migrations correm e a aplicação funciona de imediato.', 'link' => 'installation'],
            ['cmd' => 'luany make:feature Post',     'title' => 'Gerar',     'text' => 'Uma feature CRUD completa e validada num só comando — depois moldas à tua maneira.',          'link' => 'make-feature'],
            ['cmd' => 'luany dev',                   'title' => 'Iterar',    'text' => 'Live reload das vistas, CSS injetado sem recarregar, erros diretamente no editor.',          'link' => 'dev-server'],
            ['cmd' => 'luany view:check',            'title' => 'Publicar',  'text' => 'Verifica todas as vistas no CI, guarda as rotas em cache e faz deploy com Nginx ou Apache.',  'link' => 'deployment'],
        ],

        'eco_title' => 'O ecossistema',
        'eco_lead'  => 'Pacotes independentes, com testes e releases próprios. Usa a stack completa ou só as peças de que precisas.',
        'eco_docs'  => 'Documentação',

        'final_title' => 'Começa a construir com o Luany.',
        'final_lead'  => 'Instala a CLI, cria um projeto e abre a documentação ao lado.',
    ],

    'footer' => [
        'tagline'   => 'O framework PHP sem caixas negras.',
        'docs'      => 'Documentação',
        'ecosystem' => 'Ecossistema',
        'community' => 'Comunidade',
        'issues'    => 'Reportar um problema',
        'license'   => 'Distribuído sob a licença MIT.',
    ],

    'errors' => [
        '404_title' => 'Página não encontrada',
        '404_text'  => 'Esta página não existe — ou mudou de sítio quando a documentação foi reorganizada.',
        '500_title' => 'Algo correu mal',
        '500_text'  => 'O erro ficou registado. Tenta novamente daqui a pouco.',
        'home'      => 'Voltar à documentação',
    ],
];
