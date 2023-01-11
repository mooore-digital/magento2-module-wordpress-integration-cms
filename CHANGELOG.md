# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.10.9] - 2023-01-11
### Fixed
- Additional fix for href search problem

## [0.10.8] - 2022-11-07
### Fixed
- Small fix for href search pattern

## [0.10.7] - 2022-10-28
### Fixed
- Missing flex container on btn group

## [0.10.6] - 2022-09-28
### Changed
- update symfony/http-client to the latest LTS version

## [0.10.5] - 2022-07-26
### Fixed
- issue related to an undefined index in cronjob

## [0.10.4] - 2022-07-04
### Added
- Add meta description from post excerpt

## [0.10.3] - 2022-06-29
### Fixed
- **Possible breaking change** unwanted removal of page title for blog pages
- Missing build for newly added block support for `posts-list`

## [0.10.2] - 2022-06-27
### Fixed
- Fixed a bug where all URLS were parsed, changed for only hrefs

## [0.10.1] - 2022-06-24
### Fixed
- Fixed bug where URLS were not being correctly parsed

## [0.10.0] - 2022-06-24
### Added
- Media URL replace option
- Add basic blog functionality

## [0.9.1] - 2022-06-15
### Fixed
- Specificity in alignment option using the `:not()` v4 selector

## [0.9.0] - 2022-05-31
### Added
- Hyva boilerplate for setup styles for common en button,
  same as LESS and SCSS version
- Hyva auto merge option for the styles

### Changed
- Updated WPCI dependencies (thanks to @allrude)

### Fixed
- Full page layout styles with Hyva

## [0.8.8] - 2022-01-24
### Added
- noindex and nocookie pages now support WPCI pages and style

### Fixed
- Cleanup code by improving formatting

## [0.8.7] - 2022-01-11
### Fixed
- Resolved wrongly fixed merge conflict

## [0.8.6] - 2022-01-10
### Fixed
- Sometimes double-dashes gets encoded to one from API

## [0.8.5] - 2021-12-07
### Changed
- Code split the PagePlugin so the remote page resolving becomes reusable

## [0.8.4] - 2021-09-02
### Fixed
- Potential security issue with path-parse from @wordpress/block-library #57
- Detach Wordpress page does not work #49

## [0.8.3] - 2021-05-03
### Fixed
- Fixed embedded videos not being properly sized by aspect ratio

## [0.8.2] - 2021-04-21
### Added
- Common text and other util layout class

### Fixed
- Snowdog frontools compile errors
  SCSS styles for CSS need to be outside the scope of frontools (view)
- Corrected btn outline class

## [0.8.1] - 2021-04-21
### Added
- alignment options

### Improvement
- editorconfig for composer
- readme

### Fixed
- Sass button class typo

## [0.8.0] - 2021-04-21
### Added
- Block styles via node dependency
- Block styles as css file

### Improvement
- Make Button block as override for core version

## [0.7.2] - 2021-04-09
### Added
- Support for hyva theme

## [0.7.1] - 2021-03-02
### Fixed
- Issue when pushing Magento URL to Wordpress for single-store view

### Improvement
- Magento 2 shortcodes are now usable in the Wordpress instance.

## [0.7.0] - 2021-02-10
### Improvement
- Update dependency for mooore/magento2-module-wordpress-integration to ^0.3

## [0.6.3] - 2020-11-18
### Fixed
Button alignment styles

## [0.6.2] - 2020-10-08
### Added
- Missing translations
- Dutch translations
- German translations

## [0.6.1] - 2020-10-01
### Fixed
- Endpoint url from [PR#24](https://github.com/mooore-digital/magento2-module-wordpress-integration-cms/pull/24)

## [0.6.0] - 2020-09-30
### Added
- Magento 2.4 support

## [0.5.0] - 2020-09-23
### Removed
- Mooore Blocks CSS, this is now a separate plugin
- Kadence Blocks JS and CSS, this is now a separate plugin

### Fixed
- Fixed script syntax
- Fix button hover

## [0.4.0] - 2020-09-09
### Removed
- Formidable JS and CSS, this is now a separate plugin

### Changed
- Composer cleanup and improvements

## [0.3.5] - 2020-08-21
### Added
- Detach Wordpress page button to 'Select Wordpress Page' dropdown

### Changed
- Change mooore/magento2-module-wordpress-integration version constraint

## [0.3.4] - 2020-08-03
### Added
- Support for buttons and links inside Mooore's GroupLinks
- missing kt-slick styles
- mooore details block

### Fixed
- formidable label states
- mq less mistake

## [0.3.3] - 2020-07-14
### Fixed
- Fixed the btn hover/focus state

## [0.3.2] - 2020-07-14
### Fixed
- LESS version of column block not wrapping
- Fix btn group

## [0.3.0] - 2020-07-14
### Added
- New page layout for 1column full width layout.
  Allowing layouts like on wordpress themes, e.g. TwentyTwenty.

### Changed
- SCSS/LESS structure
- Load styles via `_module` instead via CSS import.
  See the wiki for more info.
- JS structure

## [0.2.1] - 2020-07-14
### Added
- Translations for nl_NL
- Support for homepage
- Fallback for Adminhtml page source if no pages could be found/fetched

## [0.2.0] - 2020-06-04
### Changed
- HTTP client from Guzzle to Symfony HttpClient
- `mooore/magento2-module-wordpress-integration` version constraint from `0.1.0` to `^0.1`
- Page listing refactored to make 'paging' through WP pages possible

### Added
- HTTP exception handling
- Formidable forms support
- LESS styling
- Search field for page select dropdown

## [0.1.1] - 2020-03-25
### Fixed
- old gradient value from KT Slider, Closes #5

## [0.1.0] - 2020-03-25
- Initial release 🎉
