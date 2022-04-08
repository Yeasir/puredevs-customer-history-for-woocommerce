<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://puredevs.com/
 * @since      1.0.0
 *
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/admin
 * @author     PureDevs <admin@puredevs.com>
 */
class PD_Customer_History_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function pdwchs_enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in PD_Customer_History_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The PD_Customer_History_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            'date-picker',
            plugin_dir_url( __FILE__ ) . 'css/daterangepicker.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            'custom-style',
            plugin_dir_url( __FILE__ ) . 'css/pd-customer-history-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function pdwchs_enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in PD_Customer_History_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The PD_Customer_History_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            'moment-js',
            plugin_dir_url( __FILE__ ) . 'js/moment.min.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'date-picker',
            plugin_dir_url( __FILE__ ) . 'js/daterangepicker.min.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        add_thickbox();
        wp_enqueue_script(
            'custom-js',
            plugin_dir_url( __FILE__ ) . 'js/pd-customer-history-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    /*
     *  Admin Menu and Separator
     */
    function pdwchs_admin_menu()
    {
        global  $menu ;
        $capability = 'manage_woocommerce';
        $dashboard_page = add_menu_page(
            __( 'PD Customer History', 'pd-customer-history' ),
            __( 'Customer History', 'pd-customer-history' ),
            $capability,
            'pd-woo-dashboard',
            array( $this, 'pdwchs_woo_dashboard' ),
            'dashicons-analytics',
            '55.5'
        );
        add_submenu_page(
            'pd-woo-dashboard',
            __( 'Dashboard', 'pd-customer-history' ),
            __( '<span id="pd-dashboard-menu">Dashboard</span>', 'pd-customer-history' ),
            $capability,
            'pd-woo-dashboard',
            array( $this, 'pdwchs_woo_dashboard' )
        );
        add_submenu_page(
            'pd-woo-dashboard',
            __( 'PD Customers', 'pd-customer-history' ),
            __( '<span id="pd-customers-menu">Customers & Users</span>', 'pd-customer-history' ),
            $capability,
            'pd-woo-customers',
            array( $this, 'pdwchs_all_customers' )
        );
        add_submenu_page(
            '',
            __( 'PD Customer', 'pd-customer-history' ),
            __( 'Customer', 'pd-customer-history' ),
            $capability,
            'pd-woo-customer',
            array( $this, 'pdwchs_customer_details' )
        );
        add_submenu_page(
            'pd-woo-dashboard',
            __( 'Order History', 'pd-customer-history' ),
            __( '<span id="pd-order-history-menu">Order History</span>', 'pd-customer-history' ),
            $capability,
            'pd-woo-order-history',
            array( $this, 'pdwchs_order_history' )
        );
        add_submenu_page(
            'pd-woo-dashboard',
            __( 'Settings', 'pd-customer-history' ),
            __( 'Settings', 'pd-customer-history' ),
            $capability,
            'pd-woo-settings',
            array( $this, 'pdwchs_admin_settings' )
        );
        add_action( 'admin_print_scripts-' . $dashboard_page, array( $this, 'pdwchs_admin_custom_dashboard_js' ) );
    }
    
    /*
     *  Callback functions
     */
    function pdwchs_woo_dashboard()
    {
        require PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR . '/dashboard.php';
    }
    
    function pdwchs_all_customers()
    {
        require PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR . '/customers.php';
    }
    
    function pdwchs_admin_settings()
    {
        require PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR . '/plugin-settings.php';
    }
    
    function pdwchs_customer_details()
    {
        $screen = get_current_screen();
        require PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR . '/customer-details.php';
    }
    
    function pdwchs_order_history()
    {
        require PDWCHS_CUSTOMER_HISTORY_ADMIN_TEMPLATE_DIR . '/order-history.php';
    }
    
    function pdwchs_admin_custom_dashboard_js()
    {
        wp_enqueue_script(
            'amcharts-core-js',
            plugin_dir_url( __FILE__ ) . 'js/amcharts-core.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            'amcharts-charts-js',
            plugin_dir_url( __FILE__ ) . 'js/amcharts-charts.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }

}