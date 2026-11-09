=== WooCommerce Analytics Dashboard ===
Contributors: wcad-contributors
Tags: woocommerce, analytics, dashboard, reports
Requires at least: 6.4
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 0.5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Admin dashboard framework for WooCommerce analytics.

== Description ==
Version 0.5.0 provides live WooCommerce overview, Sales Analytics and Orders Analytics with 7/30/90-day filters, CSV export, and capability-protected REST endpoints. Financial reports require reconciliation before production use.

== Installation ==
Upload the plugin ZIP using Plugins > Add New > Upload Plugin, activate WooCommerce, then open WooCommerce > Analytics Dashboard.

== Changelog ==
= 0.5.0 =
* Added Orders Analytics with order-status distribution, daily order trends, paid-order AOV and CSV export.
* Orders use WooCommerce CRUD for HPOS compatibility and are counted by creation date.
= 0.4.0 =
* Added Sales Analytics with daily financial breakdown and CSV export.
= 0.3.0 =
* Added live WooCommerce overview metrics and product rankings.

= 0.2.0 =
* Added demo analytics interface, REST endpoint and settings.
= 0.1.0 =
* Initial modular plugin foundation.

== v0.3.0 ==
Live WooCommerce CRUD analytics for eligible orders. Revenue includes shipping/taxes, subtracts recorded refunds and attributes refunds to order date. Up to 20,000 orders per period; five-minute cache.

== v0.4.0 ==
Added Sales Analytics page, 7/30/90-day breakdown, protected REST API, daily sales graph, and client-side CSV export. Metrics are estimates based on eligible orders; refunded tax/shipping are not allocated separately.
