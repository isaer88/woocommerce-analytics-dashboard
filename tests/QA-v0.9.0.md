# WCAD v0.9.0 regression checklist

## Installation
- Update v0.8.0 to v0.9.0 with WooCommerce enabled; verify admin loads and no fatal errors.
- Activate on WooCommerce HPOS enabled and legacy storage; confirm WooCommerce compatibility screen.
- Disable WooCommerce: plugin should show dependency notice rather than fatal error.

## REST and permissions
- GET /wp-json/wcad/v1/reports unauthenticated: 401/403.
- Logged-in subscriber: 403; shop manager: authorized.
- Reject invalid type, dates, future end, reversed range, >366 days.

## Financial reconciliation
- Processing/completed orders with shipping, tax, coupon, partial refund, full refund.
- Compare Sales, Products, Customers, Reports for 7/30/90 days.
- Note that Reports revenue includes shipping/taxes while Products revenue excludes both.
- Verify report date attribution follows order creation date, not refund date.

## Cache freshness
- Load Reports, change order status/total, reload: data should refresh.
- Create a refund, reload: data should refresh.
- Update store currency and timezone: Reports cache should refresh.

## Scalability
- Test 200, 201, 20,000 and 20,001 eligible orders per report period.
- At >20,000 orders, Reports must return explicit error (not partial totals).
- Check PHP memory/time and WooCommerce HPOS SQL queries on staging.

## Known limitations
- Other analytics modules may still impose a 20,000-order cap.
- Refunds are assigned to original order date; status history is not reconstructed.
- Multi-currency orders are not converted; do not use for accounting without reconciliation.
- Reports are computed per request, then cached for five minutes; no background aggregation.
