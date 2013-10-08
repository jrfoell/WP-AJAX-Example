<?php
/* Plugin Name: AJAX Example */

//Front-end example
// load page with form & enqueue javascript
function wordup_template_example_page( $template ) {
	if ( is_page( 'ajax-example' ) ) {
		wp_register_script( 'wordup-ajax-html', plugins_url( 'html-kittens.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script ('wordup-ajax-html' );
		wp_localize_script( 'wordup-ajax-html', 'siteSettings',
							array(
								'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
							)
		);
		
		return dirname( __FILE__ ) . '/page-template.php';
	}
	return $template;
}
add_filter( 'template_include', 'wordup_template_example_page' );

// process request
function wordup_ajax_request_html() {
	//'http://placekitten.com/g/300/300'
	$sizes = range( 400, 200, 10 );
	$keys = array_rand( $sizes, 2 );
	echo "<img src='http://placekitten.com/{$sizes[$keys[0]]}/{$sizes[$keys[1]]}' />";
	exit;
}
add_action( 'wp_ajax_wordup-html-request', 'wordup_ajax_request_html' );

function wordup_ajax_nopriv_request_html() {
	echo "<img src='http://placekitten.com/g/300/300' />";
	exit;
}
add_action( 'wp_ajax_nopriv_wordup-html-request', 'wordup_ajax_nopriv_request_html' );



//WP-Admin example
// helper function
function wordup_get_user() {
	return empty( $_GET['user_id'] ) ? wp_get_current_user() : get_user_by( 'id', $_GET['user_id'] );
}

// show form
function wordup_user_active_form() {
	$user = wordup_get_user();
	$active = $user->user_status == 0;
	$bgcolor = $active ? 'green' : 'red';
	$text = $active ? 'Active' : 'Inactive';
	?>
	<tr>
	<th><label for="activate">Active Status</label></th>
	<td>
		<input type="button" value="Toggle" size="16" id="activate" name="" />
		<span id="active-status" style="color: white; padding: 0.5em; background-color: <?php echo $bgcolor ?>;"><?php echo $text ?></span>
	</td>
	</tr>
	<?php
}
add_action( 'personal_options', 'wordup_user_active_form' );

// enqueue javascript
function wordup_queue_profile_js( $screen ) {
	if ( $screen->base == 'user-edit' || $screen->base == 'profile' ) {
		$user = wordup_get_user();
		wp_register_script( 'wordup-ajax-json', plugins_url( 'json-activate.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script ('wordup-ajax-json' );		
		wp_localize_script( 'wordup-ajax-json', 'adminSettings',
							array(
								'user_id' => $user->ID
							)
		);
	}
}
add_action( 'current_screen', 'wordup_queue_profile_js' );

// process request
function wordup_ajax_request_json() {
	/**
	 * The user_status column is not actively used by WordPress, but
	 * you could check it with the wp_authenticate_user filter to
	 * de-authorize logins
	 */ 
	$user = get_user_by( 'id', $_POST['user_id'] );
	$new_status = $user->user_status == 0 ? 2 : 0;

	//from wp-admin/includes/ms.php update_user_status()	
	global $wpdb;
    $wpdb->update( $wpdb->users, array( 'user_status' =>  $new_status), array( 'ID' => $user->ID ) );
	
	$status = array(
		'active' => $new_status == 0 ? true : false
	);
	
	echo json_encode( $status );
	exit;
}
add_action( 'wp_ajax_wordup-json-request', 'wordup_ajax_request_json' );



