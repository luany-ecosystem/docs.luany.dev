<?php

/**
 * Site helpers — loaded by AppServiceProvider::boot().
 */

if (!function_exists('icon')) {
    /**
     * Inline SVG icon: {!! icon('arrow', 16, 2) !!}
     */
    function icon(string $name, int|float $size = 22, int|float $stroke = 1.8, string $class = ''): string
    {
        static $icons = null;
        $icons ??= require __DIR__ . '/icons.php';
        $cls = $class !== '' ? ' class="' . e($class) . '"' : '';

        return '<svg' . $cls . ' width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="'
            . $stroke . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($icons[$name] ?? '') . '</svg>';
    }
}

if (!function_exists('asset_v')) {
    /**
     * Asset URL with a cache-busting version (file mtime).
     */
    function asset_v(string $path): string
    {
        $mtime = @filemtime(base_path('public/assets/' . ltrim($path, '/')));

        return asset($path) . ($mtime ? '?v=' . $mtime : '');
    }
}

if (!function_exists('ecosystem')) {
    /**
     * config('docs.packages') with live versions (see App\Docs\Versions).
     *
     * @return list<array<string, mixed>>
     */
    function ecosystem(): array
    {
        static $packages = null;

        return $packages ??= app(\App\Docs\Versions::class)->packages((array) config('docs.packages', []));
    }
}

if (!function_exists('collect_version')) {
    /** Version of one package from an ecosystem() list ('' when unknown). */
    function collect_version(array $packages, string $name): string
    {
        foreach ($packages as $p) {
            if (($p['name'] ?? null) === $name) {
                return (string) $p['version'];
            }
        }
        return '';
    }
}
