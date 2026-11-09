<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Customer purchasing analytics using WooCommerce CRUD. No personal identifiers are returned. */
final class WCAD_Customers {
    const PAGE_SIZE = 200;
    const MAX_ORDERS = 20000;
    public static function report( $days ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $key = 'wcad_customers_070_' . $days;
        $cached = get_transient( $key );
        if ( false !== $cached ) { return $cached; }
        $period = WCAD_Date_Range::periods( $days );
        $daily = array();
        for ( $i = 0; $i < $days; $i++ ) {
            $date = $period['start']->modify( '+' . $i . ' days' )->format( 'Y-m-d' );
            $daily[$date] = array( 'date' => $date, 'orders' => 0, 'customers' => 0, 'revenue' => 0.0 );
        }
        // Scan paid orders before and during the reporting period to distinguish first-time vs repeat buyers.
        // A store with more than MAX_ORDERS in its full history receives a clear error, never misleading totals.
        $customers = array(); $period_customers = array(); $page = 1; $scanned = 0;
        do {
            $orders = wc_get_orders( array(
                'type' => 'shop_order', 'status' => array( 'processing', 'completed' ),
                'date_created' => '<' . $period['end']->getTimestamp(),
                'limit' => self::PAGE_SIZE, 'paged' => $page, 'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects',
            ) );
            if ( ! is_array( $orders ) ) { return new WP_Error( 'wcad_customers_query', __( 'Could not load customer orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) ); }
            foreach ( $orders as $order ) {
                if ( ! $order instanceof WC_Order ) { continue; }
                $created = $order->get_date_created();
                if ( ! $created ) { continue; }
                $date = wp_date( 'Y-m-d', $created->getTimestamp(), wp_timezone() );
                $user_id = (int) $order->get_customer_id();
                $email = strtolower( trim( (string) $order->get_billing_email() ) );
                // Guest purchases without an email cannot be safely deduplicated; count per order.
                $identity = $user_id ? 'user:' . $user_id : ( $email && is_email( $email ) ? 'email:' . hash( 'sha256', $email ) : 'order:' . $order->get_id() );
                $amount = (float) $order->get_total() - (float) $order->get_total_refunded();
                if ( ! isset( $customers[$identity] ) ) { $customers[$identity] = array( 'first' => $date, 'lifetime_orders' => 0, 'lifetime_revenue' => 0.0 ); }
                $customers[$identity]['lifetime_orders']++;
                $customers[$identity]['lifetime_revenue'] += $amount;
                if ( ! isset( $daily[$date] ) ) { continue; }
                $daily[$date]['orders']++;
                $daily[$date]['revenue'] += $amount;
                if ( ! isset( $period_customers[$identity] ) ) {
                    $period_customers[$identity] = array( 'first' => $customers[$identity]['first'], 'orders' => 0, 'revenue' => 0.0, 'guest' => 0 === $user_id );
                }
                $period_customers[$identity]['orders']++;
                $period_customers[$identity]['revenue'] += $amount;
                $daily[$date]['customers']++; // Purchasing orders for this day; deduplicated below.
            }
            $count = count( $orders ); $scanned += $count;
            if ( $scanned >= self::MAX_ORDERS && $count === self::PAGE_SIZE ) {
                return new WP_Error( 'wcad_customers_limit', __( 'Too many historical orders for this report.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::PAGE_SIZE );
        $new = 0; $returning = 0; $guest = 0; $registered = 0; $revenue = 0.0;
        foreach ( $period_customers as $customer ) {
            if ( $customer['first'] >= $period['start']->format( 'Y-m-d' ) ) { $new++; } else { $returning++; }
            if ( $customer['guest'] ) { $guest++; } else { $registered++; }
            $revenue += $customer['revenue'];
        }
        // Distinct purchasers per day, rather than orders per day.
        foreach ( $daily as $date => &$row ) {
            $row['customers'] = 0;
            $row['revenue'] = round( $row['revenue'], wc_get_price_decimals() );
        } unset( $row );
        // For chart, count daily unique buyers using order date+identity, without exposing identities.
        // Derive this from a compact second pass over in-range orders only.
        $seen = array(); $page = 1;
        do {
            $orders = wc_get_orders( array( 'type' => 'shop_order', 'status' => array( 'processing', 'completed' ),
                'date_created' => $period['start']->getTimestamp() . '...' . ( $period['end']->getTimestamp() - 1 ),
                'limit' => self::PAGE_SIZE, 'paged' => $page, 'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects' ) );
            if ( ! is_array( $orders ) ) { break; }
            foreach ( $orders as $order ) {
                if ( ! $order instanceof WC_Order || ! $order->get_date_created() ) { continue; }
                $date = wp_date( 'Y-m-d', $order->get_date_created()->getTimestamp(), wp_timezone() );
                if ( ! isset( $daily[$date] ) ) { continue; }
                $uid = (int) $order->get_customer_id(); $email = strtolower( trim( (string) $order->get_billing_email() ) );
                $identity = $uid ? 'u:' . $uid : ( $email && is_email( $email ) ? 'e:' . hash( 'sha256', $email ) : 'o:' . $order->get_id() );
                if ( ! isset( $seen[$date][$identity] ) ) { $seen[$date][$identity] = true; $daily[$date]['customers']++; }
            }
            $page++;
        } while ( count( $orders ) === self::PAGE_SIZE );
        $total = count( $period_customers ); $decimals = wc_get_price_decimals();
        $result = array(
            'days' => $days, 'currency' => get_woocommerce_currency(),
            'totals' => array( 'customers' => $total, 'new' => $new, 'returning' => $returning,
                'guest' => $guest, 'registered' => $registered, 'revenue' => round( $revenue, $decimals ),
                'average' => $total ? round( $revenue / $total, $decimals ) : 0 ),
            'rows' => array_values( $daily ),
            'notes' => __( 'Purchasers with processing/completed orders, grouped by order creation date. New means first qualifying order in recorded history. Guests with the same billing email are grouped; guest and registered identities may remain separate. Unidentified guests count per order. Refunds are deducted from order totals. Historical currency conversion is not performed.', 'woocommerce-analytics-dashboard' ),
        );
        set_transient( $key, $result, 5 * MINUTE_IN_SECONDS );
        return $result;
    }
}
