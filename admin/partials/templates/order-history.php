<?php

defined( 'ABSPATH' ) or exit;
include 'top-nav.php';
?>
<div id="pd-woocommerce-order-history">
	<div id="orders" class="wrap">
		<h1><?php 
echo  esc_html__( 'Order History', 'pd-customer-history' ) ;
?></h1>

        <?php 
?>
        <?php 
$orders = new PD_Customer_Order_History();
$orders->prepare_items();
$orders->display();
?>
	</div>
</div>
