<div class="pd-woo-main-container">
    <div class="pd-woo-panel">
        <div class="pd-woo-header pd-woo-clearfix">
            <h2 class="pd-woo-float-left"><span class="dashicons dashicons-cart"></span>&nbsp;&nbsp;
			    <?php echo esc_html__( 'Latest Orders', 'pd-customer-history' ); ?>
            </h2>
            <div class="chart-table-toggle pd-woo-float-right">
                <span>
                    <a href="<?php echo admin_url(); ?>admin.php?page=pd-woo-order-history" class="view-all-link">
                        <span class="dashicons dashicons-admin-site-alt2" title="<?php echo esc_attr__( 'View All', 'pd-customer-history' );?>"></span>
                    </a>
				</span>
                <span class="toggle-section toggle-btn pd-woo-toggle-btn" data-toggle-content="section-last-orders">
                    <span class="dashicons dashicons-arrow-down-alt2" title="<?php echo esc_attr__( 'Click to Expend', 'pd-customer-history' );?>"></span>
                </span>
            </div>
        </div>
        <div id="section-last-orders" class="pd-woo-clearfix">
            <table class="wp-list-table widefat fixed striped posts pd-table-content">
                <tr>
                    <th><?php echo esc_html__( 'Date', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Order ID', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Customer Name', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Amount', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Status', 'pd-customer-history' ); ?></th>
                </tr>
				<?php
				$last_orders = PD_Customer_Order_History::pdwchs_get_last_orders( 5 );

                if ( !empty( $last_orders ) ) :
                    foreach ( $last_orders as $order ) {
                        if ( wp_get_post_parent_id( $order->ID ) == 0 ) {
                            $order     = new WC_Order( $order->ID );
                            $order_id  = $order->get_id();

                            $cus_id = get_post_meta($order_id, '_customer_user', true);

                            $customer = new WC_Customer( $cus_id );

                            $name = ( $customer->get_first_name() != '' || $customer->get_last_name() != '' ) ? esc_html( $customer->get_first_name() . ' ' . $customer->get_last_name() ) : esc_html( $customer->get_display_name() );

                            $items_qty = '';
                            foreach ( $order->get_items() as $line ) {
                                $items_qty .= $line['qty'] . ' x <a href="' . admin_url() . 'post.php?post=' . $line['product_id'] . '&action=edit" class="">' . $line['name'] . '</a><br />';
                            }
                            //$name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                            if( $order->get_user_id() > 0 ) :
                                $user_icon = 'dashicons dashicons-admin-users';
                            else:
                                $name = 'Guest';
                                $user_icon = 'dashicons dashicons-businessman';
                            endif;
                            ?>
                            <tr>
                                <td>
                                    <span class="dashicons dashicons-calendar-alt"></span>
                                    <?php echo __( get_the_date( '', $order_id ).' '. get_the_time( '', $order_id ), 'pd-customer-history' ); ?>
                                </td>
                                <td>
                                    <a href="<?php admin_url(); ?>post.php?post=<?php echo intval( $order_id ); ?>&action=edit" target="_blank" class="">
                                        <?php echo esc_html__( '#'.$order_id, 'pd-customer-history' ); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="<?php echo esc_attr( $user_icon );?>"></span>
                                    <?php echo esc_html__( $name, 'pd-customer-history' ); ?>
                                </td>
                                <td><?php echo __( $order->get_formatted_order_total(), 'pd-customer-history' ); ?></td>
                                <td><span class="pd-woo-order-status <?php echo str_replace( 'wc-', '', $order->get_status() );?>"><?php echo __( ucfirst( str_replace( 'wc-', '', $order->get_status() ) ), 'pd-customer-history' ); ?></span></td>
                            </tr>
                        <?php }
                    }
				endif;
				?>
            </table>
        </div>
    </div>
</div>