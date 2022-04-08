<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://puredevs.com/
 * @since      1.0.0
 *
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PD_Customer_History
 * @subpackage PD_Customer_History/includes
 * @author     PureDevs <admin@puredevs.com>
 */
class PD_Customer_History
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      PD_Customer_History_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected  $plugin_name ;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        
        if ( defined( 'PDWCHS_CUSTOMER_HISTORY_VERSION' ) ) {
            $this->version = PDWCHS_CUSTOMER_HISTORY_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        
        $this->plugin_name = 'pd-customer-history';
        $this->pdwchs_load_dependencies();
        $this->pdwchs_set_locale();
        $this->pdwchs_define_admin_hooks();
        $this->pdwchs_define_public_hooks();
        if ( is_admin() ) {
            add_action(
                'in_plugin_update_message-' . PDWCHS_CUSTOMER_HISTORY_PLUGIN_BASENAME,
                array( $this, 'pdwchs_plugin_update_message' ),
                10,
                2
            );
        }
        add_filter( 'plugin_action_links_' . PDWCHS_CUSTOMER_HISTORY_PLUGIN_BASENAME, array( $this, 'pdwchs_add_plugin_page_settings_link' ) );
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - PD_Customer_History_Loader. Orchestrates the hooks of the plugin.
     * - PD_Customer_History_i18n. Defines internationalization functionality.
     * - PD_Customer_History_Admin. Defines all hooks for the admin area.
     * - PD_Customer_History_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function pdwchs_load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pd-customer-history-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pd-customer-history-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pd-customer-history-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pd-customer-history-public.php';
        if ( !class_exists( 'WP_List_Table' ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }
        /**
         * The class responsible for managing customer/user information's
         */
        require_once PDWCHS_CUSTOMER_HISTORY_PLUGIN_DIR . 'includes/class-pd-customer-history-customers.php';
        /**
         * The class responsible for managing customer/user order information's
         */
        require_once PDWCHS_CUSTOMER_HISTORY_PLUGIN_DIR . 'includes/class-pd-customer-order-history.php';
        $this->loader = new PD_Customer_History_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the PD_Customer_History_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function pdwchs_set_locale()
    {
        $plugin_i18n = new PD_Customer_History_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'pdwchs_load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function pdwchs_define_admin_hooks()
    {
        $plugin_admin = new PD_Customer_History_Admin( $this->pdwchs_get_plugin_name(), $this->pdwchs_get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'pdwchs_enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'pdwchs_enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'pdwchs_admin_menu' );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function pdwchs_define_public_hooks()
    {
        $plugin_public = new PD_Customer_History_Public( $this->pdwchs_get_plugin_name(), $this->pdwchs_get_version() );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function pdwchs_run()
    {
        $this->loader->pdwchs_run_loader();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function pdwchs_get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    PD_Customer_History_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function pdwchs_get_version()
    {
        return $this->version;
    }
    
    /**
     * Show action links on the plugin screen.
     *
     * @param mixed $links Plugin Action links.
     *
     * @return array
     */
    public function pdwchs_add_plugin_page_settings_link( $links )
    {
        $links[] = '<a href="' . admin_url( 'admin.php?page=pd-woo-settings' ) . '">' . __( 'Settings' ) . '</a>';
        return $links;
    }
    
    /**
     * Plugin inline update notice.
     *
     * @since    1.0.0
     * @access   public
     */
    public function pdwchs_plugin_update_message( $data, $response )
    {
        if ( isset( $data['upgrade_notice'] ) ) {
            printf( '<div class="update-message">%s</div>', wpautop( $data['upgrade_notice'] ) );
        }
    }

}