# Changelog

All notable changes to `filament-4-tabler-icons` will be documented in this file.

## Unreleased

### Added
- Support for Filament 5 and Laravel 13, next to Filament 4.
- 238 icons: everything blade-tabler-icons 3.49.0 ships, including the letter F icons (`Tabler::LetterF` and its variants) that were missing.
- `composer generate` regenerates the enum from the installed blade-tabler-icons version.
- Tests on every pull request: PHP 8.2 to 8.5, Laravel 12 and 13, Filament 4 and 5. Laravel 11 is not tested because Composer refuses to install it (every 11.x release has open security advisories).

### Changed
- Requires `secondnetwork/blade-tabler-icons` 3.49 or higher.
- All PHP files declare strict types.

### Removed
- The unused `FilamentTablerIconsServiceProvider::$viewNamespace` property.
- Leftovers from the package template: the factory autoload, the facade alias and the frontend tooling.

### Fixed
- The generator skipped every icon name ending in "-f", which dropped the letter F icons.
- The changelog workflow pointed at a `main` branch that does not exist in this repository.

## 2026-02-04 - Composer security update

- Updated dependencies to resolve security advisories.

## 1.0.0 - 2025-06-23

First release of the Tabler icons for Filament v4.
