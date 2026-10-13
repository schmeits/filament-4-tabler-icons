# Tabler icons for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmeits/filament-4-tabler-icons.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-4-tabler-icons)
[![Tests](https://img.shields.io/github/actions/workflow/status/schmeits/filament-4-tabler-icons/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/schmeits/filament-4-tabler-icons/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/schmeits/filament-4-tabler-icons.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-4-tabler-icons)

All [Tabler icons](https://tabler.io/icons) as a typed enum for Filament, so you get autocompletion instead of icon name strings. The SVGs come from [blade-tabler-icons](https://github.com/secondnetwork/blade-tabler-icons).

## Compatibility

| Package | Filament | Laravel | PHP |
|---|---|---|---|
| 1.1 | 4.x and 5.x | 12 and 13 | 8.2 or higher (Laravel 13: 8.3 or higher) |
| 1.0 | 4.x | 11 and 12 | 8.2 or higher |

The package name still says "filament-4" for backwards compatibility; it works on Filament 5 as well.

## Installation

```bash
composer require schmeits/filament-4-tabler-icons
```

## Usage

```php
use Filament\Forms\Components\TextInput;
use Schmeits\FilamentTablerIcons\Support\Icons\Tabler;

TextInput::make('name')
    ->required()
    ->prefixIcon(Tabler::MoodSmile);
```

The enum covers the outline set. Look up icons on [tabler.io/icons](https://tabler.io/icons): the icon `mood-smile` becomes `Tabler::MoodSmile`.

### Unknown icons

blade-tabler-icons has `section` configured as its fallback icon. A name that does not exist therefore shows the `section` icon instead of throwing an error. With the enum you cannot pick a name that does not exist, but if you also use plain strings and prefer an error, publish the config of blade-tabler-icons and set the fallback to an empty string:

```bash
php artisan vendor:publish --tag=blade-tabler-icons-config
```

```php
// config/blade-tabler-icons.php
'fallback' => '',
```

## Updating the icons

The enum is generated from the installed version of blade-tabler-icons. After updating that package, regenerate it:

```bash
composer update secondnetwork/blade-tabler-icons
composer generate
```

The test suite fails when the enum is missing icons that the installed version ships.

## Testing

```bash
composer test
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Tally Schmeits](https://github.com/schmeits)
- [Tabler icons](https://tabler.io/icons)
- [blade-tabler-icons](https://github.com/secondnetwork/blade-tabler-icons)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
