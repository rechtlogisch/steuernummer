![Recht logisch Steuer-ID banner image](rechtlogisch-steuernummer-banner.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rechtlogisch/steuernummer.svg?style=flat-square)](https://packagist.org/packages/rechtlogisch/steuernummer)
[![Tests](https://github.com/rechtlogisch/steuernummer/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/rechtlogisch/steuernummer/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/rechtlogisch/steuernummer.svg?style=flat-square)](https://packagist.org/packages/rechtlogisch/steuernummer)

# steuernummer

> Normalize, denormalize and validate German tax numbers (Steuernummer)

Formats bidirectionally German tax numbers originating from tax office letters (German: Bescheidformat) or the ELSTER-Format (German: bundeseinheitliches ELSTER-Steuernummerformat) and validates it.

Based on the [official ELSTER documentation](https://download.elster.de/download/schnittstellen/Pruefung_der_Steuer_und_Steueridentifikatsnummer.pdf) (chapters: 3-7; as of 2024-03-01). Inspired by [kontist/normalize-steuernummer](https://github.com/kontist/normalize-steuernummer) and [kontist/denormalize-steuernummer](https://github.com/kontist/denormalize-steuernummer).

## Installation

You can install the package via composer:

```bash
composer require rechtlogisch/steuernummer
```

## Usage

### Normalize

```php
normalizeSteuernummer('21/815/08150', 'BE'); // => '1121081508150'
```

or

```php
use Rechtlogisch\Steuernummer\Normalize;

(new Normalize('21/815/08150', 'BE'))
    ->returnElsterSteuernummerOnly(); // => '1121081508150'
```

Hint: you can use `run()` instead, if you want more details.

### Denormalize

```php
denormalizeSteuernummer('1121081508150'); // => '21/815/08150'
```

or

```php
use Rechtlogisch\Steuernummer\Denormalize;

(new Denormalize('1121081508150'))
    ->returnSteuernummerOnly(); // => '21/815/08150'
```

Hint: you can use `run()` instead, if you want more details.

#### Details

You can additionally control the result with setting `details: true`. When set `true` information which federal state the Steuernummer origins from is being added to result.

```php
denormalizeSteuernummer('1121081508150', details: true);
// [
//   'steuernummer' => '21/815/08150',
//   'federalState' => 'BE',
// ]
```

or

```php
use Rechtlogisch\Steuernummer\Denormalize;

(new Denormalize('1121081508150'))
    ->returnWithFederalState(); 
// [
//   'steuernummer' => '21/815/08150',
//   'federalState' => 'BE',
// ]
```

Hint: you can use `run()` instead, if you want more details.

### Validate

You can validate an input in the so called [ELSTER-Steuernummerformat](https://www.elster.de/eportal/helpGlobal?themaGlobal=wo_ist_meine_steuernummer#aufbauSteuernummer) (13-digits):

```php
isElsterSteuernummerValid('1121081508150'); // => true
```

or by providing the so called [Bescheidformat](https://www.elster.de/eportal/helpGlobal?themaGlobal=wo_ist_meine_steuernummer#aufbauSteuernummer) (length varies) together with the federal state:

```php
isSteuernummerValid('21/815/08150', 'BE'); // => true
```

#### Alternative

```php
use Rechtlogisch\Steuernummer\Validate;

(new Validate('1121081508150'))
    ->run() // ValidationResult::class
    ->isValid(); // => true
```

The federal state is determined by the first digits of the ELSTER-Format, you can provide it as the second parameter to override the auto-determination:

```php
use Rechtlogisch\Steuernummer\Validate;

(new Validate('1121081508150', 'BE'))
    ->run() // ValidationResult::class
    ->isValid(); // => true
```

## Errors

You can get a list of errors explaining why the provided input is invalid. The `run()` method on each class returns a DTO with a `getErrors()` method:

### Validation errors
```php
use Rechtlogisch\Steuernummer\Validate;

(new Validate('123456789', 'BE'))
    ->run() // ValidationResult::class
    ->getErrors(); // => ['elsterSteuernummer is not 13 digits long, and is 12 digits long']
```

### Normalization errors

```php
use Rechtlogisch\Steuernummer\Normalize;

(new Normalize('123456789', 'BE'))
    ->run() // NormalizationResult::class
    ->getErrors(); // => ['steuernummer for BE must contain exactly 10 digits and 9 digits have been provided']
```

### Denormalization errors

```php
use Rechtlogisch\Steuernummer\Denormalize;

(new Denormalize('123456789012'))
    ->run() // ValidationResult::class
    ->getErrors(); // => ['elsterSteuernummer is not 13 digits long, and is 12 digits long']
```

Hint: All *Result::classes extend the [ResultDto](./src/Abstracts/ResultDto.php).

### Supported tax offices

By default, tax office codes (German: Bundesfinanzamtsnummer - short BUFA) included in the [ELSTER ERiC libraries](https://www.elster.de/elsterweb/infoseite/entwickler) are supported by this package. Currently, based on ERiC 39.6.4. You'll find the list in [src/Bufas.php](./src/Bufas.php).

The list includes test BUFAs, which are invalid in production. It is recommended to disable them in production with the following environment variable:

```bash
STEUERNUMMER_PRODUCTION=true
```

or in PHP:

```php
putenv('STEUERNUMMER_PRODUCTION=true');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/rechtlogisch/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email open-source@rechtlogisch.de instead of using the issue tracker.

## Credits

- [Krzysztof Tomasz Zembrowski](https://github.com/rechtlogisch)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
