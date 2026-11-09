<?php
/**
 * Plugin Name: WooCommerce Analytics Dashboard
 * Description: Modular analytics dashboard foundation for WooCommerce.
 * Version: 0.4.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Author: WCAD Contributors
 * Text Domain: woocommerce-analytics-dashboard
 * License: GPL-2.0-or-later
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'WCAD_VERSION', '0.4.0' );
define( 'WCAD_FILE', __FILE__ );
define( 'WCAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCAD_URL', plugin_dir_url( __FILE__ ) );
require_once WCAD_PATH . 'includes/class-wcad-activator.php';
require_once WCAD_PATH . 'includes/class-wcad-deactivator.php';
register_activation_hook( __FILE__, array( 'WCAD_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WCAD_Deactivator', 'deactivate' ) );
add_action( 'plugins_loaded', function () {
    require_once WCAD_PATH . 'includes/class-wcad-dependencies.php';
    if ( ! WCAD_Dependencies::is_woocommerce_active() ) {
        add_action( 'admin_notices', array( 'WCAD_Dependencies', 'notice' ) );
        return;
    }
    require_once WCAD_PATH . 'includes/class-wcad-loader.php';
    WCAD_Loader::boot();
}, 20 );
