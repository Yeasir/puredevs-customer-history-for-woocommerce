<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://puredevs.com/
 * @since             1.0.0
 * @package           PD_Customer_History
 *
 * @wordpress-plugin
 * Plugin Name: 	  PureDevs Customer History for WooCommerce
 * Plugin URI:		  https://wordpress.org/plugins/puredevs-customer-history-for-woocommerce
 * Description:       It helps you understand how your customers behave on site. It allows you to see how many pagesthey have visited, the orders they have completed, the search term they have used, and many more.
 * Version:           1.0.1
 * Author:            PureDevs
 * Author URI:        https://puredevs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pd-customer-history
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( !function_exists( 'pchfw_fs' ) ) {
    // Create a helper function for easy SDK access.
    function pchfw_fs()
    {
        global  $pchfw_fs ;
        
        if ( !isset( $pchfw_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $pchfw_fs = fs_dynamic_init( array(
                'id'               => '7511',
                'slug'             => 'puredevs-customer-history-for-woocommerce',
                'premium_slug'     => 'puredevs-customer-history-for-woocommerce-pro',
                'type'             => 'plugin',
                'public_key'       => 'pk_e6c1736a09c5350195f3ba55a1493',
                'is_premium'       => false,
                'premium_suffix'   => 'Pro',
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => true,
                'trial'            => array(
                'days'               => 30,
                'is_require_payment' => false,
            ),
                'menu'             => array(
                'slug'    => 'pd-woo-dashboard',
                'support' => false,
            ),
                'is_live'          => true,
            ) );
        }
        
        return $pchfw_fs;
    }
    
    // Init Freemius.
    pchfw_fs();
    // Signal that SDK was initiated.
    do_action( 'pchfw_fs_loaded' );
}

pchfw_fs()->add_action( 'after_uninstall', 'pchfw_fs_uninstall_cleanup' );
if ( !function_exists( 'pchfw_fs_uninstall_cleanup' ) ) {
    /**
     *  uninstall cleanup
     *
     */
    function pchfw_fs_uninstall_cleanup()
    {
        $options = array(
            'pd-woo-results_per_page',
            'pd-woo-default_save_admin_session',
            'pd-woo-hide_users_with_no_orders',
            'pd-woo-show_bot_sessions'
        );
        foreach ( $options as $option_name ) {
            delete_option( $option_name );
        }
        // drop a custom database table
        global  $wpdb ;
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pd_woo_customer_history_sessions" );
    }

}
if ( !function_exists( 'pdwchs_woo_customer_history_admin_notice' ) ) {
    /**
     *  Show an admin notice if Woocommerce is Deactivated
     *
     */
    function pdwchs_woo_customer_history_admin_notice()
    {
        ?>
		<div class="error">
			<p><?php 
        esc_html_e( 'PureDevs Customer History for WooCommerce is enabled but not effective. In order to work it requires WooCommerce.', 'pd-customer-history' );
        ?></p>
		</div>
		<?php 
    }

}
add_action( 'plugins_loaded', 'pdwchs_woo_customer_history_install', 12 );
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PDWCHS_CUSTOMER_HISTORY_VERSION', '1.0.0' );
define( 'PDWCHS_CUSTOMER_HISTORY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PDWCHS_CUSTOMER_HISTORY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR', PDWCHS_CUSTOMER_HISTORY_PLUGIN_DIR . 'admin/partials/templates' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pd-customer-history-activator.php
 */
function pdwchs_activate_customer_history()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-pd-customer-history-activator.php';
    PD_Customer_History_Activator::pdwchs_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pd-customer-history-deactivator.php
 */
function pdwchs_deactivate_customer_history()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-pd-customer-history-deactivator.php';
    PD_Customer_History_Deactivator::pdwchs_deactivate();
}

register_activation_hook( __FILE__, 'pdwchs_activate_customer_history' );
register_deactivation_hook( __FILE__, 'pdwchs_deactivate_customer_history' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pd-customer-history.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pdwchs_run_customer_history()
{
    $plugin = new PD_Customer_History();
    $plugin->pdwchs_run();
}

function pdwchs_woo_customer_history_install()
{
    
    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'pdwchs_woo_customer_history_admin_notice' );
    } else {
        pdwchs_run_customer_history();
    }

}
