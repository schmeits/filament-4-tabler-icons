<?php

declare(strict_types=1);

/*
 * bin/generate-enum.php
 *
 * Generates src/Support/Icons/Tabler.php from the SVG files that
 * secondnetwork/blade-tabler-icons ships in vendor/.
 *
 * Usage: php bin/generate-enum.php [--output=path/to/Tabler.php]
 *
 * The output only depends on the installed upstream version, so running it
 * twice against the same version gives a byte-identical file.
 */

$root = dirname(__DIR__);
$package = 'secondnetwork/blade-tabler-icons';
$svgDir = $root . '/vendor/' . $package . '/resources/svg';
$output = $root . '/src/Support/Icons/Tabler.php';

foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--output=')) {
        $output = substr($argument, strlen('--output='));
    }
}

if (! is_dir($svgDir)) {
    fwrite(STDERR, "SVG directory not found: {$svgDir}\n");
    exit(1);
}

/** @var array{packages?: list<array{name: string, version: string, time?: string}>} $installed */
$installed = json_decode((string) file_get_contents($root . '/vendor/composer/installed.json'), true);
$upstream = null;

foreach ($installed['packages'] ?? [] as $installedPackage) {
    if ($installedPackage['name'] === $package) {
        $upstream = $installedPackage;
    }
}

if ($upstream === null) {
    fwrite(STDERR, "{$package} is not installed.\n");
    exit(1);
}

$version = ltrim($upstream['version'], 'v');
$released = isset($upstream['time']) ? substr($upstream['time'], 0, 10) : 'unknown';

$names = [];

foreach ((array) scandir($svgDir) as $file) {
    if (is_string($file) && str_ends_with($file, '.svg')) {
        $names[] = substr($file, 0, -4);
    }
}

sort($names, SORT_STRING);
$available = array_flip($names);

/**
 * A filled variant is skipped because it is reached through its base icon.
 * Names that merely end in "-f" (such as "letter-f") are real icons without a
 * base file, so they stay.
 */
$isVariant = static function (string $name) use ($available): bool {
    foreach (['-filled', '-f'] as $suffix) {
        if (str_ends_with($name, $suffix) && isset($available[substr($name, 0, -strlen($suffix))])) {
            return true;
        }
    }

    return false;
};

$cases = [];

foreach ($names as $name) {
    if ($isVariant($name)) {
        continue;
    }

    $key = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));

    if (preg_match('/^\d/', $key) === 1) {
        $key = '_' . $key;
    }

    if (isset($cases[$key])) {
        fwrite(STDERR, "Duplicate enum case {$key} for '{$cases[$key]}' and '{$name}'.\n");
        exit(1);
    }

    $cases[$key] = $name;
}

$lines = [];

foreach ($cases as $key => $name) {
    $lines[] = "    case {$key} = '{$name}';";
}

$content = <<<PHP
<?php

declare(strict_types=1);

/*
 * src/Support/Icons/Tabler.php
 *
 * All Tabler icons as a Filament icon enum. Do not edit by hand: regenerate it
 * with `composer generate` (bin/generate-enum.php).
 */

namespace Schmeits\\FilamentTablerIcons\\Support\\Icons;

use Filament\\Support\\Contracts\\ScalableIcon;
use Filament\\Support\\Enums\\IconSize;

/**
 * Generated from {$package} {$version} (released {$released}).
 */
enum Tabler: string implements ScalableIcon
{

PHP;

$content .= implode("\n", $lines) . "\n" . <<<'PHP'

    public function getIconForSize(IconSize $size): string
    {
        return "tabler-{$this->value}";
    }
}

PHP;

file_put_contents($output, $content);

echo 'Tabler enum generated with ' . count($cases) . " icons from {$package} {$version}.\n";
