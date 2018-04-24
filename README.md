# PSR-16 Cache for Craft CMS
[![Join the chat at https://gitter.im/flipboxfactory/craft-psr16](https://badges.gitter.im/flipboxfactory/craft-psr16.svg)](https://gitter.im/flipboxfactory/craft-psr16?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Latest Version](https://img.shields.io/github/release/flipboxfactory/craft-psr16.svg?style=flat-square)](https://github.com/flipboxfactory/craft-psr16/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/flipboxfactory/craft-psr16/master.svg?style=flat-square)](https://travis-ci.org/flipboxfactory/craft-psr16)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/flipboxfactory/craft-psr16.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxfactory/craft-psr16/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/flipboxfactory/craft-psr16.svg?style=flat-square)](https://scrutinizer-ci.com/g/flipboxfactory/craft-psr16)
[![Total Downloads](https://img.shields.io/packagist/dt/flipboxfactory/craft-psr16.svg?style=flat-square)](https://packagist.org/packages/flipboxfactory/craft-psr16)

This package provides simple mechanism for PSR-16 Simple Cache via Craft CMS.

## Installation

To install, use composer:

```
composer require flipboxfactory/craft-psr16
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Usage
Define it as a component in your plugin
```php 
'components' => [
    'psr16cache' => [
        'class' => flipbox\craft\psr16\SimpleAdapterCache::class
     ]
]
```
or via your composer as an 'extra' definition
```json
"components": {
  "psr16cache": "flipbox\\craft\\psr16\\SimpleAdapterCache"
}
```

## Contributing

Please see [CONTRIBUTING](https://github.com/flipboxfactory/craft-psr16/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Flipbox Digital](https://github.com/flipbox)
- [devonliu02/yii2-simple-cache-adapter](https://github.com/devonliu02/yii2-simple-cache-adapter)

## License

The MIT License (MIT). Please see [License File](https://github.com/flipboxfactory/craft-psr16/blob/master/LICENSE) for more information.
