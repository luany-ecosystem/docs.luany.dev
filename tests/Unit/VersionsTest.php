<?php

namespace Tests\Unit;

use App\Docs\Versions;
use PHPUnit\Framework\TestCase;

class VersionsTest extends TestCase
{
    private string $file;

    /** @var list<array<string, mixed>> */
    private array $packages = [
        ['name' => 'luany/cli', 'version' => '1.6'],
        ['name' => 'luany/core', 'version' => '1.4'],
        ['name' => 'LTE for VS Code', 'version' => '1.1', 'marketplace' => 'luany.luany-lte'],
    ];

    protected function setUp(): void
    {
        $this->file = sys_get_temp_dir() . '/luany-versions-' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
    }

    private function fakeHttp(): callable
    {
        return function (string $url, ?string $post): ?string {
            if (str_contains($url, 'marketplace')) {
                return (string) json_encode(['results' => [['extensions' => [['versions' => [['version' => '1.2.1']]]]]]]);
            }
            if (str_contains($url, 'luany/cli')) {
                return (string) json_encode(['packages' => ['luany/cli' => [['version' => 'v1.8.0-RC1'], ['version' => 'v1.7.0'], ['version' => 'v1.6.1']]]]);
            }
            return null;   // luany/core unreachable
        };
    }

    public function test_refresh_reads_packagist_and_the_marketplace(): void
    {
        $versions = new Versions($this->file);
        $versions->refresh($this->packages, $this->fakeHttp());

        $out = array_column($versions->packages($this->packages), 'version', 'name');
        $this->assertSame('1.7', $out['luany/cli'], 'latest stable, RC ignored');
        $this->assertSame('1.4', $out['luany/core'], 'unreachable source keeps the config value');
        $this->assertSame('1.2', $out['LTE for VS Code']);
    }

    public function test_config_values_until_the_first_refresh_and_when_disabled(): void
    {
        $this->assertSame('1.6', (new Versions($this->file, live: false))->packages($this->packages)[0]['version']);

        (new Versions($this->file))->refresh($this->packages, $this->fakeHttp());
        $this->assertSame('1.6', (new Versions($this->file, live: false))->packages($this->packages)[0]['version']);
    }

    public function test_parsers(): void
    {
        $this->assertNull(Versions::fromPackagist(null, 'luany/cli'));
        $this->assertNull(Versions::fromPackagist('{"packages":{"luany/cli":[{"version":"dev-main"}]}}', 'luany/cli'));
        $this->assertSame('2.0', Versions::fromPackagist('{"packages":{"luany/cli":[{"version":"v1.9.9"},{"version":"2.0.3"}]}}', 'luany/cli'));
        $this->assertNull(Versions::fromMarketplace('{"results":[]}'));
    }
}
