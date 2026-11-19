<?php

namespace App\Docs;

/**
 * Interface copy (lang/{locale}.php) for views — including error pages,
 * which are rendered without controller data: the locale then comes from
 * the first URI segment (/pt/...), falling back to the default locale.
 */
final class Copy
{
    /** @var array<string, array<string, mixed>> */
    private static array $loaded = [];

    /** @return array<string, mixed> */
    public static function for(?string $locale = null): array
    {
        $locale ??= self::localeFromUri();
        if (!is_file(base_path("lang/{$locale}.php"))) {
            $locale = (string) config('docs.default_locale', 'en');
        }

        return self::$loaded[$locale] ??= require base_path("lang/{$locale}.php");
    }

    public static function localeFromUri(): string
    {
        $path    = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $segment = explode('/', trim($path, '/'))[0] ?? '';

        return preg_match('/^[a-z]{2}$/', $segment) === 1 ? $segment : (string) config('docs.default_locale', 'en');
    }
}
