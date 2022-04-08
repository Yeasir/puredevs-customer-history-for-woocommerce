<?php

/**
 * Template for customer history dashboard.
 */
include 'top-nav.php';
?>

<div id="pd-customer-history">
	<div id="dashboard" class="wrap">
		<h1><?php 
echo  esc_html__( 'Dashboard', 'pd-customer-history' ) ;
?></h1>
		<div class="pd-woo-dashboard">
			<?php 
include 'dashboard/top-customers.php';
include 'dashboard/last-orders.php';
?>
		</div>
	</div>
</div>
