<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="wrap wcad-wrap">
    <h1><?php esc_html_e( 'WooCommerce Analytics Dashboard', 'woocommerce-analytics-dashboard' ); ?></h1>
    <div class="wcad-panel">
        <h2><?php esc_html_e( 'Core Foundation v0.1.0', 'woocommerce-analytics-dashboard' ); ?></h2>
        <p><?php esc_html_e( 'The plugin is active and WooCommerce was detected successfully.', 'woocommerce-analytics-dashboard' ); ?></p>
        <p><strong><?php esc_html_e( 'Loaded modules:', 'woocommerce-analytics-dashboard' ); ?></strong> <?php echo esc_html( (string) $module_count ); ?></p>
        <p><?php esc_html_e( 'Analytics charts and metrics will arrive in future releases.', 'woocommerce-analytics-dashboard' ); ?></p>
    </div>
</div>
