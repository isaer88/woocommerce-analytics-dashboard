<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Sales report uses WooCommerce CRUD (HPOS compatible). Values are based on order creation date. */
final class WCAD_Sales {
    const LIMIT = 200;
    const MAX_ORDERS = 20000;
    public static function report( $days ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $key = WCAD_Cache::key( 'sales', $days );
        $cached = get_transient( $key );
        if ( false !== $cached ) { return $cached; }
        $period = WCAD_Date_Range::periods( $days );
        $rows = array();
        for ( $i = 0; $i < $days; $i++ ) {
            $date = $period['start']->modify( '+' . $i . ' days' );
            $key_date = $date->format( 'Y-m-d' );
            $rows[$key_date] = array( 'date' => $key_date, 'orders' => 0, 'gross' => 0.0, 'discounts' => 0.0, 'refunds' => 0.0, 'net' => 0.0, 'tax' => 0.0, 'shipping' => 0.0, 'total' => 0.0 );
        }
        $page = 1;
        do {
            $orders = wc_get_orders( array(
                'type' => 'shop_order', 'status' => array( 'wc-processing', 'wc-completed' ),
                'date_created' => $period['start']->getTimestamp() . '...' . ( $period['end']->getTimestamp() - 1 ),
                'limit' => self::LIMIT, 'paged' => $page, 'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects',
            ) );
            if ( ! is_array( $orders ) ) { return new WP_Error( 'wcad_sales_query', __( 'Could not load orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) ); }
            foreach ( $orders as $order ) {
                $created = $order->get_date_created();
                if ( ! $created ) { continue; }
                $day = wp_date( 'Y-m-d', $created->getTimestamp(), wp_timezone() );
                if ( ! isset( $rows[$day] ) ) { continue; }
                $r = &$rows[$day];
                $discount = (float) $order->get_discount_total() + (float) $order->get_discount_tax();
                $refund = (float) $order->get_total_refunded();
                $tax = (float) $order->get_total_tax();
                $shipping = (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
                $total = (float) $order->get_total() - $refund;
                // Gross: pre-discount order total, including shipping and taxes.
                $gross = (float) $order->get_total() + $discount;
                $r['orders']++;
                $r['gross'] += $gross;
                $r['discounts'] += $discount;
                $r['refunds'] += $refund;
                $r['tax'] += $tax;
                $r['shipping'] += $shipping;
                $r['total'] += $total;
                // Net excludes shipping and taxes. Tax/shipping shown before refund allocation.
                $r['net'] += $total - $tax - $shipping;
                unset( $r );
            }
            $count = count( $orders );
            if ( $page * self::LIMIT >= self::MAX_ORDERS && $count === self::LIMIT ) {
                return new WP_Error( 'wcad_sales_limit', __( 'Too many orders for this report.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::LIMIT );
        $totals = array( 'orders' => 0, 'gross' => 0.0, 'discounts' => 0.0, 'refunds' => 0.0, 'net' => 0.0, 'tax' => 0.0, 'shipping' => 0.0, 'total' => 0.0 );
        foreach ( $rows as $row ) { foreach ( $totals as $k => $v ) { $totals[$k] += $row[$k]; } }
        $decimals = wc_get_price_decimals();
        foreach ( $totals as $k => $v ) { if ( 'orders' !== $k ) { $totals[$k] = round( $v, $decimals ); } }
        foreach ( $rows as &$row ) { foreach ( $row as $k => $v ) { if ( 'date' !== $k && 'orders' !== $k ) { $row[$k] = round( $v, $decimals ); } } } unset( $row );
        $data = array( 'days' => $days, 'currency' => get_woocommerce_currency(), 'totals' => $totals, 'rows' => array_values( $rows ),
            'notes' => __( 'Processing/completed orders only. Based on order creation date. Gross includes tax/shipping before discounts; net excludes tax/shipping after refunds. Tax/shipping values are before refund allocation; refunded taxes and shipping may affect reconciliation.', 'woocommerce-analytics-dashboard' ) );
        set_transient( WCAD_Cache::key( 'sales', $days ), $data, 5 * MINUTE_IN_SECONDS );
        return $data;
    }
}
