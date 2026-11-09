<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Demo_Data {
    public static function get( $days = 30 ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $labels = array(); $sales = array(); $previous = array();
        $total = 0; $prev_total = 0;
        for ( $i = $days - 1; $i >= 0; $i-- ) {
            $index = $days - $i;
            $labels[] = wp_date( 'M j', strtotime( '-' . $i . ' days', current_time( 'timestamp' ) ) );
            $value = (int) ( 440 + $index * 7 + sin( $index * 0.67 ) * 140 + cos( $index * 0.21 ) * 80 );
            $prior = (int) ( 380 + $index * 5 + sin( $index * 0.53 ) * 110 );
            $sales[] = max( 0, $value ); $previous[] = max( 0, $prior );
            $total += max( 0, $value ); $prev_total += max( 0, $prior );
        }
        $orders = (int) round( $total / 74 );
        return array(
            'demo' => true, 'days' => $days, 'currency' => 'USD',
            'kpis' => array(
                'sales' => $total, 'orders' => $orders,
                'aov' => $orders ? round( $total / $orders, 2 ) : 0,
                'refunds' => round( $total * .018, 2 ),
                'change' => $prev_total ? round( ( $total - $prev_total ) / $prev_total * 100, 1 ) : 0,
            ),
            'chart' => array( 'labels' => $labels, 'sales' => $sales, 'previous' => $previous ),
            'products' => array(
                array( 'name' => 'Sample Product A', 'quantity' => 125, 'revenue' => 6250 ),
                array( 'name' => 'Sample Product B', 'quantity' => 98, 'revenue' => 4900 ),
                array( 'name' => 'Sample Product C', 'quantity' => 76, 'revenue' => 3420 ),
                array( 'name' => 'Sample Product D', 'quantity' => 52, 'revenue' => 2600 ),
            ),
        );
    }
}
