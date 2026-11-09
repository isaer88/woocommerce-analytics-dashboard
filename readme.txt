=== WooCommerce Analytics Dashboard ===
Contributors: wcad-contributors
Tags: woocommerce, analytics, dashboard, reports
Requires at least: 6.4
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 1.0.0-rc.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce analytics with sales, orders, products, customers and customizable reports.

== Description ==
WooCommerce Analytics Dashboard (WCAD) adds a dedicated WC Analytics menu with Overview, Sales, Orders, Products, Customers and Reports Center. Reports support custom date ranges, comparison periods, CSV export and browser-local presets. Read financial caveats before using reports for business decisions.

== Installation ==
1. Install and activate WooCommerce.
2. Upload the WCAD ZIP via Plugins > Add New > Upload Plugin.
3. Activate WCAD and open WC Analytics.

== Frequently Asked Questions ==
= Does WCAD support HPOS? =
WCAD uses WooCommerce CRUD order APIs and declares HPOS compatibility. Verify on your own staging store.

= Which orders are included in Reports Center? =
Processing and completed orders, grouped by order creation date. Recorded refunds reduce revenue and are attributed to the original order date. Sales include shipping and tax; product revenue excludes both. Mixed currencies are not converted.

= Are saved presets shared across devices? =
No. They are stored locally in the browser.

= Is there a report size limit? =
Reports Center rejects more than 20,000 eligible orders per period rather than returning incomplete totals. Other analytics modules have their own query limits; verify their behavior separately.

== Changelog ==
= 1.0.0-rc.1 =
* Prepare release-candidate documentation and QA checklist.
* Add bilingual portfolio case study and known-limitations documentation.
* No changes to financial calculation logic from v0.9.0.

= 0.9.0 =
* Declare WooCommerce HPOS compatibility.
* Invalidate analytics caches after WooCommerce order/refund changes.
* Correct Reports Center limit detection for exactly 20,000 orders.
= 0.8.0 =
* Reports Center with four report types, custom dates, comparisons, CSV and local presets.
= 0.7.0 =
* Customers Analytics and standalone WC Analytics admin menu.
= 0.6.0 =
* Products Analytics.
= 0.5.0 =
* Orders Analytics.
= 0.4.0 =
* Sales Analytics.
= 0.3.0 =
* Live WooCommerce overview metrics.
= 0.2.0 =
* Admin dashboard foundation.
= 0.1.0 =
* Initial modular foundation.
