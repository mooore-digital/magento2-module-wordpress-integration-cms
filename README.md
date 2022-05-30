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

All styles are loaded by default with your theme.

The blocks are loaded as an separate style for only Wordpress Integration pages.

The custom styles are loaded with your main theme styles,
which offers an option to hook in to the Wordpress block styles.

_This works for the Luma (LESS), Snowdog Blank (SCSS) and Hyva themes._

## Support

Is something missing or found a bug.
Feel free to support this Module by adding a Issue or Pull Request.

Have something cool that extend on this module share it 

### Block Compatibility Issues

If a block does match your Wordpress styling or is missing a feature,
please first check the following points before opening a issues;

<details><summary>Your Wordpress theme is made for the new full page editing</summary>

If this is the case,
please take note these block styles are not compatible for any themes that support full page editing.

This could be for example the TwentyTwentyTwo theme.

You can do the following options;

- Use an older theme like TwentyTwentyOne
- Disable the block options

</details>

<details><summary>My theme has an option that does not show on the Magento 2 side</summary>

This can mean 2 posible things;

1. Your custom option has no styles on the Magento side
2. Your custom option creates inline styles on the Wordpress side

If it is option **1** you also have an class that with that custom option,
simply add the required styles to that class.

if it is options **2** then there is nothing you can do for now,
except disable the option on the Wordpress side.

</details>
