<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Loader {
    public static function boot() {
        require_once WCAD_PATH . 'includes/class-wcad-module.php';
        require_once WCAD_PATH . 'includes/class-wcad-module-manager.php';
        require_once WCAD_PATH . 'includes/class-wcad-plugin.php';
        WCAD_Plugin::instance()->run();
    }
}
