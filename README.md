# Mooore WordpressIntegrationCms

[![Packagist Version](https://img.shields.io/packagist/v/mooore/magento2-module-wordpress-integration-cms)](https://packagist.org/packages/mooore/magento2-module-wordpress-integration-cms)

> :warning: This module is still in its alpha phase.
> So with any new version it might break something from the previous version.
> Use at your own risk.

Magento 2 module for integrating Wordpress pages in Magento CMS pages.

![Magento screenshot](docs/magento-screenshot.png)

## TOC

- [TOC](#toc)
- [Installation](#installation)
- [How to use](#how-to-use)
  - [Styles/Script](#stylesscript)

## Installation

```shell script
composer require mooore/magento2-module-wordpress-integration-cms
bin/magento setup:upgrade
```

## How to use

When the module is installed.
You can add your wordpress domain to the `Content > Wordpress Sites`.

After this the dropdown in a page in `Content > Pages` will show all Wordpress pages.

For a more in dept instruction please see the [wiki](https://github.com/mooore-digital/magento2-module-wordpress-integration-cms/wiki)

### Styles/Script

All styles and script are loaded by default with you theme.
We also offer a SCSS version of the styles.
Which you can use with tools like [Snowdog Frontools](https://github.com/SnowdogApps/magento2-frontools).
