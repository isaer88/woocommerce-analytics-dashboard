<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_Date_Range {
    public static function periods( $days ) {
        $days = in_array( (int) $days, array( 7, 30, 90 ), true ) ? (int) $days : 30;
        $today = new DateTimeImmutable( 'today', wp_timezone() );
        $start = $today->modify( '-' . ( $days - 1 ) . ' days' );
        $previous_start = $start->modify( '-' . $days . ' days' );
        return array(
            'days' => $days,
            'start' => $start,
            'end' => $today->modify( '+1 day' ),
            'previous_start' => $previous_start,
            'previous_end' => $start,
        );
    }
}
