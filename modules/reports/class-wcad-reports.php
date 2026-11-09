<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Consolidated reporting using WooCommerce CRUD and HPOS-compatible order queries. */
final class WCAD_Reports {
    const PAGE_SIZE = 200;
    const MAX_ORDERS = 20000;
    public static function valid_date( $value ) {
        if ( ! is_string( $value ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/D', $value ) ) { return false; }
        $date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );
        return $date && $date->format( 'Y-m-d' ) === $value;
    }
    public static function report( $type, $start, $end, $compare = false ) {
        if ( ! in_array( $type, array( 'sales', 'orders', 'products', 'customers' ), true ) || ! self::valid_date( $start ) || ! self::valid_date( $end ) ) {
            return new WP_Error( 'wcad_report_invalid', __( 'Invalid report parameters.', 'woocommerce-analytics-dashboard' ), array( 'status' => 400 ) );
        }
        $from = new DateTimeImmutable( $start, wp_timezone() );
        $to = new DateTimeImmutable( $end, wp_timezone() );
        $today = new DateTimeImmutable( 'today', wp_timezone() );
        $days = (int) $from->diff( $to )->format( '%r%a' ) + 1;
        if ( $days < 1 || $days > 366 || $to > $today ) {
            return new WP_Error( 'wcad_report_range', __( 'Choose a date range of 1–366 days ending no later than today.', 'woocommerce-analytics-dashboard' ), array( 'status' => 400 ) );
        }
        $key = 'wcad_report_080_' . md5( $type . '|' . $start . '|' . $end . '|' . (int) $compare );
        $cached = get_transient( $key );
        if ( false !== $cached ) { return $cached; }
        $current = self::aggregate( $type, $from, $to->modify( '+1 day' ) );
        if ( is_wp_error( $current ) ) { return $current; }
        $previous = null;
        if ( $compare ) {
            $previous = self::aggregate( $type, $from->modify( '-' . $days . ' days' ), $from );
            if ( is_wp_error( $previous ) ) { return $previous; }
        }
        $result = array( 'type' => $type, 'start' => $start, 'end' => $end, 'currency' => get_woocommerce_currency(), 'current' => $current, 'previous' => $previous,
            'notes' => __( 'Based on order creation date. Paid orders: processing/completed. Refunds are attributed to the original order date. Sales include tax/shipping; product revenue excludes both. Customers are identified by account ID or guest email within the selected period, not lifetime first purchase. Orders with different currencies are not converted. CSV contains aggregate data only.', 'woocommerce-analytics-dashboard' ) );
        set_transient( $key, $result, 5 * MINUTE_IN_SECONDS );
        return $result;
    }
    private static function aggregate( $type, DateTimeImmutable $from, DateTimeImmutable $until ) {
        $daily = array();
        for ( $date = $from; $date < $until; $date = $date->modify( '+1 day' ) ) {
            $key = $date->format( 'Y-m-d' );
            $daily[$key] = array( 'date' => $key, 'orders' => 0, 'revenue' => 0.0, 'units' => 0, 'customers' => 0 );
        }
        $totals = array( 'orders' => 0, 'revenue' => 0.0, 'units' => 0, 'customers' => 0 );
        $people = array(); $daily_people = array(); $page = 1;
        do {
            $orders = wc_get_orders( array( 'type' => 'shop_order', 'status' => array( 'wc-processing', 'wc-completed' ),
                'date_created' => $from->getTimestamp() . '...' . ( $until->getTimestamp() - 1 ),
                'limit' => self::PAGE_SIZE, 'paged' => $page, 'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects' ) );
            if ( ! is_array( $orders ) ) { return new WP_Error( 'wcad_reports_query', __( 'Unable to load orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) ); }
            foreach ( $orders as $order ) {
                if ( ! $order instanceof WC_Order || ! $order->get_date_created() ) { continue; }
                $date = wp_date( 'Y-m-d', $order->get_date_created()->getTimestamp(), wp_timezone() );
                if ( ! isset( $daily[$date] ) ) { continue; }
                $revenue = (float) $order->get_total() - (float) $order->get_total_refunded();
                $units = 0;
                if ( 'products' === $type ) {
                    $revenue = 0.0;
                    foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
                        $revenue += (float) $item->get_total() - (float) $order->get_total_refunded_for_item( $item_id );
                        $units += max( 0, (float) $item->get_quantity() - abs( (float) $order->get_qty_refunded_for_item( $item_id ) ) );
                    }
                }
                $daily[$date]['orders']++;
                $daily[$date]['revenue'] += $revenue;
                $daily[$date]['units'] += $units;
                $totals['orders']++;
                $totals['revenue'] += $revenue;
                $totals['units'] += $units;
                if ( 'customers' === $type ) {
                    $id = (int) $order->get_customer_id();
                    $email = strtolower( trim( (string) $order->get_billing_email() ) );
                    $identity = $id ? 'u:' . $id : ( is_email( $email ) ? 'e:' . hash( 'sha256', $email ) : 'o:' . $order->get_id() );
                    $people[$identity] = true;
                    $daily_people[$date][$identity] = true;
                }
            }
            $count = count( $orders );
            if ( $page * self::PAGE_SIZE >= self::MAX_ORDERS && $count === self::PAGE_SIZE ) {
                return new WP_Error( 'wcad_reports_limit', __( 'Too many orders; choose a shorter period.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::PAGE_SIZE );
        if ( 'customers' === $type ) {
            $totals['customers'] = count( $people );
            foreach ( $daily_people as $date => $identities ) { $daily[$date]['customers'] = count( $identities ); }
        }
        $decimals = wc_get_price_decimals();
        $totals['revenue'] = round( $totals['revenue'], $decimals );
        foreach ( $daily as &$row ) { $row['revenue'] = round( $row['revenue'], $decimals ); } unset( $row );
        return array( 'totals' => $totals, 'rows' => array_values( $daily ) );
    }
}
