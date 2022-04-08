<?php

defined( 'ABSPATH' ) or exit;
/*
 *	Plugin Settings
 */
?>

<?php 
include 'top-nav.php';
?>
<div id="pd-customer-history">
	<div id="settings" class="wrap">
		<h1><?php 
echo  esc_html__( 'Settings', 'pd-customer-history' ) ;
?></h1>
		<hr />
        <?php 
if ( isset( $_POST['act'] ) && $_POST['act'] == 'save' ) {
    
    if ( !isset( $_POST['save_customer_history_settings'] ) || !wp_verify_nonce( $_POST['save_customer_history_settings'], 'save_customer_history_settings_action' ) ) {
        ?>
                <div class="error">
                    <p><?php 
        esc_html_e( 'Security check failed!', 'pd-customer-history' );
        ?></p>
                </div>
                <?php 
    } else {
        $results_per_page_input = sanitize_text_field( $_POST['results_per_page'] );
        $hide_users_with_no_orders_input = sanitize_text_field( $_POST['hide_users_with_no_orders'] );
        $result_per_page = update_option( 'pd-woo-results_per_page', $results_per_page_input );
        $hide_users_with_no_order = update_option( 'pd-woo-hide_users_with_no_orders', $hide_users_with_no_orders_input );
        
        if ( $result_per_page || $save_sessions || $hide_users_with_no_order || $show_bot_sessions ) {
            ?>
                    <div class="updated">
                        <p><?php 
            esc_html_e( 'Settings Saved', 'pd-customer-history' );
            ?></p>
                    </div>
                    <?php 
        }
    
    }

}
?>
		<form id="group-form" class="settings-form" action="admin.php?page=pd-woo-settings" method="post">
            <?php 
wp_nonce_field( 'save_customer_history_settings_action', 'save_customer_history_settings' );
?>
			<input type="hidden" name="act" value="save">
			<table class="form-table">
				<tbody>
                <tr>
                    <th scope="row"><label for="results_per_page"><?php 
echo  esc_html__( 'Results per page :', 'pd-customer-history' ) ;
?></label></th>
                    <td><input name="results_per_page" type="number" class="small-text" placeholder="10" value="<?php 
echo  get_option( 'pd-woo-results_per_page' ) ;
?>"></td>
                </tr>

                <?php 
?>

				<tr>
					<th scope="row"><label for="hide_users_with_no_orders"><?php 
echo  esc_html__( 'Hide users with no orders :', 'pd-customer-history' ) ;
?></label></th>
					<td>
						<select name="hide_users_with_no_orders">
							<option value="0"><?php 
echo  esc_html__( 'No', 'pd-customer-history' ) ;
?></option>
							<option value="1"<?php 
echo  ( get_option( 'pd-woo-hide_users_with_no_orders' ) ? ' selected="selected"' : '' ) ;
?>><?php 
echo  esc_html__( 'Yes', 'pd-customer-history' ) ;
?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><input type="submit" value="<?php 
echo  esc_attr__( 'Save', 'pd-customer-history' ) ;
?>" class="button button-primary button-large"></th>
					<td></td>
				</tr>
				</tbody>
			</table>

		</form>
        <?php 
?>
	</div>
</div>
