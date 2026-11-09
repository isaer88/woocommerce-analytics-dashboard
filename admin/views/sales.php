<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap wcad-wrap" id="wcad-sales-page">
 <div class="wcad-heading"><div><p class="wcad-eyebrow">WOOCOMMERCE / ANALYTICS</p><h1><?php esc_html_e( 'Sales Analytics', 'woocommerce-analytics-dashboard' ); ?></h1><p class="wcad-muted"><?php esc_html_e( 'Revenue breakdown by order creation date', 'woocommerce-analytics-dashboard' ); ?></p></div><span class="wcad-pill">LIVE</span></div>
 <div class="wcad-toolbar"><div class="wcad-range" role="group" aria-label="Date range"><button type="button" data-days="7">7 days</button><button type="button" data-days="30">30 days</button><button type="button" data-days="90">90 days</button></div><span class="wcad-muted" id="wcad-sales-status" role="status" aria-live="polite">Loading…</span></div>
 <div class="wcad-kpis wcad-sales-kpis">
 <?php foreach ( array( 'gross' => 'Gross Sales', 'net' => 'Net Sales', 'discounts' => 'Discounts', 'refunds' => 'Refunds', 'tax' => 'Taxes', 'shipping' => 'Shipping', 'total' => 'Total Collected', 'orders' => 'Orders' ) as $key => $label ) : ?>
 <article class="wcad-card"><p><?php echo esc_html( $label ); ?></p><strong id="wcad-sales-<?php echo esc_attr( $key ); ?>">—</strong></article>
 <?php endforeach; ?></div>
 <section class="wcad-card wcad-chart-card"><div class="wcad-section-head"><h2>Daily Sales</h2></div><div class="wcad-chart-wrap"><canvas id="wcad-sales-detail-chart" role="img" aria-label="Daily sales"></canvas></div></section>
 <section class="wcad-card"><div class="wcad-section-head"><h2>Daily Breakdown</h2><button type="button" class="button" id="wcad-sales-export" disabled>Export CSV</button></div><div class="wcad-table-scroll"><table class="widefat striped"><thead><tr><th>Date</th><th>Orders</th><th>Gross</th><th>Discounts</th><th>Refunds</th><th>Net</th><th>Tax</th><th>Shipping</th><th>Total</th></tr></thead><tbody id="wcad-sales-rows"><tr><td colspan="9">Loading…</td></tr></tbody></table></div></section>
 <p class="wcad-muted" id="wcad-sales-notes"></p><p class="wcad-footer">WCAD v<?php echo esc_html( WCAD_VERSION ); ?> · Sales Analytics</p>
</div>
