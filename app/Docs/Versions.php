<?php

namespace App\Docs;

/**
 * Live ecosystem versions for the landing page, menus and footer.
 *
 * Composer packages come from Packagist, the VS Code extension from the
 * Marketplace. Results are cached (storage/cache/versions.json) and never
 * fetched while a visitor waits: a stale cache is served as is and refreshed
 * after the response is sent. Until the first refresh — or when a source is
 * unreachable — the versions in config/docs.php are used.
 */
final class Versions
{
    private const PACKAGIST   = 'https://repo.packagist.org/p2/%s.json';
    private const MARKETPLACE = 'https://marketplace.visualstudio.com/_apis/public/gallery/extensionquery';

    private bool $scheduled = false;

    public function __construct(
        private readonly string $cacheFile,
        private readonly bool $live = true,
        private readonly int $ttl = 21600,
    ) {}

    /**
     * @param  list<array<string, mixed>> $packages config('docs.packages')
     * @return list<array<string, mixed>>           same entries, 'version' updated
     */
    public function packages(array $packages): array
    {
        if (!$this->live) {
            return $packages;
        }

        $cache = $this->read();
        if ((time() - (int) ($cache['fetched_at'] ?? 0)) >= $this->ttl) {
            $this->scheduleRefresh($packages);
        }

        foreach ($packages as &$package) {
            $version = $cache['versions'][self::key($package)] ?? null;
            if (is_string($version) && $version !== '') {
                $package['version'] = $version;
            }
        }

        return $packages;
    }

    /**
     * Fetch every source now and write the cache (also used by tests/CLI).
     *
     * @param list<array<string, mixed>> $packages
     * @param (callable(string, ?string): ?string)|null $http  url, POST body → response body
     */
    public function refresh(array $packages, ?callable $http = null): void
    {
        $old = $this->read()['versions'] ?? [];
        $versions = is_array($old) ? $old : [];

        // One request per package: [url, POST body or null].
        $requests = [];
        foreach ($packages as $package) {
            $requests[self::key($package)] = isset($package['marketplace'])
                ? [self::MARKETPLACE, (string) json_encode(['filters' => [['criteria' => [['filterType' => 7, 'value' => $package['marketplace']]]]], 'flags' => 1])]
                : [sprintf(self::PACKAGIST, $package['name']), null];
        }

        $bodies = $http !== null
            ? array_map(fn (array $r) => $http($r[0], $r[1]), $requests)
            : self::fetchAll($requests);

        foreach ($packages as $package) {
            $key = self::key($package);
            $found = isset($package['marketplace'])
                ? self::fromMarketplace($bodies[$key] ?? null)
                : self::fromPackagist($bodies[$key] ?? null, (string) $package['name']);
            if ($found !== null) {
                $versions[$key] = $found;
            }
        }

        $this->write(['fetched_at' => time(), 'versions' => $versions]);
    }

    /**
     * All requests in parallel (curl_multi), sequentially without curl.
     *
     * @param  array<string, array{0: string, 1: ?string}> $requests
     * @return array<string, ?string>
     */
    private static function fetchAll(array $requests): array
    {
        if (!function_exists('curl_multi_init')) {
            return array_map(fn (array $r) => self::http($r[0], $r[1]), $requests);
        }

        $multi = curl_multi_init();
        $handles = [];
        foreach ($requests as $key => [$url, $post]) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 4, CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_USERAGENT => 'docs.luany.dev', CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => $post !== null
                    ? ['Content-Type: application/json', 'Accept: application/json;api-version=7.2-preview.1']
                    : ['Accept: application/json'],
            ]);
            if ($post !== null) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            curl_multi_add_handle($multi, $ch);
            $handles[$key] = $ch;
        }

        do {
            $status = curl_multi_exec($multi, $running);
            if ($running) {
                curl_multi_select($multi, 1.0);
            }
        } while ($running && $status === CURLM_OK);

        $bodies = [];
        foreach ($handles as $key => $ch) {
            $body = curl_multi_getcontent($ch);
            $bodies[$key] = is_string($body) && $body !== '' && curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200 ? $body : null;
            curl_multi_remove_handle($multi, $ch);
        }
        curl_multi_close($multi);

        return $bodies;
    }

    /** Latest stable "major.minor" from Packagist p2 metadata. */
    public static function fromPackagist(?string $json, string $name): ?string
    {
        $data = $json !== null ? json_decode($json, true) : null;
        $best = null;
        foreach ((array) ($data['packages'][$name] ?? []) as $entry) {
            $v = ltrim((string) ($entry['version'] ?? ''), 'v');
            if (preg_match('/^\d+\.\d+\.\d+$/', $v) && ($best === null || version_compare($v, $best, '>'))) {
                $best = $v;
            }
        }

        return $best === null ? null : implode('.', array_slice(explode('.', $best), 0, 2));
    }

    /** Latest "major.minor" from a Marketplace extension query. */
    public static function fromMarketplace(?string $json): ?string
    {
        $data = $json !== null ? json_decode($json, true) : null;
        $v = (string) ($data['results'][0]['extensions'][0]['versions'][0]['version'] ?? '');

        return preg_match('/^(\d+\.\d+)\.\d+$/', $v, $m) ? $m[1] : null;
    }

    /** @param array<string, mixed> $package */
    private static function key(array $package): string
    {
        return (string) ($package['marketplace'] ?? $package['name']);
    }

    /** @param list<array<string, mixed>> $packages */
    private function scheduleRefresh(array $packages): void
    {
        if ($this->scheduled) {
            return;
        }
        $this->scheduled = true;

        // Claim the refresh so concurrent requests don't all fetch.
        $cache = $this->read();
        $cache['fetched_at'] = time();
        $this->write($cache);

        register_shutdown_function(function () use ($packages): void {
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();   // the visitor already has the page
            }
            try {
                $this->refresh($packages);
            } catch (\Throwable) {
                // keep the cached/config versions
            }
        });
    }

    private static function http(string $url, ?string $post): ?string
    {
        $headers = "User-Agent: docs.luany.dev\r\nAccept: application/json" . ($post !== null ? ";api-version=7.2-preview.1\r\nContent-Type: application/json" : '') . "\r\n";
        $context = stream_context_create(['http' => array_filter([
            'method' => $post !== null ? 'POST' : 'GET', 'header' => $headers, 'content' => $post,
            'timeout' => 3, 'ignore_errors' => true,
        ], fn ($v) => $v !== null)]);
        $body = @file_get_contents($url, false, $context);

        return is_string($body) && $body !== '' ? $body : null;
    }

    /** @return array{fetched_at?: int, versions?: array<string, string>} */
    private function read(): array
    {
        $data = is_file($this->cacheFile) ? json_decode((string) @file_get_contents($this->cacheFile), true) : null;

        return is_array($data) ? $data : [];
    }

    /** @param array<string, mixed> $data */
    private function write(array $data): void
    {
        if (!is_dir(dirname($this->cacheFile))) {
            @mkdir(dirname($this->cacheFile), 0755, true);
        }
        $tmp = $this->cacheFile . '.' . bin2hex(random_bytes(4)) . '.tmp';
        if (@file_put_contents($tmp, (string) json_encode($data)) !== false) {
            @rename($tmp, $this->cacheFile);
        }
    }
}
