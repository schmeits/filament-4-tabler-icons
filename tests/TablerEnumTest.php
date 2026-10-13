<?php

declare(strict_types=1);

/*
 * tests/TablerEnumTest.php
 *
 * Smoke tests for the public enum this package exposes.
 *
 * WHY: the Tabler enum is the entire public API. Consumers type-hint against it
 * in their resources and widgets. If its shape changes, their code breaks.
 */

use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Enums\IconSize;
use Schmeits\FilamentTablerIcons\Support\Icons\Tabler;

it('exposes Tabler as a string-backed enum', function () {
    expect(enum_exists(Tabler::class))->toBeTrue()
        ->and((new ReflectionEnum(Tabler::class))->getBackingType()?->getName())->toBe('string');
});

it('implements the ScalableIcon contract from Filament', function () {
    // Filament calls getIconForSize() as soon as an icon is used in a component.
    // If this contract ever disappears from Filament, this test must turn red.
    expect(Tabler::Abacus)->toBeInstanceOf(ScalableIcon::class);
});

it('prefixes the icon name with tabler', function () {
    // The prefix comes from the config of blade-tabler-icons. If it changes
    // upstream, our name points at nothing anymore.
    expect(Tabler::Abacus->getIconForSize(IconSize::Medium))->toBe('tabler-abacus');
});

it('returns the same icon for every IconSize', function () {
    // Tabler has no weight variants in this package, so scaling is done with CSS
    // rather than a different SVG. This locks that in deliberately: should anyone
    // ever introduce a suffix here, it has to be an explicit choice.
    $names = array_map(
        fn (IconSize $size): string => Tabler::Abacus->getIconForSize($size),
        IconSize::cases(),
    );

    expect(array_unique($names))->toHaveCount(1);
});

it('handles every Filament IconSize without error', function () {
    // Should getIconForSize ever gain a match() without a default, this catches
    // a new IconSize in Filament before the consumer runs into it.
    foreach (IconSize::cases() as $size) {
        expect(Tabler::Abacus->getIconForSize($size))->toStartWith('tabler-');
    }
});

it('uses a unique value for every icon name', function () {
    // A duplicate value means two enum cases point at the same icon, usually a
    // copy-paste mistake when adding new icons.
    $values = array_column(Tabler::cases(), 'value');

    expect(array_diff_assoc($values, array_unique($values)))->toBeEmpty();
});

it('uses kebab-case icon names exclusively', function () {
    // blade-tabler-icons only ships kebab-case file names. A case with an
    // uppercase letter or an underscore never resolves to an SVG.
    $invalid = array_filter(
        array_column(Tabler::cases(), 'value'),
        fn (string $value): bool => preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $value) !== 1,
    );

    expect($invalid)->toBeEmpty();
});

it('deliberately contains no filled variants of an icon it already has', function () {
    // The enum only covers the outline set. The source additionally ships
    // -filled and -f variants. That is a deliberate choice; this test locks it
    // in so a half-finished expansion stands out instead of landing silently.
    $values = array_column(Tabler::cases(), 'value');
    $present = array_flip($values);

    $filled = array_filter($values, function (string $value) use ($present): bool {
        foreach (['-filled', '-f'] as $suffix) {
            if (str_ends_with($value, $suffix) && isset($present[substr($value, 0, -strlen($suffix))])) {
                return true;
            }
        }

        return false;
    });

    expect($filled)->toBeEmpty();
});

it('keeps icons whose name only happens to end in -f', function () {
    // Regression: the generator used to skip every name ending in "-f", which
    // silently dropped the letter F icons. They have no base file, so they are
    // real icons and not filled variants.
    expect(Tabler::LetterF->value)->toBe('letter-f')
        ->and(Tabler::CircleLetterF->value)->toBe('circle-letter-f')
        ->and(Tabler::SquareRoundedLetterF->value)->toBe('square-rounded-letter-f');
});
