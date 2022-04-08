<?php

defined( 'ABSPATH' ) or exit;
include 'top-nav.php';
?>
<div id="pd-woocommerce-customer-history">
	<div id="customers" class="wrap">
		<h1><?php 
echo  esc_html__( 'Customer List', 'pd-customer-history' ) ;
?></h1>
        <?php 
?>
        <?php 
$customer = new PD_Customer_History_Customers();
$customer->prepare_items();
$customer->display();
?>
	</div>
</div>
