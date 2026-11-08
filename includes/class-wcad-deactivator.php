<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Deactivator {
    public static function deactivate() { wp_clear_scheduled_hook( 'wcad_scheduled_refresh' ); }
}
