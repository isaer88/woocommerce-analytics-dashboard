<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class WCAD_REST_API {
    public static function register() {
        add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
    }
    public static function routes() {
        register_rest_route( 'wcad/v1', '/reports', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) {
                return rest_ensure_response( WCAD_Reports::report( $request->get_param( 'type' ), $request->get_param( 'start' ), $request->get_param( 'end' ), (bool) $request->get_param( 'compare' ) ) );
            },
            'args' => array(
                'type' => array( 'required' => true, 'sanitize_callback' => 'sanitize_key', 'validate_callback' => function ( $v ) { return in_array( $v, array( 'sales', 'orders', 'products', 'customers' ), true ); } ),
                'start' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field', 'validate_callback' => function ( $v ) { return WCAD_Reports::valid_date( $v ); } ),
                'end' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field', 'validate_callback' => function ( $v ) { return WCAD_Reports::valid_date( $v ); } ),
                'compare' => array( 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean' ),
            ),
        ) );

        register_rest_route( 'wcad/v1', '/customers', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) { return rest_ensure_response( WCAD_Customers::report( (int) $request->get_param( 'days' ) ) ); },
            'args' => array( 'days' => array( 'default' => 30, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return in_array( (int) $value, array( 7, 30, 90 ), true ); } ) ),
        ) );

        register_rest_route( 'wcad/v1', '/products', array(
            'methods' => WP_REST_Server::READABLE,
            'permission_callback' => function () { return current_user_can( 'manage_woocommerce' ); },
            'callback' => function ( WP_REST_Request $request ) { return rest_ensure_response( WCAD_Products::report( (int) $request->get_param( 'days' ) ) ); },
            'args' => array( 'days' => array( 'default' => 30, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return in_array( (int) $value, array( 7, 30, 90 ), true ); } ) ),
        ) );

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
