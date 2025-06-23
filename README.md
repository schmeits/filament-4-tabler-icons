# Filament 4 Tabler icons

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmeits/filament-4-phosphor-icons.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-4-phosphor-icons)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schmeits/filament-4-phosphor-icons/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schmeits/filament-4-phosphor-icons/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schmeits/filament-4-phosphor-icons.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-4-phosphor-icons)

This is a custom icon pack to be used in Filament v4. You can use it to add Tabler icons everywhere :)

## Installation

You can install the package via composer:

```bash
composer require schmeits/filament-4-tabler-icons
```

## Usage

```php
    Forms\Components\TextInput::make('name')
        ->required()
        ->prefixIcon(Tabler::MoodSmile)
```
Check out the Tabler icons on their website [Tabler Icons](https://tablericons.com/) to find the icon you want to use.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Tally Schmeits](https://github.com/schmeits)
- [Tabler icons](https://tablericons.com/)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
