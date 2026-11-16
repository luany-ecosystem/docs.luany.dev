<?php

namespace App\Controllers;

use App\Docs\Documentation;
use Luany\Core\Http\Request;
use Luany\Core\Http\Response;

class DocsController extends Controller
{
    public function __construct(private readonly Documentation $docs) {}

    /** "/" → the visitor's language (Accept-Language), default English. */
    public function home(Request $request): Response
    {
        return redirect('/' . $this->preferredLocale($request));
    }

    public function landing(string $locale): Response|string
    {
        if (!$this->docs->hasLocale($locale)) {
            abort(404);
        }

        return view('pages.landing', $this->shared($locale) + ['first' => $this->docs->pages($locale)[0] ?? null]);
    }

    public function index(string $locale): Response
    {
        if (!$this->docs->hasLocale($locale)) {
            abort(404);
        }

        return redirect("/{$locale}/docs/" . config('docs.default_page'));
    }

    public function show(string $locale, string $slug): string
    {
        if (!$this->docs->exists($locale, $slug)) {
            abort(404);
        }

        $page = $this->docs->page($locale, $slug);

        return view('pages.doc', $this->shared($locale) + [
            'page'    => $page,
            'editUrl' => str_replace(['{locale}', '{slug}'], [$locale, $slug], (string) config('docs.edit_url')),
        ]);
    }

    public function search(string $locale): Response
    {
        if (!$this->docs->hasLocale($locale)) {
            abort(404);
        }

        return response(json_encode($this->docs->searchIndex($locale), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]')
            ->header('Content-Type', 'application/json; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=600');
    }

    /** @return array<string, mixed> */
    private function shared(string $locale): array
    {
        return [
            'locale'     => $locale,
            'locales'    => $this->docs->locales(),
            'version'    => $this->docs->version(),
            'navigation' => $this->docs->navigation($locale),
        ];
    }

    private function preferredLocale(Request $request): string
    {
        // Accept-Language: "pt-PT,pt;q=0.9,en;q=0.8" — highest q first, header order breaks ties.
        $ranked = [];
        foreach (explode(',', strtolower((string) $request->header('Accept-Language', ''))) as $i => $part) {
            [$tag, $params] = array_pad(explode(';', trim($part), 2), 2, '');
            $q = preg_match('/q=([0-9.]+)/', $params, $m) === 1 ? (float) $m[1] : 1.0;
            $code = explode('-', trim($tag))[0];
            if ($code !== '' && $q > 0) {
                $ranked[] = [$q, -$i, $code];
            }
        }
        rsort($ranked);

        foreach ($ranked as [, , $code]) {
            if ($this->docs->hasLocale($code)) {
                return $code;
            }
        }

        return (string) config('docs.default_locale');
    }
}
