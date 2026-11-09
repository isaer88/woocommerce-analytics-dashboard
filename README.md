# WooCommerce Analytics Dashboard (WCAD)

Release candidate: **1.0.0-rc.1** — **not yet a stable 1.0.0 release**.

A WordPress plugin that adds a dedicated **WC Analytics** admin menu with Overview, Sales, Orders, Products, Customers and Reports Center. Features include 7/30/90-day views, CSV export, custom report date ranges, period comparison and browser-local report presets.

## Requirements
- WordPress 6.4+
- PHP 8.1+
- WooCommerce active

## Installation
Install WooCommerce, upload the plugin ZIP in **Plugins → Add New → Upload Plugin**, activate and open **WC Analytics**.

## Technical notes
- WooCommerce CRUD order APIs are used for HPOS compatibility.
- REST API endpoints are restricted with `manage_woocommerce` capability.
- Report data is cached in transients; WooCommerce order/refund changes invalidate cache keys.
- Local browser presets are not synchronized between users or devices.

## Financial and scalability limitations
See [KNOWN-LIMITATIONS.md](KNOWN-LIMITATIONS.md). Do not use these analytics as a substitute for accounting reconciliation.

## Testing
See [tests/QA-v1.0.0-rc.1.md](tests/QA-v1.0.0-rc.1.md). PHP/JS syntax checks alone do not establish WooCommerce runtime correctness.

## License
GPL-2.0-or-later. See [LICENSE](LICENSE).
