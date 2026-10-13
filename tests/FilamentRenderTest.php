<?php

declare(strict_types=1);

/*
 * tests/FilamentRenderTest.php
 *
 * Renders Tabler icons through Filament's own icon component.
 *
 * WHY: the file checks prove the SVGs exist, but only rendering through
 * <x-filament::icon> proves Filament accepts the enum as a ScalableIcon. That
 * contract is exactly what changes between Filament majors.
 */

use Illuminate\Support\Facades\Blade;
use Schmeits\FilamentTablerIcons\Support\Icons\Tabler;

it('renders a Tabler enum case through the Filament icon component', function (Tabler $icon) {
    $html = Blade::render('<x-filament::icon :icon="$icon" />', ['icon' => $icon]);

    expect($html)->toContain('<svg');
})->with([
    'a common icon' => [Tabler::Abacus],
    'a letter F icon' => [Tabler::LetterF],
]);

it('renders the same markup as blade-icons for that icon', function () {
    $html = Blade::render('<x-filament::icon :icon="$icon" />', ['icon' => Tabler::MoodSmile]);
    $svg = svg('tabler-mood-smile')->toHtml();

    // Filament adds its own classes to the <svg>, so compare the drawing itself.
    preg_match_all('/<path[^>]*>/', $svg, $expected);

    expect($expected[0])->not->toBeEmpty();

    foreach ($expected[0] as $path) {
        expect($html)->toContain($path);
    }
});
