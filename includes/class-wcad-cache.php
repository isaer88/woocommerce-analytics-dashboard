<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Cache {
    public static function get( $days ) {
        return get_transient( 'wcad_overview_' . (int) $days );
    }
    public static function set( $days, $data ) {
        set_transient( 'wcad_overview_' . (int) $days, $data, 5 * MINUTE_IN_SECONDS );
    }
}
