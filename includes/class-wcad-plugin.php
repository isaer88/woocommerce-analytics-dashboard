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
        if ( is_admin() ) {
            require_once WCAD_PATH . 'admin/class-wcad-admin.php';
            ( new WCAD_Admin( $this->modules ) )->register();
        }
    }
}
