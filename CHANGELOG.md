# Changelog

## [0.8.0] - 2026-08-26

### Added

- Added a cart tracking migration for seeding existing customer cart data from stored WooCommerce sessions.
- Added migration support for cart item quantities, totals, product details, variation attributes, update timestamps, and expiration timestamps.
- Added Cart Tracking Seeding controls to the Customer Data Migrations admin page.
- Added migration locking to prevent multiple cart migrations from running at the same time.
- Added migration progress feedback with disabled controls, a loading overlay, and completion results.
- Added PHPUnit coverage for cart migration processing, locking, admin controls, redirects, result notices, and nonce handling.

### Changed

- Extended the Customer Data Migrations page to support both purchase and cart tracking migrations.
- Updated cart tracking services to support storing normalized cart snapshots from migration data.
- Improved migration error handling so programming errors are not silently treated as individual migration failures.
- Refactored shared migration admin presentation and result handling for multiple migration types.

### Internal Improvements

- Added WooCommerce session database test coverage using a dedicated WordPress database test double.
- Added test support for stored WooCommerce sessions and cart migration data.
- Kept cart migration, cart tracking, and admin presentation responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for existing WooCommerce cart session migration.
- Added coverage for valid, invalid, missing-user, variation, total fallback, and locking scenarios.
- Added controller coverage for cart migration registration, execution, locking, UI, redirects, and result notices.
- Verified cart migration behavior on staging.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.7.0] - 2026-08-26

### Added

- Added a dedicated **Migrations** tab to the Shur-loc Customer Tools admin page.
- Added a controlled purchase-tracking migration for reseeding existing users from their most recent qualifying WooCommerce order.
- Added migration status information showing the current version, last-run version, and last-run date.
- Added protected migration controls requiring an enable checkbox and confirmation before execution.
- Added migration locking to prevent multiple purchase migrations from running at the same time.
- Added automatic recovery from stale migration locks.
- Added visible migration progress feedback with disabled controls, a loading overlay, and spinner while the migration is running.
- Added post-migration reporting for examined, updated, skipped, and error counts.
- Added a warning when another purchase migration is already running.

### Changed

- Extended the purchase tracking service to support purchase data migrations.
- Moved the existing purchase-seeding functionality from Code Snippets into the Shur-loc Customer Tools plugin.
- Made purchase seeding safely rerunnable instead of treating it as a one-time operation.
- Expanded the Customer Tools admin page with separate **Overview** and **Migrations** tabs.

### Internal Improvements

- Added dedicated migration and migration administration classes.
- Reused the existing purchase tracking service so live tracking and migration processing share the same purchase data logic.
- Added migration version and last-run tracking.
- Added server-side migration locking and security checks for migration execution.
- Added supporting WordPress test doubles for migration administration and locking.

### Testing

- Added PHPUnit coverage for purchase migration execution and administration.
- Added coverage for migration locking, stale-lock recovery, and duplicate-run prevention.
- Added coverage for migration controls, result notices, progress feedback, and admin tab routing.
- Verified migration execution, locking, and progress feedback on staging.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.6.2] - 2026-08-21

### Internal Improvements

- Fixed branding in several files.
- Updated README.
- Added WooCommerce stubs to VS Code Intelephense configuration.

## [0.6.1] - 2026-08-19

### Internal Improvements

- Changed package name from ShurLocCustomerTools to ShurlocCustomerTools.
- Fixed branding in several files.
- Cleaned up bootstrap.
- Added guard against missing Shurloc_Admin_Page_Interface.

## [0.6.0] - 2026-08-13

### Added

- Added persistent cart snapshot tracking for logged-in WooCommerce customers.
- Added cart snapshot metadata for item count, cart contents total, cart contents, last update time, expiration time, and snapshot version.
- Added detailed cart item tracking including:
  - Product and variation IDs
  - Product name and SKU
  - Quantity
  - Line subtotal and line total
  - Variation attributes
- Added automatic 30-day cart snapshot expiration timestamps.
- Added automatic cart snapshot cleanup when a customer's cart becomes empty or an order is processed.
- Added a **Cart** column to the WordPress Users screen.
- Added clickable cart details panels showing product quantities, product links, SKUs, and variation attributes.
- Added dedicated admin CSS and JavaScript for Cart column presentation and panel interaction.
- Added PHPUnit coverage for:
  - Cart snapshot creation and updates
  - Cart item normalization and quantity totals
  - Cart contents totals and expiration timestamps
  - Empty-cart and checkout cleanup
  - Guest and logged-out user handling
  - Cart column registration and rendering
  - Product and variation links
  - Legacy seeded cart snapshot compatibility
  - Variation attribute rendering
  - Cart column asset loading

### Changed

- Extended the WordPress Users screen with current customer cart information.
- Preserved compatibility with previously seeded cart snapshots that do not contain variation attribute metadata.
- Moved cart detail presentation from inline snippet assets to dedicated plugin CSS and JavaScript files.

### Internal Improvements

- Added a dedicated cart tracking service for maintaining customer cart snapshots.
- Added a dedicated Users table Cart column class for cart presentation.
- Centralized cart metadata keys and snapshot versioning in the cart service.
- Added WooCommerce and WordPress test doubles for carts, orders, product attributes, permalinks, and admin asset enqueueing.
- Kept cart tracking, cart persistence, and admin presentation responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for the complete customer cart tracking and Users-screen Cart column features.
- Added regression coverage for the existing seeded cart metadata schema.
- Added coverage for cart cleanup after checkout and when carts become empty.
- Verified cart tracking and Cart column behavior on staging.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.5.0] - 2026-08-13

### Changed

- Removed the Jetpack **WordPress.com account** column from the WordPress Users screen.
- Reclaimed Users table space for customer information relevant to WooCommerce administration.
- Preserved all other Users table columns and their existing order.

### Internal Improvements

- Added a dedicated Users table column customization class.
- Applied column cleanup after third-party columns have been registered.
- Kept general Users table customization separate from feature-specific customer columns.

### Testing

- Added PHPUnit coverage for:
  - Users table column customization registration
  - WordPress.com account column removal
  - Preservation of unrelated columns
  - Preservation of column ordering
  - Handling when the Jetpack column is not present
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.4.0] - 2026-08-13

### Added

- Added a **Phone** column to the WordPress Users screen.
- Added customer phone numbers from WooCommerce billing metadata.
- Added clickable `tel:` links for customer phone numbers.
- Added consistent display formatting for United States phone numbers.
- Added support for common United States phone number formats, including:
  - 10-digit numbers
  - 11-digit numbers with a leading `1`
  - Numbers with a `+1` country code
  - Hyphenated, dotted, parenthesized, and unformatted numbers
- Added normalized phone numbers for `tel:` links while preserving international country codes.
- Added defensive handling of missing and unrecognized phone numbers.
- Added PHPUnit coverage for:
  - Phone column registration
  - Phone column positioning
  - Missing phone numbers
  - United States phone number normalization
  - International phone numbers
  - `tel:` URI normalization
  - Phone number escaping
  - Unrecognized phone values

### Changed

- Positioned the **Phone** column immediately after the **Email** column on the WordPress Users screen.
- Standardized recognizable United States phone numbers as `(555) 123-4567` for display.
- Preserved non-U.S. international phone number formatting when displaying customer data.

### Internal Improvements

- Added a dedicated Users table column class for customer phone numbers.
- Kept phone number display normalization separate from stored WooCommerce `billing_phone` metadata.
- Separated human-readable phone formatting from `tel:` URI normalization.

### Testing

- Added PHPUnit coverage for the complete customer phone column feature.
- Added data-driven coverage for multiple United States phone number formats.
- Added coverage for international numbers and malformed phone values.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

## [0.3.0] - 2026-08-13

### Added

- Added customer purchase tracking for WooCommerce orders.
- Added **Last Purchase**, **Last Purchase Order**, **Last Purchase Status**, and **Last Purchase Total** values to user metadata.
- Added automatic purchase tracking for qualifying WooCommerce order status changes.
- Added protection against older orders overwriting a customer's more recent purchase metadata.
- Added **Last Purchase** column to the WordPress Users screen.
- Added relative-time formatting, linked order numbers, order status, and order totals to the **Last Purchase** column.
- Added sortable **Last Purchase** column.
- Added purchase filters for:
  - Purchased within 1 day
  - Purchased within 7 days
  - Purchased within 30 days
  - Not purchased within 1 day
  - Not purchased within 7 days
  - Not purchased within 30 days
  - Never purchased
- Added defensive handling of missing, empty, and zero-valued purchase timestamps.
- Added a shared Users-screen filter coordinator for customer filtering controls.
- Added PHPUnit coverage for:
  - User purchase tracking
  - Purchase metadata updates
  - Older-order protection
  - Order status changes
  - Users table purchase column
  - Purchase column sorting
  - Last Purchase filters
  - Existing user meta query preservation
  - Invalid filter values
  - Legacy and missing purchase metadata
  - Shared Users-screen filter coordination

### Changed

- Renamed `Shurloc_Activity_Time_Formatter` to `Shurloc_Relative_Time_Formatter` for reuse across customer timestamp displays.
- Refactored activity filters to use the shared Users-screen filter coordinator.
- Removed the **Last Login** column and Last Login filtering from the WordPress Users screen.
- Consolidated customer filter controls under a single shared **Filter** button.

### Internal Improvements

- Added dedicated services for purchase tracking, Users table purchase columns, and purchase filtering.
- Reused relative-time formatting across activity and purchase information.
- Separated shared Users-screen filter presentation from activity- and purchase-specific filtering logic.
- Added WooCommerce and WordPress test doubles for order data, price formatting, order statuses, and related functions.
- Kept purchase tracking, presentation, filtering, and shared admin UI responsibilities separated into focused classes.

### Testing

- Added PHPUnit coverage for the complete user purchase tracking feature.
- Added regression coverage for older orders overwriting newer purchase metadata.
- Added coverage for purchase filter selection persistence and combined user meta queries.
- Added coverage for the shared Users-screen filter toolbar and single Filter button.
- Verified PHPUnit, PHPCS, and PHPStan checks pass.

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
