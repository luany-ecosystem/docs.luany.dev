# docs.luany.dev

The documentation site for the [Luany Framework](https://github.com/luany-ecosystem), built with Luany itself.

The pages live in a separate repository, [luany-ecosystem/docs](https://github.com/luany-ecosystem/docs)
(Markdown + `manifest.json`). This repository only renders them: navigation, search, English and
Portuguese, light and dark themes.

## Running locally

```bash
git clone https://github.com/luany-ecosystem/docs.luany.dev.git
git clone https://github.com/luany-ecosystem/docs.git          # next to it
cd docs.luany.dev
composer install
cp .env.example .env
echo "DOCS_PATH=../docs" >> .env
luany serve                                                       # http://localhost:8000
```

Edits to the Markdown show up on the next request — rendered pages are cached in
`storage/cache/docs`, keyed by each file's modification time.

## Checking the content

```bash
php bin/check-content.php ../docs
```

Renders every page in the manifest and fails on missing English pages, links to pages that do
not exist and anchors that point to no heading. Untranslated pages are listed as warnings.
CI runs it against the `main` branch of the docs repository.

## How it works

| Path | Role |
| --- | --- |
| `app/Docs/Documentation.php` | Reads the manifest, renders Markdown (CommonMark + GFM), heading ids, outline, link rewriting, callouts, search index, cache |
| `app/Controllers/DocsController.php` | `/` → preferred language, `/{locale}`, `/{locale}/docs/{slug}`, `/{locale}/search.json` |
| `views/` | LTE layout, landing page, doc page, error pages |
| `public/assets/` | Styles, theme toggle, search (`/` or Ctrl/Cmd + K), copy buttons, highlight.js |

## Deploying

1. Clone this repository and the docs repository on the server, `composer install --no-dev -o`.
2. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://docs.luany.dev`,
   `DOCS_PATH=/path/to/docs`.
3. Point the web server's document root at `public/` (see the
   [deployment guide](https://docs.luany.dev/en/docs/deployment)).
4. To publish new docs: `git -C /path/to/docs pull` — no restart or cache clear needed.

## License

MIT
