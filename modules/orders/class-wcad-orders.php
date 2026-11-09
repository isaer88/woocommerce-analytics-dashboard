<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Order counts by creation date, independent of sales status. HPOS-compatible WooCommerce CRUD. */
final class WCAD_Orders {
    const PAGE_SIZE = 200;
    const MAX_ORDERS = 20000;
    public static function report( $days ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $cache_key = 'wcad_orders_050_' . $days;
        $cached = get_transient( $cache_key );
        if ( false !== $cached ) { return $cached; }
        $period = WCAD_Date_Range::periods( $days );
        $statuses = wc_get_order_statuses();
        $counts = array();
        foreach ( $statuses as $key => $label ) {
            $counts[ str_replace( 'wc-', '', $key ) ] = array( 'label' => wp_strip_all_tags( $label ), 'count' => 0 );
        }
        $rows = array();
        for ( $i = 0; $i < $days; $i++ ) {
            $date = $period['start']->modify( '+' . $i . ' days' )->format( 'Y-m-d' );
            $rows[$date] = array( 'date' => $date, 'orders' => 0, 'paid_orders' => 0, 'paid_total' => 0.0 );
        }
        $total = 0;
        $paid_count = 0;
        $paid_sum = 0.0;
        $page = 1;
        do {
            $orders = wc_get_orders( array(
                'type' => 'shop_order',
                'status' => array_keys( $statuses ),
                'date_created' => $period['start']->getTimestamp() . '...' . ( $period['end']->getTimestamp() - 1 ),
                'limit' => self::PAGE_SIZE, 'paged' => $page,
                'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects',
            ) );
            if ( ! is_array( $orders ) ) {
                return new WP_Error( 'wcad_orders_query', __( 'Could not load orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) );
            }
            foreach ( $orders as $order ) {
                if ( ! $order instanceof WC_Order ) { continue; }
                $created = $order->get_date_created();
                if ( ! $created ) { continue; }
                $date = wp_date( 'Y-m-d', $created->getTimestamp(), wp_timezone() );
                if ( ! isset( $rows[$date] ) ) { continue; }
                $status = $order->get_status();
                if ( ! isset( $counts[$status] ) ) {
                    $counts[$status] = array( 'label' => $status, 'count' => 0 );
                }
                $counts[$status]['count']++;
                $rows[$date]['orders']++;
                $total++;
                // Align paid-order metric with the current Sales Analytics policy.
                if ( in_array( $status, array( 'processing', 'completed' ), true ) ) {
                    $amount = (float) $order->get_total() - (float) $order->get_total_refunded();
                    $rows[$date]['paid_orders']++;
                    $rows[$date]['paid_total'] += $amount;
                    $paid_count++;
                    $paid_sum += $amount;
                }
            }
            $count = count( $orders );
            if ( $page * self::PAGE_SIZE >= self::MAX_ORDERS && $count === self::PAGE_SIZE ) {
                return new WP_Error( 'wcad_orders_limit', __( 'Too many orders for this report. Try a shorter period.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::PAGE_SIZE );
        $decimals = wc_get_price_decimals();
        foreach ( $rows as &$row ) { $row['paid_total'] = round( $row['paid_total'], $decimals ); }
        unset( $row );
        $distribution = array();
        foreach ( $counts as $key => $value ) {
            $distribution[] = array( 'status' => $key, 'label' => $value['label'], 'count' => $value['count'] );
        }
        usort( $distribution, function ( $a, $b ) { return $b['count'] <=> $a['count']; } );
        $data = array(
            'days' => $days, 'currency' => get_woocommerce_currency(),
            'totals' => array( 'orders' => $total, 'paid_orders' => $paid_count,
                'other_orders' => $total - $paid_count, 'paid_total' => round( $paid_sum, $decimals ),
                'aov' => $paid_count ? round( $paid_sum / $paid_count, $decimals ) : 0 ),
            'statuses' => $distribution, 'rows' => array_values( $rows ),
            'notes' => __( 'Counts include all WooCommerce order statuses, grouped by order creation date. Paid orders and AOV use processing/completed statuses only, with refunds deducted. Values are in the current store currency; historical currency conversion is not performed.', 'woocommerce-analytics-dashboard' ),
        );
        set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );
        return $data;
    }
}
