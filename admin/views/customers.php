<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap wcad-wrap" id="wcad-customers-page">
 <div class="wcad-heading"><div><p class="wcad-eyebrow">WC ANALYTICS / CUSTOMERS</p><h1><?php esc_html_e( 'Customers Analytics', 'woocommerce-analytics-dashboard' ); ?></h1><p class="wcad-muted">New and returning purchasers, revenue and purchasing trends</p></div><span class="wcad-pill">LIVE</span></div>
 <div class="wcad-toolbar"><div class="wcad-range" role="group" aria-label="Date range"><button type="button" data-days="7">7 days</button><button type="button" data-days="30">30 days</button><button type="button" data-days="90">90 days</button></div><span class="wcad-muted" id="wcad-customers-status" role="status" aria-live="polite">Loading…</span></div>
 <div class="wcad-kpis">
 <?php foreach ( array( 'customers' => 'Unique Customers', 'new' => 'New Customers', 'returning' => 'Returning Customers', 'revenue' => 'Customer Revenue', 'average' => 'Average Customer Value', 'guest' => 'Guest Customers', 'registered' => 'Registered Customers' ) as $key => $label ) : ?>
 <article class="wcad-card"><p><?php echo esc_html( $label ); ?></p><strong id="wcad-customers-<?php echo esc_attr( $key ); ?>">—</strong></article>
 <?php endforeach; ?></div>
 <section class="wcad-card wcad-chart-card"><div class="wcad-section-head"><h2>Daily Purchasing Customers</h2></div><div class="wcad-chart-wrap"><canvas id="wcad-customers-chart" role="img" aria-label="Daily purchasing customers"></canvas></div></section>
 <section class="wcad-card"><div class="wcad-section-head"><h2>Daily Breakdown</h2><button type="button" class="button" id="wcad-customers-export" disabled>Export CSV</button></div><div class="wcad-table-scroll"><table class="widefat striped"><thead><tr><th>Date</th><th>Orders</th><th>Unique Customers</th><th>Revenue</th></tr></thead><tbody id="wcad-customers-rows"><tr><td colspan="4">Loading…</td></tr></tbody></table></div></section>
 <p class="wcad-muted" id="wcad-customers-notes"></p><p class="wcad-footer">WCAD v<?php echo esc_html( WCAD_VERSION ); ?> · Customers Analytics</p>
</div>
