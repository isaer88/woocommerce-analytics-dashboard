<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * HPOS-compatible order analytics using WooCommerce CRUD APIs.
 * Attribution: sales/refunds are assigned to the original order creation date.
 * Revenue: order totals (incl. taxes/shipping), less recorded refunds.
 * Eligible orders: processing and completed. Historical status changes are not reconstructed.
 */
final class WCAD_Repository {
    const PAGE_SIZE = 200;
    const MAX_ORDERS = 20000;

    public static function overview( $days ) {
        $period = WCAD_Date_Range::periods( $days );
        $days = $period['days'];
        $cached = WCAD_Cache::get( $days );
        if ( false !== $cached ) { return $cached; }
        $current = self::aggregate( $period['start'], $period['end'], $days, true );
        if ( is_wp_error( $current ) ) { return $current; }
        $previous = self::aggregate( $period['previous_start'], $period['previous_end'], $days, false );
        if ( is_wp_error( $previous ) ) { return $previous; }
        $prev_sales = $previous['sales'];
        $data = array(
            'demo' => false,
            'days' => $days,
            'currency' => get_woocommerce_currency(),
            'kpis' => array(
                'sales' => round( $current['sales'], wc_get_price_decimals() ),
                'orders' => $current['orders'],
                'aov' => $current['orders'] ? round( $current['sales'] / $current['orders'], wc_get_price_decimals() ) : 0,
                'refunds' => round( $current['refunds'], wc_get_price_decimals() ),
                'change' => $prev_sales > 0 ? round( ( $current['sales'] - $prev_sales ) / $prev_sales * 100, 1 ) : null,
            ),
            'chart' => array(
                'labels' => $current['labels'],
                'sales' => array_values( $current['daily'] ),
                'previous' => array_values( $previous['daily'] ),
            ),
            'products' => array_slice( array_values( self::sort_products( $current['products'] ) ), 0, 10 ),
            'notes' => __( 'Sales include tax and shipping, minus refunds, attributed to order date. Processing and completed orders only.', 'woocommerce-analytics-dashboard' ),
        );
        WCAD_Cache::set( $days, $data );
        return $data;
    }

    private static function aggregate( $start, $end, $days, $with_products ) {
        $daily = array(); $labels = array();
        for ( $i = 0; $i < $days; $i++ ) {
            $date = $start->modify( '+' . $i . ' days' );
            $daily[ $date->format( 'Y-m-d' ) ] = 0.0;
            $labels[] = wp_date( 'M j', $date->getTimestamp(), wp_timezone() );
        }
        $result = array( 'sales' => 0.0, 'orders' => 0, 'refunds' => 0.0, 'daily' => $daily, 'labels' => $labels, 'products' => array() );
        $from = $start->getTimestamp();
        $to = $end->getTimestamp() - 1;
        $page = 1;
        do {
            $orders = wc_get_orders( array(
                'type' => 'shop_order',
                'status' => array( 'wc-processing', 'wc-completed' ),
                'date_created' => $from . '...' . $to,
                'limit' => self::PAGE_SIZE,
                'paged' => $page,
                'paginate' => false,
                'orderby' => 'date',
                'order' => 'ASC',
                'return' => 'objects',
            ) );
            if ( ! is_array( $orders ) ) { return new WP_Error( 'wcad_query_failed', __( 'Unable to retrieve WooCommerce orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) ); }
            foreach ( $orders as $order ) {
                $created = $order->get_date_created();
                if ( ! $created ) { continue; }
                $date_key = wp_date( 'Y-m-d', $created->getTimestamp(), wp_timezone() );
                if ( ! array_key_exists( $date_key, $result['daily'] ) ) { continue; }
                $refund = (float) $order->get_total_refunded();
                $sale = (float) $order->get_total() - $refund;
                $result['orders']++;
                $result['sales'] += $sale;
                $result['refunds'] += $refund;
                $result['daily'][ $date_key ] += $sale;
                if ( ! $with_products ) { continue; }
                foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
                    $product_id = (int) ( $item->get_variation_id() ?: $item->get_product_id() );
                    $key = $product_id ?: ( 'deleted_' . md5( $item->get_name() ) );
                    if ( ! isset( $result['products'][ $key ] ) ) {
                        $result['products'][ $key ] = array( 'name' => $item->get_name(), 'quantity' => 0, 'revenue' => 0 );
                    }
                    $result['products'][ $key ]['quantity'] += max( 0, (float) $item->get_quantity() + (float) $order->get_qty_refunded_for_item( $item_id ) );
                    $result['products'][ $key ]['revenue'] += (float) $item->get_total() - (float) $order->get_total_refunded_for_item( $item_id );
                }
            }
            $count = count( $orders );
            if ( $page * self::PAGE_SIZE >= self::MAX_ORDERS && $count === self::PAGE_SIZE ) {
                return new WP_Error( 'wcad_too_many_orders', __( 'This period contains too many orders for the basic analytics engine. Narrow the date range.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::PAGE_SIZE );
        return $result;
    }

    private static function sort_products( $products ) {
        uasort( $products, function ( $a, $b ) { return $b['revenue'] <=> $a['revenue']; } );
        return $products;
    }
}
