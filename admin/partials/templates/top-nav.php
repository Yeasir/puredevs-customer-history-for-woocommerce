<?php

defined( 'ABSPATH' ) or exit;
/*
 *	Plugin Top Navigation
 */
?>
<h2 class="nav-tab-wrapper top-nav">
    <a href="admin.php?page=pd-woo-dashboard" class="nav-tab <?php 
if ( isset( $_GET['page'] ) && $_GET['page'] == 'pd-woo-dashboard' ) {
    ?>nav-tab-active<?php 
}
?>" id="pd-dashboard-tab"><span class="dashicons dashicons-dashboard"></span><?php 
echo  esc_html__( 'Dashboard', 'pd-customer-history' ) ;
?></a>
    <a href="admin.php?page=pd-woo-customers" class="nav-tab <?php 
if ( isset( $_GET['page'] ) && $_GET['page'] == 'pd-woo-customers' ) {
    ?>nav-tab-active<?php 
}
?>" id="pd-customers-tab"><span class="dashicons dashicons-admin-users"></span><?php 
echo  esc_html__( 'Customers & Users', 'pd-customer-history' ) ;
?></a>
    <a href="admin.php?page=pd-woo-order-history" class="nav-tab <?php 
if ( isset( $_GET['page'] ) && $_GET['page'] == 'pd-woo-order-history' ) {
    ?>nav-tab-active<?php 
}
?>" id="pd-order-history-tab"><span class="dashicons dashicons-clock"></span><?php 
echo  esc_html__( 'Order History', 'pd-customer-history' ) ;
?></a>
    <?php 
?>
    <a href="admin.php?page=pd-woo-settings" class="nav-tab <?php 
if ( isset( $_GET['page'] ) && $_GET['page'] == 'pd-woo-settings' ) {
    ?>nav-tab-active<?php 
}
?>" id="pd-settings-tab"><span class="dashicons dashicons-admin-settings"></span><?php 
echo  esc_html__( 'Settings', 'pd-customer-history' ) ;
?></a>
</h2>
