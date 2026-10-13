<?php

declare(strict_types=1);

/*
 * tests/ArchTest.php
 *
 * Architecture rules for the package source.
 */

use Filament\Support\Contracts\ScalableIcon;
use Schmeits\FilamentTablerIcons\Support\Icons\Tabler;

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('declares strict types in every source file')
    ->expect('Schmeits\FilamentTablerIcons')
    ->toUseStrictTypes();

it('keeps the Tabler enum a Filament ScalableIcon')
    ->expect(Tabler::class)
    ->toBeEnum()
    ->toImplement(ScalableIcon::class);
