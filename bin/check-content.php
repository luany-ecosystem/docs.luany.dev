<?php

/**
 * Checks the documentation checkout: every page in manifest.json renders,
 * and every internal link points to an existing page and heading.
 *
 *   php bin/check-content.php [path]      (default: DOCS_PATH or content/)
 *
 * Missing pages in the default locale are errors; untranslated pages in
 * other locales are reported as warnings. Exit code 1 on any error.
 */

use App\Docs\Documentation;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);
$path = $argv[1] ?? (getenv('DOCS_PATH') ?: 'content');
if ($path[0] !== '/') {
    // Relative to the current directory first, then to the project root.
    $path = is_dir(getcwd() . '/' . $path) ? getcwd() . '/' . $path : $root . '/' . $path;
}

$cache = sys_get_temp_dir() . '/luany-docs-check-' . getmypid();
$docs  = new Documentation(rtrim($path, '/'), $cache);

$errors = $warnings = [];
$rendered = [];

foreach (array_keys($docs->locales()) as $i => $locale) {
    foreach ($docs->pages($locale) as $page) {
        if (!$docs->exists($locale, $page['slug'])) {
            if ($i === 0) {
                $errors[] = "{$locale}/{$page['slug']}.md is missing";
            } else {
                $warnings[] = "{$locale}/{$page['slug']}.md is not translated yet";
            }
            continue;
        }
        $rendered[$locale][$page['slug']] = $docs->page($locale, $page['slug']);
    }
}

foreach ($rendered as $locale => $pages) {
    foreach ($pages as $slug => $page) {
        preg_match_all('#href="/' . $locale . '/docs/([a-z0-9-]+)(?:\#([^"]*))?"#', $page['html'], $links, PREG_SET_ORDER);
        preg_match_all('#href="\#([^"]+)"#', $page['html'], $local);

        foreach ($links as $link) {
            [$target, $anchor] = [$link[1], rawurldecode($link[2] ?? '')];
            if (!isset($pages[$target])) {
                $errors[] = "{$locale}/{$slug}.md links to missing page {$target}.md";
            } elseif ($anchor !== '' && !in_array($anchor, array_column($pages[$target]['outline'], 'id'), true)) {
                $errors[] = "{$locale}/{$slug}.md links to {$target}.md#{$anchor}, heading not found";
            }
        }
        foreach (array_map('rawurldecode', $local[1]) as $anchor) {
            if (!in_array($anchor, array_column($page['outline'], 'id'), true)) {
                $errors[] = "{$locale}/{$slug}.md links to #{$anchor}, heading not found";
            }
        }
        if (preg_match_all('#href="(?!https?:|/|\#|mailto:)([^"]+)"#', $page['html'], $odd)) {
            foreach ($odd[1] as $href) {
                $errors[] = "{$locale}/{$slug}.md has an unresolved link: {$href}";
            }
        }
    }
}

array_map('unlink', glob($cache . '/*') ?: []);
@rmdir($cache);

foreach ($warnings as $w) {
    echo "  warn   {$w}\n";
}
foreach ($errors as $e) {
    echo "  error  {$e}\n";
}

$count = array_sum(array_map('count', $rendered));
echo "\n  {$count} pages rendered, " . count($errors) . ' error(s), ' . count($warnings) . " warning(s)\n";
exit($errors === [] ? 0 : 1);
