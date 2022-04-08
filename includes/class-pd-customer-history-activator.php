<?php

/**
 * Fired during plugin activation
 *
 * @link       https://puredevs.com/
 * @since      1.0.0
 *
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/includes
 * @author     PureDevs <admin@puredevs.com>
 */
class PD_Customer_History_Activator
{
    /**
     * Runs during activation of the plugin
     *
     * @since    1.0.0
     */
    public static function pdwchs_activate()
    {
        if ( !get_option( 'pd-woo-results_per_page' ) ) {
            update_option( 'pd-woo-results_per_page', 10 );
        }
        if ( !get_option( 'pd-woo-hide_users_with_no_orders' ) ) {
            update_option( 'pd-woo-hide_users_with_no_orders', 0 );
        }
    }

}