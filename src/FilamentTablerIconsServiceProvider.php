<?php

declare(strict_types=1);

/*
 * src/FilamentTablerIconsServiceProvider.php
 *
 * Registers the package with Laravel. The icons themselves come from
 * secondnetwork/blade-tabler-icons; this package only adds the Tabler enum.
 */

namespace Schmeits\FilamentTablerIcons;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTablerIconsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-4-tabler-icons';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
