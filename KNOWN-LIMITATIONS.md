# Known limitations — v1.0.0 RC1

- Reports Center processes at most 20,000 eligible orders per period and returns an error above that threshold. Other modules may have different behavior and require separate verification.
- Reports count processing/completed orders by creation date, not payment date.
- Refunds are attributed to the original order creation date; historical order-status snapshots are not reconstructed.
- Sales revenue includes taxes and shipping; product-line revenue excludes both. Refund allocation may differ from WooCommerce Analytics.
- Orders in different currencies are aggregated without currency conversion.
- Customers are unique within the selected period, not necessarily lifetime new customers.
- Report presets are saved in browser storage, not WordPress user settings.
- Performance for high-volume stores and real-world HPOS environments needs validation.
- Plugin Check, PHPCS, WP/WC compatibility matrix and a formal security audit have not yet been completed.
