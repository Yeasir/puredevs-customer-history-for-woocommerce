<?php

defined( 'ABSPATH' ) or exit;
/*
 *  PD WooCommerce Customer Customers
 */
if ( !class_exists( 'PD_Customer_History_Customers' ) ) {
    class PD_Customer_History_Customers extends WP_List_Table
    {
        /**
         * Store customer information
         *
         * @since 1.0.0
         * @access protected
         * @var array $items
         */
        public  $items = array() ;
        /**
         * Store customer information
         *
         * @since 1.0.0
         * @access protected
         * @var array $customer_array
         */
        protected  $customer_array = array() ;
        /*
         *  Constructor
         */
        public function __construct()
        {
            parent::__construct();
        }
        
        public function get_columns()
        {
            $columns = array(
                'user'           => esc_html__( 'User', 'pd-customer-history' ),
                'role'           => esc_html__( 'Role', 'pd-customer-history' ),
                'orders'         => esc_html__( 'Orders', 'pd-customer-history' ),
                'pending_orders' => esc_html__( 'Pending Orders', 'pd-customer-history' ),
                'refund_orders'  => esc_html__( 'Refunded Orders', 'pd-customer-history' ),
                'orders_average' => esc_html__( 'Orders average', 'pd-customer-history' ),
                'total_spent'    => esc_html__( 'Total Spending', 'pd-customer-history' ),
                'actions'        => esc_html__( 'Actions', 'pd-customer-history' ),
            );
            return $columns;
        }
        
        public function prepare_items()
        {
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );
            $search = ( isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' );
            $advanceSearchTerms = array();
            $customers = $this->pdwchs_woo_get_all_customers( $search, $advanceSearchTerms );
            if ( get_option( 'pd-woo-hide_users_with_no_orders' ) ) {
                foreach ( $customers as $key => $user ) {
                    $order_count = count( get_posts( array(
                        'numberposts' => -1,
                        'meta_key'    => '_customer_user',
                        'meta_value'  => $user->ID,
                        'post_type'   => 'shop_order',
                        'post_status' => 'any',
                        'post_parent' => '0',
                    ) ) );
                    if ( !$order_count > 0 ) {
                        unset( $customers[$key] );
                    }
                }
            }
            //$data
            foreach ( $customers as $user ) {
                $total_spent = $this->pdwchs_woo_get_customer_total_spent( $user->ID );
                $order_count = count( get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => '_customer_user',
                    'meta_value'  => $user->ID,
                    'post_type'   => 'shop_order',
                    'post_status' => 'any',
                    'post_parent' => '0',
                ) ) );
                $pending_orders_count = count( get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => '_customer_user',
                    'meta_value'  => $user->ID,
                    'post_type'   => 'shop_order',
                    'post_status' => array( 'pending', 'wc-pending' ),
                    'post_parent' => '0',
                ) ) );
                $refunded_orders_count = count( get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => '_customer_user',
                    'meta_value'  => $user->ID,
                    'post_type'   => 'shop_order',
                    'post_status' => array( 'refunded', 'wc-refunded' ),
                    'post_parent' => '0',
                ) ) );
                global  $wp_roles ;
                $roles_array = array();
                foreach ( $wp_roles->role_names as $role => $name ) {
                    if ( user_can( $user, $role ) ) {
                        $roles_array[] = $role;
                    }
                }
                $this->customer_array[] = array(
                    'user'           => '<span class="dashicons dashicons-admin-users" style="margin-right: 5px;"></span><a href="admin.php?page=pd-woo-customer&user_id=' . esc_html( $user->ID ) . '">' . esc_html( $user->display_name ) . '</a>',
                    'role'           => implode( '<br />', $roles_array ),
                    'orders'         => $order_count,
                    'pending_orders' => $pending_orders_count,
                    'refund_orders'  => $refunded_orders_count,
                    'orders_average' => ( $order_count > 0 ? wc_price( $total_spent / $order_count ) : wc_price( $total_spent ) ),
                    'total_spent'    => wc_price( $total_spent ),
                    'actions'        => '<a href="admin.php?page=pd-woo-customer&user_id=' . esc_html( $user->ID ) . '" class="button"><strong><span class="dashicons dashicons-visibility vertical-align-middle"></span> ' . esc_html__( 'View', 'pd-customer-history' ) . '</strong></a>',
                );
            }
            usort( $this->customer_array, array( &$this, 'sort_data' ) );
            $results_per_page = get_option( 'pd-woo-results_per_page' );
            
            if ( !empty($_GET['show-items']) ) {
                $perPage = sanitize_text_field( $_GET['show-items'] );
            } else {
                $perPage = ( !empty($results_per_page) ? $results_per_page : 10 );
            }
            
            $perPage = (int) $perPage;
            $currentPage = $this->get_pagenum();
            $totalItems = count( $this->customer_array );
            $this->set_pagination_args( array(
                'total_items' => $totalItems,
                'per_page'    => $perPage,
            ) );
            if ( !empty($this->customer_array) ) {
                $this->customer_array = array_slice( $this->customer_array, ($currentPage - 1) * $perPage, $perPage );
            }
            $this->items = $this->customer_array;
        }
        
        public function column_default( $item, $column_name )
        {
            switch ( $column_name ) {
                case 'user':
                case 'role':
                case 'orders':
                case 'pending_orders':
                case 'refund_orders':
                case 'orders_average':
                case 'total_spent':
                case 'actions':
                    return $item[$column_name];
                default:
                    return print_r( $item, true );
                    //Show the whole array for troubleshooting purposes
            }
        }
        
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'user'           => array( 'user', false ),
                'role'           => array( 'role', false ),
                'orders'         => array( 'orders', false ),
                'pending_orders' => array( 'pending_orders', false ),
                'refund_orders'  => array( 'refund_orders', false ),
                'orders_average' => array( 'orders_average', false ),
                'total_spent'    => array( 'total_spent', false ),
            );
            return $sortable_columns;
        }
        
        private function sort_data( $a, $b )
        {
            // Set defaults
            $orderby = 'user';
            $order = 'desc';
            // If orderby is set, use this as the sort column
            if ( !empty($_GET['orderby']) ) {
                $orderby = sanitize_text_field( $_GET['orderby'] );
            }
            // If order is set use this as the order
            if ( !empty($_GET['order']) ) {
                $order = sanitize_text_field( $_GET['order'] );
            }
            $result = strcmp( $a[$orderby], $b[$orderby] );
            if ( $order === 'asc' ) {
                return $result;
            }
            return -$result;
        }
        
        /**
         * Load all customer.
         *
         * @since    1.0.0
         * @access   public static
         */
        public static function pdwchs_woo_get_all_customers( $search, $advanceSearchTerms )
        {
            $user_role = array( 'administrator', 'customer', 'subscriber' );
            $args = array(
                'number'   => -1,
                'role__in' => $user_role,
            );
            $customer_query = new WP_User_Query( $args );
            return $customer_query->get_results();
        }
        
        /**
         * Load customer total spent.
         *
         * @since    1.0.0
         * @access   public static
         */
        public static function pdwchs_woo_get_customer_total_spent( $customer_id )
        {
            $total_spent = 0;
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $customer_id,
                'post_type'   => 'shop_order',
                'post_status' => array( 'any' ),
                'post_parent' => '0',
            ) );
            foreach ( $customer_orders as $order ) {
                $order = new WC_Order( $order->ID );
                $total_spent += $order->get_total();
            }
            return $total_spent;
        }
        
        /**
         * Load customer pending orders total.
         *
         * @since    1.0.0
         * @access   public static
         */
        public static function pdwchs_woo_get_customer_pending_orders_total( $customer_id )
        {
            $pending_orders_total = 0;
            $pending_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $customer_id,
                'post_type'   => 'shop_order',
                'post_status' => array( 'pending', 'wc-pending' ),
                'post_parent' => '0',
            ) );
            foreach ( $pending_orders as $order ) {
                $order = new WC_Order( $order->ID );
                $pending_orders_total += $order->get_total();
            }
            return $pending_orders_total;
        }
        
        /**
         * Load customer refunded orders total.
         *
         * @since    1.0.0
         * @access   public static
         */
        public static function pdwchs_woo_get_customer_refunded_orders_total( $customer_id )
        {
            $refunded_orders_total = 0;
            $refunded_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $customer_id,
                'post_type'   => 'shop_order',
                'post_status' => array( 'refunded', 'wc-refunded' ),
                'post_parent' => '0',
            ) );
            foreach ( $refunded_orders as $order ) {
                $order = new WC_Order( $order->ID );
                $refunded_orders_total += $order->get_total();
            }
            return $refunded_orders_total;
        }
    
    }
}