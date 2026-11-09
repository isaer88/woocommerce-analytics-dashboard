<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Product-line analytics from WooCommerce orders; excludes shipping and tax. */
final class WCAD_Products {
    const PAGE_SIZE = 200;
    const MAX_ORDERS = 20000;
    public static function report( $days ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $cached = get_transient( WCAD_Cache::key( 'products', $days ) );
        if ( false !== $cached ) { return $cached; }
        $period = WCAD_Date_Range::periods( $days );
        $daily = array();
        for ( $i = 0; $i < $days; $i++ ) {
            $date = $period['start']->modify( '+' . $i . ' days' )->format( 'Y-m-d' );
            $daily[$date] = array( 'date' => $date, 'units' => 0, 'revenue' => 0.0 );
        }
        $products = array(); $categories = array(); $total_units = 0; $total_revenue = 0.0;
        $page = 1;
        do {
            $orders = wc_get_orders( array(
                'type' => 'shop_order', 'status' => array( 'processing', 'completed' ),
                'date_created' => $period['start']->getTimestamp() . '...' . ( $period['end']->getTimestamp() - 1 ),
                'limit' => self::PAGE_SIZE, 'paged' => $page, 'orderby' => 'date', 'order' => 'ASC', 'return' => 'objects',
            ) );
            if ( ! is_array( $orders ) ) { return new WP_Error( 'wcad_products_query', __( 'Could not load product orders.', 'woocommerce-analytics-dashboard' ), array( 'status' => 500 ) ); }
            foreach ( $orders as $order ) {
                if ( ! $order instanceof WC_Order ) { continue; }
                $created = $order->get_date_created();
                if ( ! $created ) { continue; }
                $date = wp_date( 'Y-m-d', $created->getTimestamp(), wp_timezone() );
                if ( ! isset( $daily[$date] ) ) { continue; }
                $refund_qty = array(); $refund_value = array();
                foreach ( $order->get_refunds() as $refund ) {
                    foreach ( $refund->get_items( 'line_item' ) as $ref_item ) {
                        $original = (int) $ref_item->get_meta( '_refunded_item_id', true );
                        if ( ! $original ) { continue; }
                        $refund_qty[$original] = ( $refund_qty[$original] ?? 0 ) + abs( (float) $ref_item->get_quantity() );
                        $refund_value[$original] = ( $refund_value[$original] ?? 0 ) + abs( (float) $ref_item->get_total() );
                    }
                }
                foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
                    if ( ! $item instanceof WC_Order_Item_Product ) { continue; }
                    $id = (int) $item->get_product_id();
                    $variation_id = (int) $item->get_variation_id();
                    $key = $variation_id ?: $id;
                    if ( ! $key ) { $key = 'deleted-' . (int) $item_id; }
                    $key = (string) $key;
                    $qty = max( 0, (float) $item->get_quantity() - ( $refund_qty[$item_id] ?? 0 ) );
                    $revenue = max( 0, (float) $item->get_total() - ( $refund_value[$item_id] ?? 0 ) );
                    $name = $item->get_name();
                    if ( ! isset( $products[$key] ) ) {
                        $products[$key] = array( 'product_id' => $id, 'variation_id' => $variation_id, 'name' => $name, 'units' => 0, 'revenue' => 0.0 );
                    }
                    $products[$key]['units'] += $qty; $products[$key]['revenue'] += $revenue;
                    $daily[$date]['units'] += $qty; $daily[$date]['revenue'] += $revenue;
                    $total_units += $qty; $total_revenue += $revenue;
                    // Assign each product to one category to avoid double counting multi-category products.
                    $terms = $id ? get_the_terms( $id, 'product_cat' ) : false;
                    $category = ( is_array( $terms ) && $terms ) ? reset( $terms )->name : __( 'Uncategorized / deleted', 'woocommerce-analytics-dashboard' );
                    if ( ! isset( $categories[$category] ) ) { $categories[$category] = array( 'name' => $category, 'units' => 0, 'revenue' => 0.0 ); }
                    $categories[$category]['units'] += $qty; $categories[$category]['revenue'] += $revenue;
                }
            }
            $count = count( $orders );
            if ( $page * self::PAGE_SIZE >= self::MAX_ORDERS && $count === self::PAGE_SIZE ) {
                return new WP_Error( 'wcad_products_limit', __( 'Too many orders for this report. Try a shorter period.', 'woocommerce-analytics-dashboard' ), array( 'status' => 422 ) );
            }
            $page++;
        } while ( $count === self::PAGE_SIZE );
        $decimals = wc_get_price_decimals();
        foreach ( $daily as &$row ) { $row['revenue'] = round( $row['revenue'], $decimals ); } unset( $row );
        foreach ( $products as &$row ) { $row['revenue'] = round( $row['revenue'], $decimals ); } unset( $row );
        foreach ( $categories as &$row ) { $row['revenue'] = round( $row['revenue'], $decimals ); } unset( $row );
        $products = array_values( $products ); $categories = array_values( $categories );
        usort( $products, function( $a, $b ) { return $b['revenue'] <=> $a['revenue']; } );
        usort( $categories, function( $a, $b ) { return $b['revenue'] <=> $a['revenue']; } );
        $result = array(
            'days' => $days, 'currency' => get_woocommerce_currency(),
            'totals' => array( 'units' => $total_units, 'revenue' => round( $total_revenue, $decimals ), 'products' => count( $products ), 'categories' => count( $categories ) ),
            'products' => $products, 'categories' => $categories, 'rows' => array_values( $daily ),
            'notes' => __( 'Processing/completed orders by creation date. Product revenue is line total after discounts, less itemized line refunds; excludes shipping and taxes. Non-itemized refunds cannot be assigned to products. Units exclude itemized refunded quantities. Each product is assigned to one category; variations are separate. Historical currency conversion is not performed.', 'woocommerce-analytics-dashboard' ),
        );
        set_transient( WCAD_Cache::key( 'products', $days ), $result, 5 * MINUTE_IN_SECONDS );
        return $result;
    }
}
