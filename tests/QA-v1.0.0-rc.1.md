# WCAD 1.0.0 RC1 release gate

## Regression
- [ ] Upgrade from 0.9.0 and fresh install
- [ ] All six admin screens and Reports Center work
- [ ] All REST routes deny unauthenticated/subscriber access and allow shop manager
- [ ] Invalid dates/types return 400; range >366 days rejected
- [ ] CSV opens correctly in spreadsheet software and handles formula-looking cell values safely
- [ ] Cache refresh after order update, refund, currency/timezone change
- [ ] WP_DEBUG_LOG empty of WCAD errors

## Finance and scale
- [ ] Compare coupon, tax, shipping, partial and full refund scenarios against WooCommerce
- [ ] Compare HPOS on/off stores with the same test fixtures
- [ ] Check 200, 201, 20,000 and 20,001 eligible orders
- [ ] Confirm no misleading partial totals in Overview, Sales, Orders, Products, Customers
- [ ] Measure memory/time on staging with large order volumes

## Release compliance
- [ ] Run Plugin Check
- [ ] Run WordPress Coding Standards and fix actionable issues
- [ ] Review REST permission and sanitization/escaping paths
- [ ] Verify translation readiness and text domain
- [ ] Validate WordPress/WooCommerce/PHP compatibility matrix
- [ ] Confirm license and third-party asset licenses
- [ ] Capture screenshots and prepare portfolio case study

Do not label RC1 as stable 1.0.0 until the gate is completed.
