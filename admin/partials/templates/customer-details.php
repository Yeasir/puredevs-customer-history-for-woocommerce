<?php

defined( 'ABSPATH' ) or exit;
/*
 *  Customer
 */
global  $wpdb ;
?>
<div id="pd-customer-history">
    <?php 
$user_id = ( isset( $_GET['user_id'] ) ? sanitize_text_field( $_GET['user_id'] ) : 0 );

if ( $user_id > 0 && current_user_can( 'manage_woocommerce' ) ) {
    $user = get_user_by( 'id', $user_id );
    ?>

        <div id="customer" class="wrap">
            <h1><?php 
    echo  esc_html__( 'Customer Details', 'pd-customer-history' ) ;
    ?></h1>
            <div class="pd-ch-dashboard">
                <div class="pd-ch-main-container">
                    <?php 
    $orderStatuses = wc_get_order_statuses();
    
    if ( !empty($orderStatuses) ) {
        $counter = 1;
        foreach ( $orderStatuses as $key => $value ) {
            $records = get_posts( array(
                'numberposts' => 10,
                'meta_key'    => '_customer_user',
                'meta_value'  => $user_id,
                'post_type'   => 'shop_order',
                'post_status' => array( str_replace( 'wc-', '', $key ), $key ),
            ) );
            
            if ( !empty($records) ) {
                ?>
                                <div class="pd-ch-panel">
                                    <h2 class="status-<?php 
                echo  esc_attr( $key ) ;
                ?>"><span class="dashicons dashicons-cart"></span>&nbsp;&nbsp;<?php 
                echo  esc_html__( '' . $value . ' Orders', 'pd-customer-history' ) ;
                ?></h2>
                                    <span class="toggle-section toggle-btn pd-ch-toggle-btn pd-woo-toggle-btn" data-toggle-content="section<?php 
                echo  esc_attr( $counter ) ;
                ?>"><span class="dashicons dashicons-arrow-down-alt2" title="<?php 
                echo  esc_attr__( 'Click to Expend', 'pd-customer-history' ) ;
                ?>"></span></span>
                                    <div id="section<?php 
                echo  esc_attr( $counter ) ;
                ?>" class="pd-ch-clearfix">
                                        <table class="wp-list-table widefat fixed striped posts">
                                            <tr>
                                                <th><?php 
                echo  esc_html__( 'Order', 'pd-customer-history' ) ;
                ?></th>
                                                <th><?php 
                echo  esc_html__( 'Date', 'pd-customer-history' ) ;
                ?></th>
                                                <th><?php 
                echo  esc_html__( 'Items', 'pd-customer-history' ) ;
                ?></th>
                                                <th><?php 
                echo  esc_html__( 'Total', 'pd-customer-history' ) ;
                ?></th>
                                                <th><?php 
                echo  esc_html__( 'Actions', 'pd-customer-history' ) ;
                ?></th>
                                            </tr>

                                            <?php 
                foreach ( $records as $order ) {
                    
                    if ( wp_get_post_parent_id( $order->ID ) == 0 ) {
                        $order = new WC_Order( $order->ID );
                        $order_id = $order->get_id();
                        $order_date = $order->get_date_created();
                        ?>
                                                    <tr>
                                                        <td><a href="<?php 
                        echo  admin_url() ;
                        ?>post.php?post=<?php 
                        echo  intval( $order_id ) ;
                        ?>&action=edit" class="">#<?php 
                        echo  intval( $order_id ) ;
                        ?></a></td>
                                                        <td><?php 
                        echo  get_the_date( '', $order_id ) ;
                        ?> <?php 
                        echo  get_the_time( '', $order_id ) ;
                        ?></td>
                                                        <td><?php 
                        foreach ( $order->get_items() as $line ) {
                            echo  intval( $line['qty'] ) . ' x <a href="' . admin_url() . 'post.php?post=' . intval( $line['product_id'] ) . '&action=edit" class="">' . esc_html( $line['name'] ) . '</a><br />' ;
                        }
                        ?></td>
                                                        <td>
                                                            <?php 
                        $args = array(
                            'span' => array(
                            'class' => true,
                        ),
                        );
                        echo  wp_kses( $order->get_formatted_order_total(), $args ) ;
                        ?>
                                                        </td>
                                                        <td><a href="<?php 
                        echo  admin_url() ;
                        ?>post.php?post=<?php 
                        echo  intval( $order_id ) ;
                        ?>&action=edit" class="button"><?php 
                        echo  esc_html__( 'View', 'pd-customer-history' ) ;
                        ?></a></td>
                                                    </tr>
                                                    </tr>
                                                <?php 
                    }
                    
                    ?>
                                            <?php 
                }
                ?>
                                        </table>
                                    </div>
                                </div>
                            <?php 
            }
            
            ?>
                            <?php 
            $counter++;
            ?>
                        <?php 
        }
        ?>
                    <?php 
    }
    
    ?>
                    <?php 
    ?>
                </div>
                <div class="pd-ch-side-container pd-ch-side-container-sidebar">
                    <section class="pd-ch-panel">
                        <h2><?php 
    echo  esc_html__( 'Financial Summary', 'pd-customer-history' ) ;
    ?></h2>
                        <div class="customer-info">
                            <table>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Total Orders', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <?php 
    $order_count = count( get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'post_type'   => 'shop_order',
        'post_status' => 'any',
        'post_parent' => '0',
    ) ) );
    ?>
                                        <strong><?php 
    echo  intval( $order_count ) ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Pending Orders', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <?php 
    $pending_orders_count = count( get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'post_type'   => 'shop_order',
        'post_status' => array( 'pending', 'wc-pending' ),
        'post_parent' => '0',
    ) ) );
    ?>
                                        <strong><?php 
    echo  intval( $pending_orders_count ) ;
    ?></strong><br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Pending Orders Amount', 'pd-customer-history' ) ;
    ?>:<br />
                                    </td>
                                    <td>
                                        <?php 
    $pending_orders_total = PD_Customer_History_Customers::pdwchs_woo_get_customer_pending_orders_total( $user->ID );
    ?>
                                        <strong><?php 
    echo  get_woocommerce_currency_symbol() . '' . $pending_orders_total ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Refunded Orders', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <?php 
    $refunded_orders_count = count( get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'post_type'   => 'shop_order',
        'post_status' => array( 'refunded', 'wc-refunded' ),
        'post_parent' => '0',
    ) ) );
    ?>
                                        <strong><?php 
    echo  intval( $refunded_orders_count ) ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Refunded Orders Amount', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <?php 
    $refunded_orders_total = PD_Customer_History_Customers::pdwchs_woo_get_customer_refunded_orders_total( $user->ID );
    ?>
                                        <strong><?php 
    echo  get_woocommerce_currency_symbol() . '' . $refunded_orders_total ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Orders average', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <?php 
    $total_spent = PD_Customer_History_Customers::pdwchs_woo_get_customer_total_spent( $user->ID );
    ?>
                                        <strong>
                                            <?php 
    
    if ( $order_count > 0 ) {
        echo  wc_price( $total_spent / $order_count ) ;
    } else {
        echo  wc_price( $total_spent ) ;
    }
    
    ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Total Spent', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <strong><?php 
    echo  wc_price( $total_spent ) ;
    ?></strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="clear"></div>
                    </section>
                    <section class="pd-ch-panel">
                        <h2><?php 
    echo  esc_html__( 'Personal Information', 'pd-customer-history' ) ;
    ?></h2>
                        <div class="customer-avatar"><?php 
    echo  get_avatar( $user_id ) ;
    ?></div>
                        <div class="customer-info">
                            <table>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'User ID', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <strong><?php 
    echo  intval( $user->ID ) ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Name', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <strong><?php 
    echo  esc_html( $user->display_name ) ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Username', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <strong><?php 
    echo  esc_html( $user->user_login ) ;
    ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Email', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <a href="mailto:<?php 
    echo  esc_attr( $user->user_email ) ;
    ?>"><?php 
    echo  esc_html( $user->user_email ) ;
    ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo  esc_html__( 'Website', 'pd-customer-history' ) ;
    ?>:
                                    </td>
                                    <td>
                                        <a href="<?php 
    echo  esc_url( $user->user_url ) ;
    ?>" target="_blank"><?php 
    echo  esc_url( $user->user_url ) ;
    ?></a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="customer-actions">
                            <table>
                                <tr>
                                    <td>
                                        <span class="dashicons dashicons-edit"></span>&nbsp;<a href="<?php 
    echo  get_edit_user_link( $user->ID ) ;
    ?>"><?php 
    echo  esc_html__( 'Edit this user', 'pd-customer-history' ) ;
    ?></a>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <div class="clear"></div>
                    </section>
                </div>

                <div class="pd-ch-clearfix  "></div>
            </div>

        </div>

    <?php 
} else {
    ?>

        <div class="wrap">
            <h1><?php 
    echo  esc_html__( 'Error', 'pd-customer-history' ) ;
    ?></h1>
            <p><?php 
    echo  esc_html__( "I'm sorry, you are not allowed to see this page!", 'pd-customer-history' ) ;
    ?><br /><br /><a href="#"><?php 
    echo  esc_html__( 'Back', 'pd-customer-history' ) ;
    ?></a></p>
        </div>

    <?php 
}

?>

</div>
