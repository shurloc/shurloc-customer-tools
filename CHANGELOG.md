# Changelog

## [0.2.0] - 2026-08-13

### Added

- Added customer login and activity tracking for WordPress users.
- Added **Last Login** and **Last Activity** timestamps to user metadata.
- Added throttled activity tracking for logged-in users across the frontend and WordPress administration area.
- Added **Last Login** and **Last Activity** columns to the WordPress Users screen.
- Added relative-time formatting for customer activity timestamps.
- Added sortable **Last Login** and **Last Activity** columns.
- Added activity filters for:
  - Logged in or active within 1 day
  - Logged in or active within 7 days
  - Logged in or active within 30 days
  - Never logged in
  - Never active
- Added defensive handling of missing, empty, and zero-valued activity timestamps.
- Added PHPUnit coverage for:
  - User login and activity tracking
  - Activity update throttling
  - Activity timestamp formatting
  - Users table activity columns
  - Activity column sorting
  - Last Login and Last Activity filters
  - Combined activity filters
  - Existing user meta query preservation
  - Invalid filter values
  - Legacy and missing activity metadata

### Changed

- Extended the WordPress Users screen with customer activity information and filtering controls.
- Positioned activity filters separately from WordPress's built-in user role controls.
- Limited activity filter controls to the top Users toolbar to prevent duplicate request parameters.

### Internal Improvements

- Added dedicated services for activity tracking, timestamp formatting, Users table columns, and activity filtering.
- Added deterministic namespaced `time()` test support for timestamp-dependent behavior.
- Added WordPress test doubles for user metadata, user queries, admin controls, and related functions.
- Kept activity tracking, presentation, and query modification responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for the complete user activity tracking feature.
- Added regression coverage for duplicate Users-screen filter fields and filter selection persistence.
- Added coverage for defensive handling of legacy activity metadata.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.1.1] - 2026-08-12

### Changed

- Adapted plugin to `shurloc-tools` new namespace, `Shurloc\Tools`.

## [0.1.0] - 2026-08-12

### Added

- Namespaced plugin at `Shurloc\CustomerTools`.
- Added `constants.php` and `bootstrap.php`.
- Added `Shurloc_Autoloader` to autoload classes.
- Added dedicated **Customers** submenu to **Shurloc Tools** top-level menu.

### Testing

- Set up PHPCS and Composer script `lint`.
- Set up PHPStan and Composer script `check`.
- Set up PHPUnit and Composer script `test`.
