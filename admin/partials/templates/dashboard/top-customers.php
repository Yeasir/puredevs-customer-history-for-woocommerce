<div class="pd-woo-main-container">
    <div class="pd-woo-panel">
        <div class="pd-woo-header pd-woo-clearfix">
            <h2 class="pd-woo-float-left"><span class="dashicons dashicons-groups"></span>&nbsp;&nbsp;
                <?php echo esc_html__( 'Top Customers', 'pd-customer-history' ); ?>
            </h2>
            <div class="chart-table-toggle pd-woo-float-right">
                <span class="pd-toggle-btn pd-active-btn" data-target="pd-table-content">
                    <span class="dashicons dashicons-editor-table" title="<?php echo esc_attr__( 'Table View', 'pd-customer-history' ); ?>"></span>
				</span>
                <span class="pd-toggle-btn" data-target="pd-chart-content">
                    <span class="dashicons dashicons-chart-pie" title="<?php echo esc_attr__( 'Chart View', 'pd-customer-history' ); ?>"></span>
                </span>
                <span>
                    <a href="<?php echo admin_url(); ?>admin.php?page=pd-woo-customers" class="view-all-link">
                        <span class="dashicons dashicons-admin-site-alt2" title="<?php echo esc_attr__( 'View All', 'pd-customer-history' ); ?>"></span>
                    </a>
				</span>
                <span class="toggle-section toggle-btn pd-woo-toggle-btn" data-toggle-content="section-top-users">
					<span class="dashicons dashicons-arrow-down-alt2" title="<?php echo esc_attr__( 'Click to Expend', 'pd-customer-history' ); ?>"></span>
                </span>
            </div>
        </div>
        <div id="section-top-users" class="pd-woo-clearfix">
            <table class="wp-list-table widefat fixed striped posts pd-table-content">
                <tr>
                    <th><?php echo esc_html__( 'Customer', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Total Amount', 'pd-customer-history' ); ?></th>
                    <th><?php echo esc_html__( 'Total Orders', 'pd-customer-history' ); ?></th>
                </tr>
		        <?php
		        $top_customers = array();
		        $customers     = PD_Customer_History_Customers::pdwchs_woo_get_all_customers( '', '' );

		        if ( get_option( 'pd-woo-hide_users_with_no_orders' ) ) {
		            if( !empty( $customers ) ):
                        foreach ( $customers as $key => $user ) {
                            $order_count = count( get_posts( array(
                                'numberposts' => - 1,
                                'meta_key'    => '_customer_user',
                                'meta_value'  => $user->ID,
                                'post_type'   => 'shop_order',
                                'post_status' => 'any',
                                'post_parent' => '0',
                            ) ) );
                            if ( ! $order_count > 0 ) {
                                unset( $customers[ $key ] );
                            }
                        }
			        endif;
		        }

		        if ( !empty( $customers ) ) :
                    foreach ( $customers as $customer ) {
                        $total_spent                 = PD_Customer_History_Customers::pdwchs_woo_get_customer_total_spent( $customer->ID );
                        $order_count                 = count( get_posts( array(
                            'numberposts' => - 1,
                            'meta_key'    => '_customer_user',
                            'meta_value'  => $customer->ID,
                            'post_type'   => 'shop_order',
                            'post_status' => 'any',
                            'post_parent' => '0',
                        ) ) );
                        $top_customer['user_id']     = $customer->ID;
                        $top_customer['user_name']   = ( $customer->first_name != '' || $customer->last_name != '' ) ? esc_html( $customer->first_name . ' ' . $customer->last_name ) : esc_html( $customer->display_name );
                        $top_customer['total_spent'] = $total_spent;
                        $top_customer['order_count'] = $order_count;
                        array_push( $top_customers, $top_customer );
                    }
		        endif;
		        $spent = array();
		        if ( !empty( $top_customers ) ) :
                    foreach ( $top_customers as $key => $row ) {
                        $spent[ $key ] = $row['total_spent'];
                    }
                    array_multisort( $spent, SORT_DESC, $top_customers );
                    $top_customers = array_slice( $top_customers, 0, 5 );

                    $t_cus = array();
                    foreach ( $top_customers as $customer ) {
                        $t_cus[] = array(
                                'username' => $customer['user_name'],
                                'totalspent' => $customer['total_spent'],
                            );
                        ?>
                        <tr>
                            <td>
                                <span class="dashicons dashicons-admin-users"></span>
                                <a href="admin.php?page=pd-woo-customer&user_id=<?php echo intval( $customer['user_id'] ); ?>"
                                   target="_blank">
                                    <?php echo esc_html__( $customer['user_name'], 'pd-customer-history' ); ?>
                                </a>
                            </td>
                            <td><?php echo __(  get_woocommerce_currency_symbol() .$customer['total_spent'], 'pd-customer-history' ); ?></td>
                            <td><?php echo esc_html__( $customer['order_count'], 'pd-customer-history' ); ?></td>
                        </tr>
                    <?php } ?>
                <?php endif;?>
            </table>
            <script>
                jQuery(window).load(function() {
                    var chart = am4core.create("top-customers", am4charts.PieChart);
                    chart.data = <?php echo json_encode($t_cus);?>;
                    var pieSeries = chart.series.push(new am4charts.PieSeries());
                    pieSeries.dataFields.value = "totalspent";
                    pieSeries.dataFields.category = "username";
                });
            </script>
            <div class="pd-chart-content">
                <div id="top-customers"></div>
            </div>
        </div>
    </div>
</div>