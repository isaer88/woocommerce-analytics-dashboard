<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap wcad-wrap" id="wcad-orders-page">
 <div class="wcad-heading"><div><p class="wcad-eyebrow">WOOCOMMERCE / ANALYTICS</p><h1><?php esc_html_e( 'Orders Analytics', 'woocommerce-analytics-dashboard' ); ?></h1><p class="wcad-muted"><?php esc_html_e( 'Order volume, status distribution and paid order value', 'woocommerce-analytics-dashboard' ); ?></p></div><span class="wcad-pill">LIVE</span></div>
 <div class="wcad-toolbar"><div class="wcad-range" role="group" aria-label="Date range"><button type="button" data-days="7">7 days</button><button type="button" data-days="30">30 days</button><button type="button" data-days="90">90 days</button></div><span class="wcad-muted" id="wcad-orders-status" role="status" aria-live="polite">Loading…</span></div>
 <div class="wcad-kpis">
 <?php foreach ( array( 'orders' => 'All Orders', 'paid_orders' => 'Paid Orders', 'other_orders' => 'Other Statuses', 'aov' => 'Paid Order AOV' ) as $key => $label ) : ?>
 <article class="wcad-card"><p><?php echo esc_html( $label ); ?></p><strong id="wcad-orders-<?php echo esc_attr( $key ); ?>">—</strong></article>
 <?php endforeach; ?></div>
 <section class="wcad-card wcad-chart-card"><div class="wcad-section-head"><h2><?php esc_html_e( 'Daily Orders', 'woocommerce-analytics-dashboard' ); ?></h2></div><div class="wcad-chart-wrap"><canvas id="wcad-orders-chart" role="img" aria-label="Daily order counts"></canvas></div></section>
 <section class="wcad-card"><div class="wcad-section-head"><h2><?php esc_html_e( 'Status Distribution', 'woocommerce-analytics-dashboard' ); ?></h2></div><div class="wcad-table-scroll"><table class="widefat striped"><thead><tr><th>Status</th><th>Orders</th><th>Share</th></tr></thead><tbody id="wcad-orders-status-rows"><tr><td colspan="3">Loading…</td></tr></tbody></table></div></section>
 <section class="wcad-card"><div class="wcad-section-head"><h2><?php esc_html_e( 'Daily Breakdown', 'woocommerce-analytics-dashboard' ); ?></h2><button type="button" class="button" id="wcad-orders-export" disabled>Export CSV</button></div><div class="wcad-table-scroll"><table class="widefat striped"><thead><tr><th>Date</th><th>All Orders</th><th>Paid Orders</th><th>Paid Total</th></tr></thead><tbody id="wcad-orders-rows"><tr><td colspan="4">Loading…</td></tr></tbody></table></div></section>
 <p class="wcad-muted" id="wcad-orders-notes"></p><p class="wcad-footer">WCAD v<?php echo esc_html( WCAD_VERSION ); ?> · Orders Analytics</p>
</div>
