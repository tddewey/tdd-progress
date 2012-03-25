<?php

/*
* Array of color options
*/
$colors = array (
	'strawberry' => __ ( 'Strawberry', 'tdd_pb' ),
	'fuchsia' => __( 'Fuchsia', 'tdd_pb' ),
	'purple' => __( 'Purple', 'tdd_pb' ),
	'blue' => __( 'Blue', 'tdd_pb' ),
	'lightblue' => __( 'Light Blue', 'tdd_pb' ),
	'teal' => __( 'Teal', 'tdd_pb' ),
	'green' => __( 'Green', 'tdd_pb' ),
	'yellow' => __( 'Yellow', 'tdd_pb' ),
	'orange' => __( 'Orange', 'tdd_pb' ),
	'red' => __( 'Red', 'tdd_pb' ),
	'black' => __( 'Black', 'tdd_pb' ),
	'silver' => __( 'Silver', 'tdd_pb' ),
);

/*
* Setup custom meta boxes for the tdd_bp custom post type page
*/
function tdd_pb_metabox_create() {
	add_meta_box( 'tdd_pb_meta', __( 'Progress Bar Options' ), 'tdd_pb_metabox_display', 'tdd_pb' );
}
add_action( 'add_meta_boxes', 'tdd_pb_metabox_create' );


/**
 * Meta box display for the Progress Bar post type.
 *
 * Provides the form controls necessary to select the color of the bar as well as:
 * Percentage input
 * X of Y input
 * Label Display mode (only applicable if global show percentages option is on):
 *  - None
 *   - Percentage only
 *   - Text label
 *  - Percentage (text label)
 *  - Text label (percentage)
 */
function tdd_pb_metabox_display( $post ) {
	$tdd_pb_color = get_post_meta( $post->ID, '_tdd_pb_color', true );
	$tdd_pb_percentage = get_post_meta( $post->ID, '_tdd_pb_percentage', true );
	$tdd_pb_input_method = get_post_meta( $post->ID, '_tdd_pb_input_method', true );
	$tdd_pb_start = get_post_meta( $post->ID, '_tdd_pb_start', true );
	$tdd_pb_end = get_post_meta( $post->ID, '_tdd_pb_end', true );
	$tdd_pb_percentage_display = get_post_meta( $post->ID, '_tdd_pb_percentage_display', true );
	$tdd_pb_xofy_display = get_post_meta( $post->ID, '_tdd_pb_xofy_display', true );
?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="tdd_pb_color"><?php _e( 'Bar Color', 'tdd_pb' ); ?></label></th>
			<td><select name="tdd_pb_color">
				<?php global $colors; ?>
				<?php foreach ( $colors as $color=>$label ): ?>
					<option value="<?php echo $color; ?>" <?php selected( $tdd_pb_color, $color ); ?>"><?php echo $label; ?></option>
				<?php endforeach; ?>
				</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Calculation Mode', 'tdd_pb' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Input Method and Percentage Calculation', 'tdd_pb' ); ?></span></legend>

					<table class="form-table">
						<tr valign="top">
							<td>
								<label><input name="tdd_pb_input_method" type="radio" <?php checked( $tdd_pb_input_method, 'percentage' ); ?> value="percentage" > <?php _e( 'Percentage', 'tdd_pb' ); ?></label>
							</td>
							<td>
								<input name="tdd_pb_percentage" type="text" size="2" value="<?php echo esc_attr( $tdd_pb_percentage ); ?>"> %							</td>
						</tr>
						<tr valign="top">
							<td>
								<label><input name="tdd_pb_input_method" type="radio" <?php checked( $tdd_pb_input_method, 'xofy' ); ?> value="xofy" > <?php _e( 'Calculate Percentage (x of y)', 'tdd_pb' ); ?></label>
							</td>
							<td>
								<input name="tdd_pb_start" type="text" size="10" value="<?php echo esc_attr( $tdd_pb_start ); ?>"> <?php _e( 'of', 'tdd_pb' ); ?>
								<input name="tdd_pb_end" type="text" size="10" value="<?php echo esc_attr( $tdd_pb_end ); ?>"><br />
								<span class="description"><?php _e( "Numbers only, don't include units", 'tdd_pb' ); ?></span>
							</td>
						</tr>
					</table>
			</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Display the following on the progress bar:', 'tdd_pb' ); ?></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Display the following on the progress bar:', 'tdd_pb' ); ?></span></legend>
					<label><input name="tdd_pb_percentage_display" type="checkbox"	<?php checked( $tdd_pb_percentage_display, 'on' ); ?>> <?php _e( 'Display Calculated Percentage', 'tdd_pb' ); ?><label><br />
					<label><input name="tdd_pb_xofy_display" type="checkbox" <?php checked( $tdd_pb_xofy_display, 'on' ); ?> > <?php _e( 'Display x of y Values', 'tdd_pb' ); ?></label>
				</fieldset>
			</td>
		</tr>
	</table>

	<?php $id = get_the_ID(); ?>
	<p><?php _e( "Example shortcode: <code>[progress id={$id}]</code>", 'tdd_pb' ); ?> </p>

	<?php echo tdd_pb_get_bars( array(
			'ids' => array( get_the_ID() ),
			'class' => 'tdd_pb_race',
		) );
?>

<?php
}

/**
 * tdd_float_number_only
 * Takes the input from a form field that expects a number, strips out everything except for numbers and decimals (.)
 * Used for sanitation, not validation purposes
 * @param string|int $str The input to filter.
 * @return str The returned string
 */
function tdd_float_number_only( $str ){
	$regexreturn = array();
	preg_match_all("/\.?[0-9]\.?/", $str, $regexreturn ); // Selects out just the numbers and any decimal places.
	$regexreturn = implode( '', $regexreturn[0] ); // Organizes the regex array back into a string.
	return $regexreturn;
}

/*
* Saves the meta box info for the post
*/
function tdd_pb_metabox_save( $post_id ) {
	if ( isset( $_POST['tdd_pb_color'] ) ) {
		update_post_meta( $post_id, '_tdd_pb_color', strip_tags( $_POST['tdd_pb_color'] ) );
	}

	if ( isset( $_POST['tdd_pb_percentage'] ) ) {
		$tdd_pb_percentage = tdd_float_number_only( $_POST['tdd_pb_percentage'] );
		update_post_meta( $post_id, '_tdd_pb_percentage', $tdd_pb_percentage );
	}

	if ( isset( $_POST['tdd_pb_start'] ) ) {
		$tdd_pb_start = tdd_float_number_only( $_POST['tdd_pb_start'] );
		update_post_meta( $post_id, '_tdd_pb_start', $tdd_pb_start );
	}

	if ( isset( $_POST['tdd_pb_end'] ) ) {
		$tdd_pb_end = tdd_float_number_only( $_POST['tdd_pb_end'] );
		update_post_meta( $post_id, '_tdd_pb_end', $tdd_pb_end );
	}

	if ( isset( $_POST['tdd_pb_input_method'] ) ){
		update_post_meta( $post_id, '_tdd_pb_input_method', strip_tags( $_POST['tdd_pb_input_method'] ) );
	}

	if ( isset( $_POST['tdd_pb_percentage_display'] ) ){
		update_post_meta( $post_id, '_tdd_pb_percentage_display', 'on' );
	} else {
		update_post_meta( $post_id, '_tdd_pb_percentage_display', 'off' );
	}

	if ( isset( $_POST['tdd_pb_xofy_display'] ) ){
		update_post_meta( $post_id, '_tdd_pb_xofy_display', 'on' );
	} else {
		update_post_meta( $post_id, '_tdd_pb_xofy_display', 'off' );
	}

	// $tdd_pb_input_method = get_post_meta( $post->ID, '_tdd_pb_input_method', true );
	// $tdd_pb_start = get_post_meta( $post->ID, '_tdd_pb_start', true );
	// $tdd_pb_end = get_post_meta( $post->ID, '_tdd_pb_end', true );
	// $tdd_pb_percentage_display = get_post_meta( $post->ID, '_tdd_pb_percentage_display', true );
	// $tdd_pb_xofy_display = get_post_meta( $post->ID, '_tdd_pb_xofy_display', true );



}
add_action( 'save_post', 'tdd_pb_metabox_save' );

/*
* Adds custom columns to the Progress Bars view
*/
function set_edit_tdd_pb_columns( $columns ) {
	unset( $columns['date'] );
	return array_merge( $columns,
		array( 'progress_bar' => 'Progress Bar',
			'shortcode' => 'Shortcode',
			'date' => __( 'Date' ),
		) );
}
add_filter( 'manage_edit-tdd_pb_columns' , 'set_edit_tdd_pb_columns' );


/*
* Put content in those custom columns
*/
function tdd_pb_custom_columns( $column ) {
	global $post;

	switch ( $column ) {
	case 'progress_bar':
		echo tdd_pb_get_bars( array(
				'ids' => array( $post->ID ),
				'class' => 'tdd_pb_race',
			) );

		break;
	case 'shortcode':
		echo '<code>[progress id='. $post->ID .']</code>';
		break;
	}
}
add_action( 'manage_posts_custom_column' , 'tdd_pb_custom_columns' );

/*
* Add some custom inputs to the Quick Edit Menu
* Normally this would require a hook into save_posts (because it doesn't do it automatically), but we've already done that for the custom meta box info.
*/
function tdd_pb_add_quick_edit( $column_name, $post_type ) {
	if ( $column_name != 'progress_bar' || $post_type != 'tdd_pb' ) return;
?>

	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="alignleft"><span class="title"><?php _e( 'Complete', 'tdd_pb' ); ?></span>
			<input name="tdd_pb_percentage" id="tdd_pb_percentage" type="text" value="" size="4" maxlength="3" />%
			</label>
		</div>
		<div class="inline-edit-col">
			<label class="alignright"><span class="title"><?php _e( 'Bar Color', 'tdd_pb' ); ?></span>
				<select name="tdd_pb_color" id="tdd_pb_color">
				<?php global $colors; ?>
				<?php foreach ( $colors as $color=>$label ): ?>
					<option value="<?php echo $color; ?>"><?php echo $label; ?></option>
				<?php endforeach; ?>
				</select>
			</label>
	</fieldset>
	<?php
}
add_action( 'quick_edit_custom_box', 'tdd_pb_add_quick_edit', 20, 2 );

/*
* Add the AJAX Handler for the Quick Edit screen values
*/
function tdd_pb_ajax_qe_handler() {
	global $wpdb;

	//If the incoming post id isn't numeric -- or isn't set, return an error.
	if ( !is_numeric( $_GET['post_id'] ) || !isset( $_GET['post_id'] ) ) {
		$json = array ( 'error' => 'Post ID not set' );
		echo json_encode( $json );
		die();
	}

	//Check for correct permissions, and verify nonce
	if ( !current_user_can( 'edit_posts' ) || !check_ajax_referer( "tdd_pb_get_{$_GET['post_id']}", 'nonce', false ) ) {
		$json = array ( 'error' => 'Permission Denied', 'GET' => $_GET );
		echo json_encode( $json );
		die();
	}

	$tdd_pb_percentage = get_post_meta( $_GET['post_id'], '_tdd_pb_percentage', true );
	$tdd_pb_color = get_post_meta( $_GET['post_id'], '_tdd_pb_color', true );

	$json = array(
		'percentage' => $tdd_pb_percentage,
		'color' => $tdd_pb_color
	);

	echo json_encode( $json );
	die();
}
add_action( 'wp_ajax_tdd_pb_get_custom_values', 'tdd_pb_ajax_qe_handler' );
/*
* Add some javascript to feed the correct values to the quick edit screen
*/
function tdd_pb_quick_edit_js() {
	global $current_screen;
	if ( ( $current_screen->id != 'edit-tdd_pb' ) || ( $current_screen->post_type != 'tdd_pb' ) ) return;
?>
	<script type="text/javascript">
		function set_inline_tdd_pb( post_id, nonce ){
			inlineEditPost.revert(); //Calls an internal handler to make all the regular values fix themselves appropriately.

			var ajaxdata = {
				action: 'tdd_pb_get_custom_values',
				nonce: nonce,
				post_id: post_id
				}

			jQuery.getJSON(ajaxurl, ajaxdata, function(response){

				//Set the percentage to be correct
				var tdd_pb_percentage = document.getElementById('tdd_pb_percentage');
				tdd_pb_percentage.value = response.percentage;

				//Set "selected" for the right option in the color select list
				var tdd_pb_color = document.getElementById('tdd_pb_color');
				jQuery('#tdd_pb_color').val(response.color).attr('selected', true);
			});

		} //end set_inline_tdd_pb
	</script>
	<?php
}
add_action( 'admin_footer', 'tdd_pb_quick_edit_js' );

/*
* Binds the tdd_pb_quick_edit_js to the quick edit button
*/
function tdd_pb_quick_edit_button( $actions, $post ) {
	global $current_screen;
	if ( ( $current_screen->id != 'edit-tdd_pb' ) || ( $current_screen->post_type != 'tdd_pb' ) ) return $actions;

	$nonce = wp_create_nonce( "tdd_pb_get_{$post->ID}" );

	$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
	$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ). '" ';
	$actions['inline hide-if-no-js'] .= ' onclick="set_inline_tdd_pb('.$post->ID.', \''.$nonce.'\');">';
	$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
	$actions['inline hide-if-no-js'] .= '</a>';
	return $actions;
}
add_filter( 'post_row_actions', 'tdd_pb_quick_edit_button', 10, 2 );


/*
* View Settings admin page
*/
function tdd_pb_view_settings() {
	$tdd_pb_options = get_option( 'tdd_pb_options' );

?>
<div class="wrap">
	<?php screen_icon( 'plugins' ); ?>
	<h2><?php _e( 'TDD Progress Bars', 'tdd_pb' ); ?></h2>

	<form action="options.php" method="post">
	<?php settings_fields( 'tdd_pb_options' ); ?>
	<?php do_settings_sections(  __FILE__ ); ?>
	<input name="Submit" type="submit" value="<?php _e( 'Save Changes', 'tdd_pb' ); ?>" class="button-primary" />
	</form>
</div>
<?php
}

//Register settings
function tdd_pb_admin_init() {
	register_setting( 'tdd_pb_options', 'tdd_pb_options', 'tdd_pb_options_validate' );

	//register Scripts and Styles section & controls
	add_settings_section( 'tdd_pb_sas', __( 'Scripts and Styles', 'tdd_pb' ), 'tdd_pb_admin_sasheader', __FILE__ );
	add_settings_field( 'animate', __( 'Animate Bars', 'tdd_pb' ), 'tdd_pb_admin_form_animate', __FILE__ , 'tdd_pb_sas' );
	add_settings_field( 'default_css', __( 'Use Default CSS', 'tdd_pb' ), 'tdd_pb_admin_form_default_css', __FILE__, 'tdd_pb_sas' );

	//register Percent section & controls
	add_settings_section( 'tdd_pb_percent', __( 'Percentage Displays', 'tdd_pb' ), 'tdd_pb_admin_percentheader', __FILE__ );
	add_settings_field( 'display_percentage', __( 'Display Text on the Bar', 'tdd_pb' ), 'tdd_pb_admin_form_perecent_display', __FILE__, 'tdd_pb_percent' );
	add_settings_field( 'percentage_color', __( 'Color of Percentage Text', 'tdd_pb' ), 'tdd_pb_admin_form_percentage_color', __FILE__, 'tdd_pb_percent' );
	add_settings_field( 'bar_background_color', __( 'Color of Bar Background', 'tdd_pb' ), 'tdd_pb_admin_form_bar_background_color', __FILE__, 'tdd_pb_percent' );

}
add_action( 'admin_init', 'tdd_pb_admin_init' );

//Scripts & Styles section header
function tdd_pb_admin_sasheader() {
	_e( "<p>The following two boxes allow you to stop including the animation javascript and the default CSS on each page load. It's highly suggested that you don't turn off the Default CSS option unless you have a replacement in mind. The Animate Bars option can be turned off freely if you'd prefer the bars didn't have that cool animation (or you want to save HTTP requests).</p>", 'tdd_pb' );
}

//Animate Checkbox
function tdd_pb_admin_form_animate() {
	$options = get_option( 'tdd_pb_options' );
	$checked = ( $options['animate'] ) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[animate]' id='animate' type='checkbox' ". $checked ."> <br /> ";
	_e( "<small>This script depends on jQuery, so it will ensure that is loaded as well</small>", 'tdd_pb' );
}

//Default CSS
function tdd_pb_admin_form_default_css() {
	$options = get_option( 'tdd_pb_options' );
	$checked = ( $options['default_css'] ) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[default_css]' id='default_css' type='checkbox' ".$checked.">";
}


//Percentage displays section header
function tdd_pb_admin_percentheader() {
	_e( "<p>This is a global control to turn on and off text being displayed on the bar. Individual bars may show the percentage, text (x of y), both, or nothing if this box is checked. Bars will show nothing if this box is unchecked.</p>", 'tdd_pb' );
}


//Percent Display
function tdd_pb_admin_form_perecent_display() {
	$options = get_option( 'tdd_pb_options' );
	$checked = ( $options['display_percentage'] ) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[display_percentage]' id='display_percentage' type='checkbox' ". $checked .">";
}

//percentage_color
function tdd_pb_admin_form_percentage_color() {
	$options = get_option( 'tdd_pb_options' );
	_e( "#<input name='tdd_pb_options[percentage_color]' id='percentage_color' type='text' value='{$options['percentage_color']}' maxlength='6' size='6' />", 'tdd_pb' );
}

function tdd_pb_admin_form_bar_background_color() {
	$options = get_option( 'tdd_pb_options' );
	echo "#<input name='tdd_pb_options[bar_background_color]' id='bar_backround_color' type='text' value='{$options['bar_background_color']}' maxlength='6' size='6' />";
}

//validate
function tdd_pb_options_validate( $input ) {

	//whitelist checkboxes (add them back in, even if false)
	$input['display_percentage'] =  ( isset( $input['display_percentage'] ) ) ? $input['display_percentage'] : false;
	$input['animate'] =  ( isset( $input['animate'] ) ) ? $input['animate'] : false;
	$input['default_css'] =  ( isset( $input['default_css'] ) ) ? $input['default_css'] : false;

	return $input;
}
