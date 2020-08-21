# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Composer author name

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

Initial release 🎉
