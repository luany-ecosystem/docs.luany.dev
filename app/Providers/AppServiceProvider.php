<?php

namespace App\Providers;

use App\Docs\Documentation;
use Luany\Framework\Application;
use Luany\Framework\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(Application $app): void
    {
        $app->singleton(Documentation::class, function (Application $app) {
            $path = rtrim((string) config('docs.path'), '/');
            if ($path !== '' && $path[0] !== '/' && preg_match('/^[A-Za-z]:[\\\\\/]/', $path) !== 1) {
                $path = base_path($path);   // relative DOCS_PATH → project root
            }

            return new Documentation($path, $app->storagePath('cache/docs'));
        });
    }

    public function boot(Application $app): void
    {
        require_once $app->basePath('app/Support/helpers.php');
    }
}
