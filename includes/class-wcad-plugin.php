<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Plugin {
    private static $instance = null;
    private $modules;
    public static function instance() {
        if ( null === self::$instance ) { self::$instance = new self(); }
        return self::$instance;
    }
    private function __construct() {}
    public function run() {
        $this->modules = new WCAD_Module_Manager();
        $this->modules->load();
        require_once WCAD_PATH . 'includes/class-wcad-demo-data.php';
        require_once WCAD_PATH . 'includes/class-wcad-rest-api.php';
        WCAD_REST_API::register();
        if ( is_admin() ) {
            require_once WCAD_PATH . 'admin/class-wcad-admin.php';
            ( new WCAD_Admin( $this->modules ) )->register();
        }
    }
}
