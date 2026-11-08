<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Activator {
    public static function activate() {
        if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
            wp_die( esc_html__( 'WooCommerce Analytics Dashboard requires PHP 8.1 or newer.', 'woocommerce-analytics-dashboard' ) );
        }
        if ( version_compare( get_bloginfo( 'version' ), '6.4', '<' ) ) {
            wp_die( esc_html__( 'WooCommerce Analytics Dashboard requires WordPress 6.4 or newer.', 'woocommerce-analytics-dashboard' ) );
        }
        update_option( 'wcad_version', WCAD_VERSION, false );
    }
}
