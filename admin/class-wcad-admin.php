<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Admin {
    private $modules;
    public function __construct( $modules ) { $this->modules = $modules; }
    public function register() {
        add_action( 'admin_menu', array( $this, 'menu' ), 30 );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
        add_action( 'admin_init', array( $this, 'settings' ) );
    }
    public function menu() {
        add_menu_page( __( 'WC Analytics', 'woocommerce-analytics-dashboard' ), __( 'WC Analytics', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-dashboard', array( $this, 'render' ), 'dashicons-chart-area', 56 );
        add_submenu_page( 'wcad-dashboard', __( 'Overview', 'woocommerce-analytics-dashboard' ), __( 'Overview', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-dashboard', array( $this, 'render' ) );
        add_submenu_page( 'wcad-dashboard', __( 'Sales Analytics', 'woocommerce-analytics-dashboard' ), __( 'Sales Analytics', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-sales', array( $this, 'render_sales' ) );
        add_submenu_page( 'wcad-dashboard', __( 'Orders Analytics', 'woocommerce-analytics-dashboard' ), __( 'Orders Analytics', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-orders', array( $this, 'render_orders' ) );
        add_submenu_page( 'wcad-dashboard', __( 'Products Analytics', 'woocommerce-analytics-dashboard' ), __( 'Products Analytics', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-products', array( $this, 'render_products' ) );
        add_submenu_page( 'wcad-dashboard', __( 'Customers Analytics', 'woocommerce-analytics-dashboard' ), __( 'Customers Analytics', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-customers', array( $this, 'render_customers' ) );
        add_submenu_page( 'wcad-dashboard', __( 'Settings', 'woocommerce-analytics-dashboard' ), __( 'Settings', 'woocommerce-analytics-dashboard' ), 'manage_woocommerce', 'wcad-settings', array( $this, 'render_settings' ) );
    }
    public function settings() {
        register_setting( 'wcad_settings', 'wcad_default_range', array(
            'type' => 'integer', 'default' => 30,
            'sanitize_callback' => function ( $value ) {
                return in_array( (int) $value, array( 7, 30, 90 ), true ) ? (int) $value : 30;
            },
        ) );
    }
    public function assets( $hook ) {
        if ( ! isset( $_GET['page'] ) || ! in_array( sanitize_key( wp_unslash( $_GET['page'] ) ), array( 'wcad-dashboard', 'wcad-settings', 'wcad-sales', 'wcad-orders', 'wcad-products', 'wcad-customers' ), true ) ) { return; }
        wp_enqueue_style( 'wcad-admin', WCAD_URL . 'admin/assets/css/admin.css', array(), WCAD_VERSION );
        if ( 'wcad-customers' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            wp_enqueue_script( 'wcad-chart', WCAD_URL . 'admin/assets/js/chart.js', array(), WCAD_VERSION, true );
            wp_enqueue_script( 'wcad-customers', WCAD_URL . 'admin/assets/js/customers.js', array( 'wcad-chart' ), WCAD_VERSION, true );
            wp_localize_script( 'wcad-customers', 'wcadCustomersConfig', array(
                'endpoint' => esc_url_raw( rest_url( 'wcad/v1/customers' ) ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'defaultDays' => (int) get_option( 'wcad_default_range', 30 ),
            ) );
        }
        if ( 'wcad-products' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            wp_enqueue_script( 'wcad-chart', WCAD_URL . 'admin/assets/js/chart.js', array(), WCAD_VERSION, true );
            wp_enqueue_script( 'wcad-products', WCAD_URL . 'admin/assets/js/products.js', array( 'wcad-chart' ), WCAD_VERSION, true );
            wp_localize_script( 'wcad-products', 'wcadProductsConfig', array(
                'endpoint' => esc_url_raw( rest_url( 'wcad/v1/products' ) ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'defaultDays' => (int) get_option( 'wcad_default_range', 30 ),
            ) );
        }
        if ( 'wcad-orders' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            wp_enqueue_script( 'wcad-chart', WCAD_URL . 'admin/assets/js/chart.js', array(), WCAD_VERSION, true );
            wp_enqueue_script( 'wcad-orders', WCAD_URL . 'admin/assets/js/orders.js', array( 'wcad-chart' ), WCAD_VERSION, true );
            wp_localize_script( 'wcad-orders', 'wcadOrdersConfig', array(
                'endpoint' => esc_url_raw( rest_url( 'wcad/v1/orders' ) ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'defaultDays' => (int) get_option( 'wcad_default_range', 30 ),
            ) );
        }
        if ( 'wcad-sales' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            wp_enqueue_script( 'wcad-chart', WCAD_URL . 'admin/assets/js/chart.js', array(), WCAD_VERSION, true );
            wp_enqueue_script( 'wcad-sales', WCAD_URL . 'admin/assets/js/sales.js', array( 'wcad-chart' ), WCAD_VERSION, true );
            wp_localize_script( 'wcad-sales', 'wcadSalesConfig', array(
                'endpoint' => esc_url_raw( rest_url( 'wcad/v1/sales' ) ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'defaultDays' => (int) get_option( 'wcad_default_range', 30 ),
            ) );
        }
        if ( 'wcad-dashboard' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
            wp_enqueue_script( 'wcad-chart', WCAD_URL . 'admin/assets/js/chart.js', array(), WCAD_VERSION, true );
            wp_enqueue_script( 'wcad-dashboard', WCAD_URL . 'admin/assets/js/dashboard.js', array( 'wcad-chart' ), WCAD_VERSION, true );
            wp_localize_script( 'wcad-dashboard', 'wcadConfig', array(
                'endpoint' => esc_url_raw( rest_url( 'wcad/v1/overview' ) ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'defaultDays' => (int) get_option( 'wcad_default_range', 30 ),
                'error' => __( 'Unable to load dashboard data.', 'woocommerce-analytics-dashboard' ),
            ) );
        }
    }
    public function render() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        $module_count = $this->modules->count();
        require WCAD_PATH . 'admin/views/dashboard.php';
    }
    public function render_sales() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        require WCAD_PATH . 'admin/views/sales.php';
    }
    public function render_orders() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        require WCAD_PATH . 'admin/views/orders.php';
    }
    public function render_products() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        require WCAD_PATH . 'admin/views/products.php';
    }
    public function render_customers() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        require WCAD_PATH . 'admin/views/customers.php';
    }
    public function render_settings() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) { wp_die( esc_html__( 'Access denied.', 'woocommerce-analytics-dashboard' ) ); }
        require WCAD_PATH . 'admin/views/settings.php';
    }
}
