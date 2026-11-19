<?php

namespace App\Providers;

use App\Docs\Documentation;
use App\Docs\Versions;
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

        $app->singleton(Versions::class, fn (Application $app) => new Versions(
            $app->storagePath('cache/versions.json'),
            (bool) config('docs.live_versions', true),
        ));
    }

    public function boot(Application $app): void
    {
        require_once $app->basePath('app/Support/helpers.php');
    }
}
