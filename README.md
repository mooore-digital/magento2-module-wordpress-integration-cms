# Mooore WordpressIntegrationCms

![Packagist Version](https://img.shields.io/packagist/v/mooore/magento2-module-wordpress-integration-cms)

Magento 2 module for integrating Wordpress pages in Magento CMS pages.

- [Installation](#installation)
- [Styles](#styles)
  - [SCSS](#scss)
  - [LESS (Magento)](#less-magento)
  - [Extending and Including styles from main theme](#extending-and-including-styles-from-main-theme)

## Installation

```shell script
composer require mooore/magento2-module-wordpress-integration-cms
bin/magento setup:upgrade
```

## Styles

### SCSS

If your project is using the scss version.
Via e.g. [SnowdogApps/magento2-frontools](https://github.com/SnowdogApps/magento2-frontools)

Copy the files to your theme via;

```bash
mkdir -p app/design/frontend/<VENDOR>/<THEME>/Mooore_WordpressIntegrationCms
cp vendor/mooore/magento2-module-wordpress-integration-cms/view/frontend/styles app/design/frontend/<VENDOR>/<THEME>/Mooore_WordpressIntegrationCms
```

### LESS (Magento)

The LESS version is not available via the traditional options.
As we feel you must not load the styles on all pages.

If you feel the need to use it directly in your theme import the css file.

_We are working on a LESS version._

### Extending and Including styles from main theme

The gutenberg styles by default work separate from the theme styles of your project.

Add a SCSS/LESS file to your theme.

And hook the styles from your theme to the classes used by gutenberg.
To have your styles work with the gutenberg styles.

Sample for the button styles.

```scss
// File: _gutenberg-extend.scss
// Extend the gutenberg classes with the core styles

// button (action)
.wp-block-button__link,
a.wp-block-file__button,
.wp-block-file__button,
.kt-button {
    @include lib-button();
    @include lib-button-primary();
}
```
