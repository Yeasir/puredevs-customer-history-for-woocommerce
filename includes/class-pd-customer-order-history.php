<?php

defined( 'ABSPATH' ) or exit;
/*
 *  PD WooCommerce Customer Order History
 */
if ( !class_exists( 'PD_Customer_Order_History' ) ) {
    class PD_Customer_Order_History extends WP_List_Table
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
         * Store order information
         *
         * @since 1.0.0
         * @access protected
         * @var array $last_orders
         */
        protected  $last_orders = array() ;
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
                'order_date'     => esc_html__( 'Date', 'pd-customer-history' ),
                'order_id'       => esc_html__( 'Order ID', 'pd-customer-history' ),
                'customer_name'  => esc_html__( 'Customer Name', 'pd-customer-history' ),
                'items_qty'      => esc_html__( 'Items & Qty', 'pd-customer-history' ),
                'order_total'    => esc_html__( 'Total', 'pd-customer-history' ),
                'payment_method' => esc_html__( 'Payment Method', 'pd-customer-history' ),
                'order_status'   => esc_html__( 'Status', 'pd-customer-history' ),
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
            $search = sanitize_text_field( $search );
            $advanceSearchTerms = array();
            $latest_orders = $this->pdwchs_get_latest_orders( $search, $advanceSearchTerms );
            if ( !empty($latest_orders) ) {
                foreach ( $latest_orders as $order ) {
                    
                    if ( wp_get_post_parent_id( $order->ID ) == 0 ) {
                        $order = new WC_Order( $order->ID );
                        $order_id = $order->get_id();
                        $items_qty = '';
                        foreach ( $order->get_items() as $line ) {
                            $items_qty .= $line['qty'] . ' x <a href="' . admin_url() . 'post.php?post=' . $line['product_id'] . '&action=edit" class="">' . $line['name'] . '</a><br />';
                        }
                        $cus_id = get_post_meta( $order_id, '_customer_user', true );
                        $customer = new WC_Customer( $cus_id );
                        $name = ( $customer->get_first_name() != '' || $customer->get_last_name() != '' ? esc_html( $customer->get_first_name() . ' ' . $customer->get_last_name() ) : esc_html( $customer->get_display_name() ) );
                        //$name = $order->get_billing_first_name().' '.$order->get_billing_last_name();
                        
                        if ( $order->get_user_id() > 0 ) {
                            $url = admin_url() . 'admin.php?page=pd-woo-customer&user_id=' . $order->get_user_id();
                            $customer_name = '<span class="dashicons dashicons-admin-users"></span><strong><a href="' . $url . '" target="_blank">' . $name . '</a></strong>';
                        } else {
                            $customer_name = '<span class="dashicons dashicons-businessman"></span><strong>Guest</strong>';
                        }
                        
                        $this->last_orders[] = array(
                            'order_date'     => '<span class="dashicons dashicons-calendar-alt"></span>
&nbsp;' . get_the_date( '', $order_id ) . ' ' . get_the_time( '', $order_id ),
                            'order_id'       => '<a href="' . admin_url() . 'post.php?post=' . $order_id . '&action=edit" class="">#' . $order_id . '</a>',
                            'customer_name'  => $customer_name,
                            'items_qty'      => $items_qty,
                            'order_total'    => $order->get_formatted_order_total(),
                            'payment_method' => $order->get_payment_method(),
                            'order_status'   => '<span class="pd-woo-order-status ' . str_replace( 'wc-', '', $order->get_status() ) . '">' . ucfirst( str_replace( 'wc-', '', $order->get_status() ) ) . '</span>',
                        );
                    }
                
                }
            }
            usort( $this->last_orders, array( &$this, 'sort_data' ) );
            $results_per_page = get_option( 'pd-woo-results_per_page' );
            
            if ( !empty($_GET['show-items']) ) {
                $perPage = sanitize_text_field( $_GET['show-items'] );
            } else {
                $perPage = ( !empty($results_per_page) ? $results_per_page : 10 );
            }
            
            $perPage = (int) $perPage;
            $currentPage = $this->get_pagenum();
            $totalItems = count( $this->last_orders );
            $this->set_pagination_args( array(
                'total_items' => $totalItems,
                'per_page'    => $perPage,
            ) );
            if ( !empty($this->last_orders) ) {
                $this->last_orders = array_slice( $this->last_orders, ($currentPage - 1) * $perPage, $perPage );
            }
            $this->items = $this->last_orders;
        }
        
        public function column_default( $item, $column_name )
        {
            switch ( $column_name ) {
                case 'order_date':
                case 'order_id':
                case 'customer_name':
                case 'items_qty':
                case 'order_total':
                case 'payment_method':
                case 'order_status':
                    return $item[$column_name];
                default:
                    return print_r( $item, true );
                    //Show the whole array for troubleshooting purposes
            }
        }
        
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'order_date'     => array( 'order_date', false ),
                'order_id'       => array( 'order_id', false ),
                'customer_name'  => array( 'customer_name', false ),
                'order_total'    => array( 'order_total', false ),
                'payment_method' => array( 'payment_method', false ),
                'order_status'   => array( 'order_status', false ),
            );
            return $sortable_columns;
        }
        
        private function sort_data( $a, $b )
        {
            // Set defaults
            $orderby = 'order_date';
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
         * Get latest orders.
         *
         * @since    1.0.0
         * @access   public
         */
        public function pdwchs_get_latest_orders( $search, $advanceSearchTerms )
        {
            $order_status = array(
                'pending',
                'processing',
                'on-hold',
                'completed',
                'cancelled',
                'refunded',
                'failed',
                'wc-pending',
                'wc-processing',
                'wc-on-hold',
                'wc-completed',
                'wc-cancelled',
                'wc-refunded',
                'wc-failed'
            );
            $args = array(
                'numberposts' => -1,
                'post_type'   => 'shop_order',
                'post_status' => $order_status,
            );
            $latest_orders = get_posts( $args );
            return $latest_orders;
        }
        
        /**
         * Get last orders.
         *
         * @since    1.0.0
         * @access   public
         */
        public static function pdwchs_get_last_orders( $limit = '' )
        {
            $order_status = array(
                'pending',
                'processing',
                'on-hold',
                'completed',
                'cancelled',
                'refunded',
                'failed',
                'wc-pending',
                'wc-processing',
                'wc-on-hold',
                'wc-completed',
                'wc-cancelled',
                'wc-refunded',
                'wc-failed'
            );
            $args = array(
                'numberposts' => $limit,
                'post_type'   => 'shop_order',
                'post_status' => $order_status,
            );
            $last_orders = get_posts( $args );
            return $last_orders;
        }
    
    }
}