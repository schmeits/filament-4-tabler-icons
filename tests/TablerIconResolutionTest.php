<?php

declare(strict_types=1);

/*
 * tests/TablerIconResolutionTest.php
 *
 * Smoke tests that prove every enum case actually points at an existing SVG
 * in the icon source secondnetwork/blade-tabler-icons.
 *
 * WHY: this is exactly what breaks when the icon source is upgraded. Tabler
 * occasionally renames or removes icons. The enum still compiles, but in
 * production the consumer hits an SvgNotFound exception the moment the page
 * renders. These tests surface that early.
 */

use BladeUI\Icons\Exceptions\SvgNotFound;
use BladeUI\Icons\Factory;
use Filament\Support\Enums\IconSize;
use Schmeits\FilamentTablerIcons\Support\Icons\Tabler;

/**
 * Path to the icon source's SVG directory, resolved through the registered set.
 * Deliberately not hardcoded: this way the test also proves the registration is
 * correct.
 */
function tablerSvgPath(): string
{
    $path = data_get(app(Factory::class)->all(), 'tabler.paths.0');

    if (! is_string($path)) {
        throw new RuntimeException('The tabler icon set is not registered.');
    }

    return $path;
}

it('finds an SVG file on disk for every enum case', function () {
    // The core check of this package. If it fails, the enum points at icons
    // that the new version of the source no longer knows about.
    $missing = [];

    foreach (Tabler::cases() as $icon) {
        $file = tablerSvgPath() . '/' . $icon->value . '.svg';

        if (! file_exists($file)) {
            $missing[] = $icon->value;
        }
    }

    expect($missing)->toBeEmpty(
        'Missing SVG files: ' . implode(', ', array_slice($missing, 0, 20)),
    );
});

it('finds an existing SVG file for every Filament IconSize', function (IconSize $size) {
    // A separate check per size: should getIconForSize ever pick a different
    // variant per size, this is covered right away without rewriting the test.
    $missing = [];

    foreach (Tabler::cases() as $icon) {
        $name = $icon->getIconForSize($size);
        $file = tablerSvgPath() . '/' . str($name)->after('tabler-')->toString() . '.svg';

        if (! file_exists($file)) {
            $missing[] = $name;
        }
    }

    expect($missing)->toBeEmpty(
        'Missing SVG files for size ' . $size->value . ': ' . implode(', ', array_slice($missing, 0, 20)),
    );
})->with(fn (): array => IconSize::cases());

it('ships at least as many icons in the source as the enum points at', function () {
    // A sanity check on the source itself: if the number of files drops below
    // the number of enum cases, something is badly wrong or half-installed
    // upstream.
    $fileCount = count(glob(tablerSvgPath() . '/*.svg') ?: []);

    expect($fileCount)->toBeGreaterThanOrEqual(count(Tabler::cases()));
});

it('actually renders an icon as SVG markup', function () {
    // Checking the file alone is not enough: this proves blade-icons can
    // actually fetch and render the icon inside a Laravel app.
    $svg = app(Factory::class)->svg(Tabler::Abacus->getIconForSize(IconSize::Medium))->toHtml();

    expect($svg)->toContain('<svg')->toContain('</svg>');
});

it('also renders the first and the last icon from the enum', function () {
    // The edges of the enum: that is where new cases are added and where a
    // sorted generation goes wrong the fastest.
    $cases = Tabler::cases();

    foreach ([reset($cases), end($cases)] as $icon) {
        $svg = app(Factory::class)->svg($icon->getIconForSize(IconSize::Medium))->toHtml();

        expect($svg)->toContain('<svg');
    }
});

it('silently falls back to the fallback icon for an unknown icon', function () {
    // Worth knowing: blade-tabler-icons has 'section' configured as its
    // fallback. A vanished icon therefore does NOT raise an exception in
    // production but silently shows the wrong glyph. That is why the file check
    // above is the only real safeguard, not the rendering itself.
    $fallback = app(Factory::class)->svg('tabler-this-icon-does-not-exist')->toHtml();
    $section = app(Factory::class)->svg('tabler-section')->toHtml();

    expect($fallback)->toContain('<svg')
        ->and($fallback)->toBe($section);
});

it('covers every icon from the source with the enum', function () {
    // The reverse check: if the source ships new icons, the enum is missing
    // them. That is not a crash but a gap we deliberately want to see after a
    // dependency upgrade, so we can regenerate the enum with `composer generate`.
    $names = array_map(fn (string $file): string => basename($file, '.svg'), glob(tablerSvgPath() . '/*.svg') ?: []);
    $available = array_flip($names);

    $isVariant = function (string $name) use ($available): bool {
        foreach (['-filled', '-f'] as $suffix) {
            if (str_ends_with($name, $suffix) && isset($available[substr($name, 0, -strlen($suffix))])) {
                return true;
            }
        }

        return false;
    };

    $fromSource = array_filter($names, fn (string $name): bool => ! $isVariant($name));
    $missing = array_values(array_diff($fromSource, array_column(Tabler::cases(), 'value')));

    expect($missing)->toBeEmpty(
        count($missing) . ' icons missing from the enum, run `composer generate`: ' . implode(', ', array_slice($missing, 0, 20)),
    );
});

it('resolves the letter F icons to a real file', function () {
    // Regression for the generator bug that dropped every name ending in "-f".
    expect(file_exists(tablerSvgPath() . '/' . Tabler::LetterF->value . '.svg'))->toBeTrue();
});

it('does throw an error for an unknown icon set', function () {
    // By contrast: a set that does not exist at all has no fallback and does
    // raise a proper exception.
    app(Factory::class)->svg('this-set-does-not-exist-abc');
})->throws(SvgNotFound::class);
