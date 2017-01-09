# PHP Platform Configuration
This package provides uniform APIs for configuring package specific settings in PHP Platform

[![Build Status](https://travis-ci.org/PHPPlatform/config.svg?branch=v0.1)](https://travis-ci.org/PHPPlatform/config)

## Usage
 - add a file named _config.json_ in the root of the package
 - to read a setting use
``` PHP
PhpPlatform\Config\Settings::getSettings($package,$setting)
```
where ``$package`` is package name and ``$setting`` is a required setting


## Example
_config.json_ in package named __phpplatform/mypackage__
``` JSON
{
    "logs":{
        "error":"/logs/error.log",
        "debug":"/logs/debug.log"
    }
}
```
to read logs.error
``` PHP
PhpPlatform\Config\Settings::getSettings('phpplatform/mypackage','logs.error');
```
