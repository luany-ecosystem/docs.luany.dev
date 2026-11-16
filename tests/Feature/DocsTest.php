<?php

namespace Tests\Feature;

use Luany\Core\Http\Request;
use Luany\Core\Http\Response;
use Luany\Framework\Http\Kernel;
use PHPUnit\Framework\TestCase;

class DocsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // Rendered pages are cached by mtime; start from a clean cache.
        foreach (glob(dirname(__DIR__, 2) . '/storage/cache/docs/*') ?: [] as $file) {
            @unlink($file);
        }
    }

    /** @param array<string, string> $headers */
    private function get(string $uri, array $headers = []): Response
    {
        $app    = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $kernel = $app->make(Kernel::class);
        $kernel->boot();

        return $kernel->handle(new Request('GET', $uri, headers: $headers, server: ['REMOTE_ADDR' => '127.0.0.1']));
    }

    public function test_root_redirects_to_the_preferred_locale(): void
    {
        $this->assertSame('/en', $this->get('/')->getHeaders()['Location'] ?? null);
        $this->assertSame('/pt', $this->get('/', ['Accept-Language' => 'pt-PT,pt;q=0.9,en;q=0.8'])->getHeaders()['Location'] ?? null);
    }

    public function test_landing_and_docs_index(): void
    {
        $landing = $this->get('/en');
        $this->assertSame(200, $landing->getStatusCode());
        $this->assertStringContainsString('/en/docs/installation', $landing->getBody());

        $this->assertSame('/pt/docs/installation', $this->get('/pt/docs')->getHeaders()['Location'] ?? null);
    }

    public function test_page_renders_markdown_with_navigation_outline_and_pager(): void
    {
        $response = $this->get('/en/docs/installation');
        $html     = $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('id="requirements"', $html);
        $this->assertStringContainsString('id="requirements-2"', $html, 'duplicate headings get unique ids');
        $this->assertStringContainsString('href="/en/docs/routing#route-parameters"', $html, '.md links are rewritten');
        $this->assertStringContainsString('target="_blank" rel="noopener"', $html);
        $this->assertStringContainsString('callout callout-tip', $html);
        $this->assertStringContainsString('class="table-wrap"', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html, 'raw HTML is escaped');
        $this->assertStringContainsString('Routing', $html, 'sidebar / next link');
        $this->assertStringContainsString('github.com/luany-ecosystem/docs/edit/main/en/installation.md', $html);
    }

    public function test_page_is_served_from_cache_on_second_request(): void
    {
        $this->get('/en/docs/routing');
        $this->assertNotEmpty(glob(dirname(__DIR__, 2) . '/storage/cache/docs/en-routing-*.php'));
        $this->assertStringContainsString('Route parameters', $this->get('/en/docs/routing')->getBody());
    }

    public function test_portuguese_titles_and_callouts(): void
    {
        $html = $this->get('/pt/docs/installation')->getBody();
        $this->assertStringContainsString('Instalação', $html);
        $this->assertStringContainsString('Primeiros Passos', $html);
        $this->assertStringContainsString('callout callout-note', $html);
        $this->assertStringContainsString('id="configuração-do-servidor"', $html, 'anchors keep accented letters');
        $this->assertStringContainsString('id="base_path"', $this->get('/en/docs/routing')->getBody(), 'anchors keep underscores');
    }

    public function test_unknown_locale_slug_and_missing_translation_are_404(): void
    {
        $this->assertSame(404, $this->get('/fr')->getStatusCode());
        $this->assertSame(404, $this->get('/en/docs/nope')->getStatusCode());
        $this->assertSame(404, $this->get('/en/docs/..%2Fmanifest')->getStatusCode());
        $this->assertSame(404, $this->get('/pt/docs/routing')->getStatusCode(), 'page listed but not translated');
    }

    public function test_search_index(): void
    {
        $response = $this->get('/en/search.json');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringStartsWith('application/json', $response->getHeaders()['Content-Type']);

        $index = json_decode($response->getBody(), true);
        $urls  = array_column($index, 'url');
        $this->assertContains('/en/docs/installation', $urls);
        $this->assertContains('/en/docs/routing#route-parameters', $urls);

        $this->assertNotEmpty(glob(dirname(__DIR__, 2) . '/storage/cache/docs/search-en-*.php'), 'index is cached');
        $this->assertSame($response->getBody(), $this->get('/en/search.json')->getBody(), 'cached index is identical');
    }
}
