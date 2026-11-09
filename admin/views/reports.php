<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap wcad-wrap" id="wcad-reports-page">
 <div class="wcad-heading"><div><p class="wcad-eyebrow">WC ANALYTICS / REPORTS</p><h1><?php esc_html_e( 'Reports Center', 'woocommerce-analytics-dashboard' ); ?></h1><p class="wcad-muted">Build and compare custom WooCommerce reports</p></div><span class="wcad-pill">LIVE</span></div>
 <section class="wcad-card"><div class="wcad-report-controls">
  <label>Report type <select id="wcad-report-type"><option value="sales">Sales</option><option value="orders">Orders</option><option value="products">Products</option><option value="customers">Customers</option></select></label>
  <label>From <input id="wcad-report-start" type="date"></label><label>To <input id="wcad-report-end" type="date"></label>
  <label><input id="wcad-report-compare" type="checkbox" checked> Compare previous period</label>
  <button type="button" class="button button-primary" id="wcad-report-run">Generate report</button>
  <button type="button" class="button" id="wcad-report-save">Save preset</button>
  <label>Saved presets <select id="wcad-report-presets"><option value="">Choose preset</option></select></label>
  <button type="button" class="button" id="wcad-report-delete">Delete preset</button>
 </div><div class="wcad-range" role="group" aria-label="Quick ranges"><button type="button" data-days="7">7 days</button><button type="button" data-days="30">30 days</button><button type="button" data-days="90">90 days</button></div>
 <p id="wcad-report-status" class="wcad-muted" role="status" aria-live="polite">Choose a report and generate it.</p></section>
 <div class="wcad-kpis"><article class="wcad-card"><p>Orders</p><strong id="wcad-report-orders">—</strong><small id="wcad-report-orders-change"></small></article><article class="wcad-card"><p>Revenue</p><strong id="wcad-report-revenue">—</strong><small id="wcad-report-revenue-change"></small></article><article class="wcad-card"><p>Units</p><strong id="wcad-report-units">—</strong><small id="wcad-report-units-change"></small></article><article class="wcad-card"><p>Unique customers</p><strong id="wcad-report-customers">—</strong><small id="wcad-report-customers-change"></small></article></div>
 <section class="wcad-card wcad-chart-card"><div class="wcad-section-head"><h2>Daily trend</h2></div><div class="wcad-chart-wrap"><canvas id="wcad-report-chart" role="img" aria-label="Daily report trend"></canvas></div></section>
 <section class="wcad-card"><div class="wcad-section-head"><h2>Daily breakdown</h2><button type="button" class="button" id="wcad-report-export" disabled>Export CSV</button></div><div class="wcad-table-scroll"><table class="widefat striped"><thead><tr><th>Date</th><th>Orders</th><th>Revenue</th><th>Units</th><th>Unique customers</th></tr></thead><tbody id="wcad-report-rows"></tbody></table></div></section>
 <p class="wcad-muted" id="wcad-report-notes"></p><p class="wcad-footer">WCAD v<?php echo esc_html( WCAD_VERSION ); ?> · Reports Center</p>
</div>
