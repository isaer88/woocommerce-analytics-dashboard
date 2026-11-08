<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Module_Manager {
    private $modules = array();
    public function load() {
        $files = glob( WCAD_PATH . 'modules/*/module.php' );
        if ( ! is_array( $files ) ) { return; }
        foreach ( $files as $file ) {
            $module = require $file;
            if ( $module instanceof WCAD_Module ) {
                $this->modules[] = $module;
                $module->register();
            }
        }
    }
    public function count() { return count( $this->modules ); }
}
