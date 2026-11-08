<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Admin {
    private $modules;
    public function __construct( $modules ) { $this->modules = $modules; }
    public function register() {
        add_action( 'admin_menu', array( $this, 'menu' ), 30 );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
    }
    public function menu() {
        add_submenu_page( 'woocommerce', __( 'Analytics Dashboard', 'woocommerce-analytics-dashboard' ), __( 'Analytics Dashboard', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-dashboard', array( $this, 'render' ) );
    }
    public function assets( $hook ) {
        if ( 'woocommerce_page_wcad-dashboard' !== $hook ) { return; }
        wp_enqueue_style( 'wcad-admin', WCAD_URL . 'admin/assets/css/admin.css', array(), WCAD_VERSION );
    }
    public function render() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        $module_count = $this->modules->count();
        require WCAD_PATH . 'admin/views/dashboard.php';
    }
}
