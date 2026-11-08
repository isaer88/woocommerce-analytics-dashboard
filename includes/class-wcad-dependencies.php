<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Dependencies {
    public static function is_woocommerce_active() { return class_exists( 'WooCommerce' ); }
    public static function notice() {
        if ( ! current_user_can( 'activate_plugins' ) ) { return; }
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'WooCommerce Analytics Dashboard requires an active WooCommerce plugin.', 'woocommerce-analytics-dashboard' ) . '</p></div>';
    }
}
