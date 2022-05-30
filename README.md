<div align="center">

![Magento screenshot](assets/preview.png)

</div>

<h1 align="center">Mooore Wordpress Integration Cms</h1>

[![Packagist Version](https://img.shields.io/packagist/v/mooore/magento2-module-wordpress-integration-cms)](https://packagist.org/packages/mooore/magento2-module-wordpress-integration-cms)
![Supported Magento Versions](https://img.shields.io/badge/magento-%202.3_|_2.4-brightgreen.svg?logo=magento&longCache=true)
[![Compatible with Hyva](https://img.shields.io/badge/Compatible_with-Hyva-3df0af.svg?longCache=true)](https://hyva.io/)
![license](https://img.shields.io/github/license/mooore-digital/magento2-module-wordpress-integration-cms)

Magento 2 module for integrating Wordpress pages in to your Magento 2 frontend.
Giving you the power of a real CMS system via Wordpress inside Magento 2.

## Installation

```bash
composer require mooore/magento2-module-wordpress-integration-cms
bin/magento setup:upgrade
```

> :warning: This module is still in its Beta phase.
> So with any new version it might break something from the previous version.
> Use at your own risk.

## How to use

When the module is installed.
You can add your wordpress domain to the `Content > Wordpress Sites`.
_This is the base URL._

After this, all pages will be available as dropdown in your Magento CMS pages.

For more in-depth instructions please see the [wiki](https://github.com/mooore-digital/magento2-module-wordpress-integration-cms/wiki)

### Styles/Script

All styles are loaded by default with your theme, as a separate CSS file,
and the CSS is only loaded for Wordpress Integration pages.

We also offer some additional styles to hook into the main styles via LESS, SCSS or Hyva;

- For SCSS, you can use it via tools like [Snowdog Frontools](https://github.com/SnowdogApps/magento2-frontools).
- For Hyva copy the styles from the `web/tailwind` to your theme.

## Support

Is something missing or found a bug.
Feel free to support this Module by adding a Issue or Pull Request.

Have something cool that extend on this module share it 
