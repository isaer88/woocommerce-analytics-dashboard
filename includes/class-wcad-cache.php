<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Cache version changes when order, refund, or product data is updated. */
final class WCAD_Cache {
    public static function epoch() { return (int) get_option( 'wcad_cache_epoch', 1 ); }
    public static function invalidate( ...$ignored ) {
        update_option( 'wcad_cache_epoch', self::epoch() + 1, false );
    }
    public static function key( $prefix, $suffix ) {
        return 'wcad_' . sanitize_key( $prefix ) . '_' . self::epoch() . '_' . md5( (string) $suffix );
    }
    public static function get( $days ) {
        return get_transient( self::key( 'overview', (int) $days ) );
    }
    public static function set( $days, $data ) {
        set_transient( self::key( 'overview', (int) $days ), $data, 5 * MINUTE_IN_SECONDS );
    }
    public static function register_invalidation() {
        foreach ( array( 'woocommerce_new_order', 'woocommerce_update_order', 'woocommerce_delete_order', 'woocommerce_trash_order', 'woocommerce_order_refunded', 'woocommerce_refund_created', 'woocommerce_new_order_item', 'woocommerce_update_order_item', 'woocommerce_delete_order_item' ) as $hook ) {
            add_action( $hook, array( __CLASS__, 'invalidate' ), 10, 3 );
        }
        add_action( 'updated_option', array( __CLASS__, 'invalidate_settings' ), 10, 3 );
    }
    public static function invalidate_settings( $option, $old, $new ) {
        if ( in_array( $option, array( 'woocommerce_currency', 'woocommerce_price_num_decimals', 'timezone_string', 'gmt_offset' ), true ) && $old !== $new ) {
            self::invalidate();
        }
    }
}
