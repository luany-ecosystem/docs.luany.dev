<?php

namespace App\Docs;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Reads the documentation checkout (manifest.json + {locale}/{slug}.md),
 * renders pages to HTML and builds navigation, outlines and the search index.
 *
 * Rendered pages are cached in storage/cache/docs, keyed by file and mtime.
 */
final class Documentation
{
    /** @var array<string, mixed>|null */
    private ?array $manifest = null;

    private ?MarkdownConverter $converter = null;

    public function __construct(
        private readonly string $path,
        private readonly string $cachePath,
    ) {}

    // ── Manifest ──────────────────────────────────────────────────────────

    /** @return array<string, string> code => label */
    public function locales(): array
    {
        return $this->manifest()['locales'] ?? ['en' => 'English'];
    }

    public function version(): string
    {
        return (string) ($this->manifest()['version'] ?? '1.x');
    }

    public function hasLocale(string $locale): bool
    {
        return array_key_exists($locale, $this->locales());
    }

    /**
     * Sidebar: sections with their pages, titles in $locale.
     *
     * @return list<array{title: string, pages: list<array{slug: string, title: string}>}>
     */
    public function navigation(string $locale): array
    {
        $nav = [];
        foreach ($this->manifest()['sections'] ?? [] as $section) {
            $pages = [];
            foreach ($section['pages'] as $page) {
                $pages[] = ['slug' => $page['slug'], 'title' => $this->title($page['title'], $locale)];
            }
            $nav[] = ['title' => $this->title($section['title'], $locale), 'pages' => $pages];
        }
        return $nav;
    }

    /**
     * Pages in reading order.
     *
     * @return list<array{slug: string, title: string, section: string}>
     */
    public function pages(string $locale): array
    {
        $flat = [];
        foreach ($this->navigation($locale) as $section) {
            foreach ($section['pages'] as $page) {
                $flat[] = $page + ['section' => $section['title']];
            }
        }
        return $flat;
    }

    public function exists(string $locale, string $slug): bool
    {
        return $this->hasLocale($locale)
            && preg_match('/^[a-z0-9-]+$/', $slug) === 1
            && $this->find($locale, $slug) !== null
            && is_file($this->file($locale, $slug));
    }

    // ── Pages ─────────────────────────────────────────────────────────────

    /**
     * Rendered page: title, HTML, outline (h2/h3), section, prev/next.
     *
     * @return array{slug: string, title: string, section: string, html: string,
     *               outline: list<array{level: int, id: string, text: string}>,
     *               prev: ?array{slug: string, title: string}, next: ?array{slug: string, title: string}}
     */
    public function page(string $locale, string $slug): array
    {
        $file   = $this->file($locale, $slug);
        $cached = $this->cachePath . '/' . $locale . '-' . $slug . '-' . filemtime($file) . '.php';

        if (is_file($cached)) {
            /** @var array{html: string, outline: list<array{level: int, id: string, text: string}>, title: string} $rendered */
            $rendered = require $cached;
        } else {
            $rendered = $this->render((string) file_get_contents($file), $locale);
            $this->store($cached, $rendered);
        }

        $pages = $this->pages($locale);
        $index = array_search($slug, array_column($pages, 'slug'), true);
        $meta  = $pages[$index];

        return [
            'slug'    => $slug,
            'title'   => $meta['title'],
            'section' => $meta['section'],
            'html'    => $rendered['html'],
            'outline' => $rendered['outline'],
            'prev'    => $index > 0 ? array_intersect_key($pages[$index - 1], ['slug' => 1, 'title' => 1]) : null,
            'next'    => $index < count($pages) - 1 ? array_intersect_key($pages[$index + 1], ['slug' => 1, 'title' => 1]) : null,
        ];
    }

    /**
     * Search index for $locale: one entry per page and per h2/h3 section.
     *
     * @return list<array{title: string, page: string, section: string, url: string, text: string}>
     */
    public function searchIndex(string $locale): array
    {
        $index = [];
        foreach ($this->pages($locale) as $page) {
            if (!is_file($this->file($locale, $page['slug']))) {
                continue;
            }
            $markdown = (string) file_get_contents($this->file($locale, $page['slug']));
            $parts    = preg_split('/^(#{2,3})\s+(.+)$/m', $markdown, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$markdown];

            $index[] = [
                'title'   => $page['title'],
                'page'    => $page['title'],
                'section' => $page['section'],
                'url'     => "/{$locale}/docs/{$page['slug']}",
                'text'    => $this->plain($parts[0]),
            ];

            for ($i = 1; $i + 2 < count($parts) + 1; $i += 3) {
                $heading = trim($parts[$i + 1] ?? '');
                if ($heading === '') {
                    continue;
                }
                $index[] = [
                    'title'   => $this->plain($heading),
                    'page'    => $page['title'],
                    'section' => $page['section'],
                    'url'     => "/{$locale}/docs/{$page['slug']}#" . self::anchor($this->plain($heading)),
                    'text'    => $this->plain($parts[$i + 2] ?? ''),
                ];
            }
        }
        return $index;
    }

    // ── Rendering ─────────────────────────────────────────────────────────

    /**
     * @return array{html: string, outline: list<array{level: int, id: string, text: string}>, title: string}
     */
    private function render(string $markdown, string $locale): array
    {
        $html = $this->converter()->convert($markdown)->getContent();

        // Heading ids + outline (h2/h3), unique per page.
        $outline = [];
        $used    = [];
        $title   = '';
        $html = (string) preg_replace_callback('/<h([1-3])>(.*?)<\/h\1>/s', function (array $m) use (&$outline, &$used, &$title) {
            $text = trim(html_entity_decode(strip_tags($m[2]), ENT_QUOTES, 'UTF-8'));
            if ($m[1] === '1') {
                $title = $text;
                return '';   // the layout prints the page title
            }
            $id = self::anchor($text);
            $base = $id;
            for ($n = 2; isset($used[$id]); $n++) {
                $id = "{$base}-{$n}";
            }
            $used[$id] = true;
            $outline[] = ['level' => (int) $m[1], 'id' => $id, 'text' => $text];

            return sprintf('<h%1$d id="%2$s"><a class="anchor" href="#%2$s" aria-hidden="true">#</a>%3$s</h%1$d>', $m[1], $id, $m[2]);
        }, $html);

        // Relative links: routing.md#x → /{locale}/docs/routing#x
        $html = (string) preg_replace_callback('/href="([a-z0-9-]+)\.md(#[^"]*)?"/', fn (array $m) => 'href="/' . $locale . '/docs/' . $m[1] . ($m[2] ?? '') . '"', $html);

        // External links open in a new tab.
        $html = (string) preg_replace('/<a href="(https?:\/\/[^"]+)"/', '<a href="$1" target="_blank" rel="noopener"', $html);

        // > **Note** / **Tip** / **Warning** callouts.
        $html = (string) preg_replace_callback('/<blockquote>\s*<p><strong>(Note|Tip|Warning|Nota|Dica|Aviso)<\/strong>\s*/', function (array $m) {
            $kind = match ($m[1]) {
                'Tip', 'Dica'       => 'tip',
                'Warning', 'Aviso'  => 'warning',
                default             => 'note',
            };
            return '<blockquote class="callout callout-' . $kind . '"><p><strong>' . $m[1] . '</strong> ';
        }, $html);

        // Wrap tables for horizontal scrolling on small screens.
        $html = str_replace(['<table>', '</table>'], ['<div class="table-wrap"><table>', '</table></div>'], $html);

        return ['html' => $html, 'outline' => $outline, 'title' => $title];
    }

    public static function anchor(string $text): string
    {
        // GitHub-style: lowercase, keep letters (any script), digits, _ and -.
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = (string) preg_replace('/[^\p{L}\p{N}_\s-]/u', '', $text);
        $text = (string) preg_replace('/[\s-]+/u', '-', $text);
        return trim($text, '-') ?: 'section';
    }

    private function plain(string $markdown): string
    {
        $text = (string) preg_replace('/```.*?```/s', ' ', $markdown);
        $text = (string) preg_replace('/[`*_>#|\[\]()]/', ' ', $text);
        $text = (string) preg_replace('/\s+/', ' ', $text);
        return mb_substr(trim($text), 0, 300);
    }

    private function converter(): MarkdownConverter
    {
        if ($this->converter === null) {
            $environment = new Environment([
                'html_input'         => 'escape',
                'allow_unsafe_links' => false,
            ]);
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $this->converter = new MarkdownConverter($environment);
        }
        return $this->converter;
    }

    // ── Internals ─────────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function manifest(): array
    {
        if ($this->manifest === null) {
            $file = $this->path . '/manifest.json';
            if (!is_file($file)) {
                throw new \RuntimeException("Documentation manifest not found at {$file}. Run: git submodule update --init");
            }
            $this->manifest = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }
        return $this->manifest;
    }

    /** @return array<string, mixed>|null */
    private function find(string $locale, string $slug): ?array
    {
        foreach ($this->manifest()['sections'] ?? [] as $section) {
            foreach ($section['pages'] as $page) {
                if ($page['slug'] === $slug) {
                    return $page;
                }
            }
        }
        return null;
    }

    private function file(string $locale, string $slug): string
    {
        return $this->path . '/' . $locale . '/' . $slug . '.md';
    }

    /** @param array<string, string> $titles */
    private function title(array $titles, string $locale): string
    {
        return $titles[$locale] ?? $titles['en'] ?? reset($titles) ?: '';
    }

    /** @param array<string, mixed> $data */
    private function store(string $file, array $data): void
    {
        if (!is_dir($this->cachePath)) {
            @mkdir($this->cachePath, 0755, true);
        }
        $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
        if (@file_put_contents($tmp, '<?php return ' . var_export($data, true) . ';') !== false) {
            @rename($tmp, $file);
        }
    }
}
