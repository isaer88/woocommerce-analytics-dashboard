<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_REST_API {
    public static function register() {
        add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
    }
    public static function routes() {
        register_rest_route( 'wcad/v1', '/orders', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) { return rest_ensure_response( WCAD_Orders::report( (int) $request->get_param( 'days' ) ) ); },
            'args' => array( 'days' => array( 'default' => 30, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return in_array( (int) $value, array( 7, 30, 90 ), true ); } ) ),
        ) );

        register_rest_route( 'wcad/v1', '/sales', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) { return rest_ensure_response( WCAD_Sales::report( (int) $request->get_param( 'days' ) ) ); },
            'args' => array( 'days' => array( 'default' => 30, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return in_array( (int) $value, array( 7, 30, 90 ), true ); } ) ),
        ) );

        register_rest_route( 'wcad/v1', '/overview', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) {
                return rest_ensure_response( WCAD_Repository::overview( (int) $request->get_param( 'days' ) ) );
            },
            'args' => array( 'days' => array(
                'default' => 30, 'sanitize_callback' => 'absint',
                'validate_callback' => function ( $value ) { return in_array( (int) $value, array( 7, 30, 90 ), true ); },
            ) ),
        ) );
    }
}
