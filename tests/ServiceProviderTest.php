<?php

/**
 * Smoke tests for the package's service provider.
 *
 * WHY: this package is loaded via package discovery into every Filament app.
 * If the provider stops registering or booting, every app that uses it falls
 * over on startup. This is the first domino we guard.
 */

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Schmeits\FilamentTablerIcons\FilamentTablerIconsServiceProvider;

it('registers the service provider in the application', function () {
    // getLoadedProviders() only contains providers that registered and booted
    // without errors, so this proves both phases at once.
    expect(app()->getLoadedProviders())
        ->toHaveKey(FilamentTablerIconsServiceProvider::class);
});

it('is a Laravel service provider', function () {
    expect(new FilamentTablerIconsServiceProvider(app()))
        ->toBeInstanceOf(ServiceProvider::class);
});

it('uses its own package name and not that of another package', function () {
    // These two were set to 'filament-4-phosphor-icons', a copy-paste mistake
    // from the sibling package. The name drives how spatie/laravel-package-tools
    // names config, views and translations, so two packages sharing the same
    // name in one app collide the moment a view or config is ever added.
    expect(FilamentTablerIconsServiceProvider::$name)->toBe('filament-4-tabler-icons')
        ->and(FilamentTablerIconsServiceProvider::$viewNamespace)->toBe('filament-4-tabler-icons');
});

it('loads the underlying blade-tabler-icons icon set', function () {
    // The package relies entirely on secondnetwork's icon set. If that
    // registration disappears after an upgrade, no icon resolves anymore.
    expect(app(Factory::class)->all())->toHaveKey('tabler');
});

it('runs the test suite on an in-memory sqlite database', function () {
    // Explicit safeguard: these tests must never touch a real database.
    expect(config('database.default'))->toBe('testing')
        ->and(config('database.connections.testing.driver'))->toBe('sqlite')
        ->and(config('database.connections.testing.database'))->toBe(':memory:');
});
