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
 * Load the miniColors color picker scripts
 */
function enqueue_mini_colors( $hook ) {
	if ( 'post.php' == $hook || 'tdd_pb_page_settings' == $hook ){
		wp_enqueue_script( 'minicolors', plugins_url( 'js/miniColors/jquery.miniColors.js', dirname( __FILE__ ) ), array( 'jquery' ), '1', true );
		wp_enqueue_style( 'minicolors', plugins_url( 'js/miniColors/jquery.miniColors.css', dirname( __FILE__ ) ), false, '1' );
	}
}
add_action( 'admin_enqueue_scripts', 'enqueue_mini_colors' );

/**
 * Instantiate the minicolors scripts
 */
function instantiate_mini_colors(){
	echo '<script>jQuery(".color").focus(function(){ jQuery(this).miniColors(); });</script>';
}
add_action( 'admin_footer-tdd_pb_page_settings', 'instantiate_mini_colors' );
add_action( 'admin_footer-post.php', 'instantiate_mini_colors' );


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

	if ( $input_method = get_post_meta( $post->ID, '_tdd_pb_input_method', true ) ){
		$whitelist = array( 'percentage', 'xofy' );
		if ( in_array( $input_method, $whitelist ) )
			$tdd_pb_input_method = $input_method;
		else
			$tdd_pb_input_method = 'percentage';
	} else {
			$tdd_pb_input_method = 'percentage';
	}

	$tdd_pb_percentage = floatval( get_post_meta( $post->ID, '_tdd_pb_percentage', true ) );
	$tdd_pb_custom_color = tdd_pb_sanitize_color_hex_raw( get_post_meta( $post->ID, '_tdd_pb_custom_color', true ) );
	$tdd_pb_start = floatval( get_post_meta( $post->ID, '_tdd_pb_start', true ) );
	$tdd_pb_end = floatval( get_post_meta( $post->ID, '_tdd_pb_end', true ) );

	if ( $percentage_display = get_post_meta( $post->ID, '_tdd_pb_percentage_display', true ) ){
		$whitelist = array( 'on', 'off' );
		if ( in_array( $percentage_display, $whitelist ) )
			$tdd_pb_percentage_display = $percentage_display;
		else
			$tdd_pb_percentage_display = 'on';
	} else {
		$tdd_pb_percentage_display = 'on';
	}

	if ( $xofy_display = get_post_meta( $post->ID, '_tdd_pb_xofy_display', true ) ){
		$whitelist = array( 'on', 'off' );
		if ( in_array( $xofy_display, $whitelist ) )
			$tdd_pb_xofy_display = $xofy_display;
		else
			$tdd_pb_xofy_display = 'off';
	} else {
		$tdd_pb_xofy_display = 'off';
	}

?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="tdd_pb_color"><?php _e( 'Bar Color', 'tdd_pb' ); ?></label></th>
			<td><select name="tdd_pb_color">
				<?php global $colors; ?>
				<option>Select a color</option>
				<?php foreach ( $colors as $color=>$label ): ?>
					<option value="<?php echo $color; ?>" <?php selected( $tdd_pb_color, $color ); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
				</select>

				<label for="tdd_pb_custom_color">or custom: </label>
				<input type="text" class="color" name="tdd_pb_custom_color" id="tdd_pb_custom_color" size="6" value="<?php echo $tdd_pb_custom_color; ?>">
			</td>
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
								<input name="tdd_pb_percentage" type="text" size="2" value="<?php echo round( floatval( $tdd_pb_percentage ), 2 ); ?>"> %							</td>
						</tr>
						<tr valign="top">
							<td>
								<label><input name="tdd_pb_input_method" type="radio" <?php checked( $tdd_pb_input_method, 'xofy' ); ?> value="xofy" > <?php _e( 'Calculate Percentage (x of y)', 'tdd_pb' ); ?></label>
							</td>
							<td>
								<input name="tdd_pb_start" type="text" size="10" value="<?php echo round( floatval( $tdd_pb_start ), 2 ); ?>"> <?php _e( 'of', 'tdd_pb' ); ?>
								<input name="tdd_pb_end" type="text" size="10" value="<?php echo round( floatval( $tdd_pb_end ), 2 ); ?>"><br />
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
			'height' => '25px'
		) );

?>

<?php
}

/*
* Saves the meta box info for the post
*/
function tdd_pb_metabox_save( $post_id ) {

	if ( isset( $_POST['tdd_pb_color'] ) )
		update_post_meta( $post_id, '_tdd_pb_color', sanitize_html_class( $_POST['tdd_pb_color'] ) );

	if ( isset( $_POST['tdd_pb_custom_color'] ) )
		update_post_meta( $post_id, '_tdd_pb_custom_color', tdd_pb_sanitize_color_hex_raw( $_POST['tdd_pb_custom_color'] ) );
	else
		delete_post_meta( $post_id, '_tdd_pb_custom_color' );

	if ( isset( $_POST['tdd_pb_percentage'] ) ) {
		update_post_meta( $post_id, '_tdd_pb_percentage', abs( floatval( $_POST['tdd_pb_percentage'] ) ) );
	}

	if ( isset( $_POST['tdd_pb_start'] ) ) {
		update_post_meta( $post_id, '_tdd_pb_start', floatval( $_POST['tdd_pb_start'] ) );
	}

	if ( isset( $_POST['tdd_pb_end'] ) ) {
		update_post_meta( $post_id, '_tdd_pb_end', floatval( $_POST['tdd_pb_end'] ) );
	}

	if ( isset( $_POST['tdd_pb_input_method'] ) ){
		switch ( $_POST['tdd_pb_input_method'] ){
			case 'xofy':
				update_post_meta( $post_id, '_tdd_pb_input_method', 'xofy' );
				break;
			default:
				update_post_meta( $post_id, '_tdd_pb_input_method',  'percentage' );
		}
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
				'height' => '25px'
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
					<option>Select a color</option>
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

/**
 * Class sets up and handles the global settings page for the progress bar. Settings sub-panel is attached to the custom post type menu.
 */
class TDD_PB_Admin_Settings {

	function __construct(){
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add the sub-menu to the Progress Bars custom post type.
	 */
	function add_submenu(){
		add_submenu_page( 'edit.php?post_type=tdd_pb', 'TDD Progress Bars - Settings', 'Settings', 'manage_options', 'settings', array( $this, 'render_settings_page' ) );
	}

	/**
	 * Renders the settings page. Fired via callback in $this->add_submenu
	 */
	function render_settings_page(){
		?>
		<div class="wrap">
			<?php screen_icon( 'plugins' ); ?>
			<h2><?php _e( 'TDD Progress Bars', 'tdd_pb' ); ?></h2>

			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ): ?>
				<div class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
			<?php endif; ?>

			<form action="options.php" method="post">
				<?php settings_fields( 'tdd_pb_options' ); ?>
				<?php do_settings_sections(  __FILE__ ); ?>
				<input name="Submit" type="submit" value="<?php _e( 'Save Changes', 'tdd_pb' ); ?>" class="button-primary" />
			</form>
		</div>
		<?php
	}

	/**
	 * Hooked in the construct() to admin_init
	 */
	function register_settings(){
		register_setting( 'tdd_pb_options', 'tdd_pb_options', array( $this, 'validate' ) );

		//register Scripts and Styles section & controls
		add_settings_section( 'tdd_pb_sas', __( 'Scripts and Styles', 'tdd_pb' ), array( $this, 'sasheader' ), __FILE__ );
		add_settings_field( 'animate', __( 'Animate Bars', 'tdd_pb' ), array( $this, 'setting_animate' ), __FILE__ , 'tdd_pb_sas' );
		add_settings_field( 'default_css', __( 'Use Default CSS', 'tdd_pb' ), array( $this, 'setting_css' ), __FILE__, 'tdd_pb_sas' );
		add_settings_field( 'bar_background_color', __( 'Color of Bar Background', 'tdd_pb' ), array( $this, 'setting_bar_background_color' ), __FILE__, 'tdd_pb_sas' );
		add_settings_field( 'single_height', __( 'Default height for a single progress bar' ), array( $this, 'setting_default_single_height' ), __FILE__, 'tdd_pb_sas' );
		add_settings_field( 'race_height', __( 'Default height when multiple bars are shown together, sometimes called "racing"' ), array( $this, 'setting_default_race_height' ), __FILE__, 'tdd_pb_sas' );

		//register Percent section & controls
		add_settings_section( 'tdd_pb_percent', __( 'Text Overlay Displays', 'tdd_pb' ), array( $this, 'percentheader' ), __FILE__ );
		add_settings_field( 'display_percentage', __( 'Display Text on the Bar', 'tdd_pb' ), array( $this, 'setting_perecent_display' ), __FILE__, 'tdd_pb_percent' );
		add_settings_field( 'percentage_color', __( 'Color of Overlay Text', 'tdd_pb' ), array( $this, 'setting_percentage_color' ), __FILE__, 'tdd_pb_percent' );

	}

	function get_options( $options = '' ){
		if ( !$options )
			$options = get_option('tdd_pb_options');
		$mergedoptions = wp_parse_args( $options, array(
			'animate' => true,
			'default_css' => true,
			'bar_background_color' => '333333',
			'single_height' => '50',
			'race_height' => '25',
			'display_percentage' => true,
			'percentage_color' => 'ececec',
			) );

		return $mergedoptions;
	}

	function sasheader() {
		_e( "<p>The following two boxes allow you to stop including the animation javascript and the default CSS on each page load. It's highly suggested that you don't turn off the Default CSS option unless you have a replacement in mind. The Animate Bars option can be turned off freely if you'd prefer the bars didn't have that cool animation (or you'd rather not load the required javascript)</p>", 'tdd_pb' );
	}

	//Animate Checkbox
	function setting_animate() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[animate]" id="animate" type="checkbox" <?php checked( $options['animate'] ); ?>> <br />
		<?php _e( "<small>This script depends on jQuery, so it will ensure that is loaded as well</small>", 'tdd_pb' ); ?>
		<?
	}

	//Default CSS Checkbox
	function setting_css() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[default_css]" id="default_css" type="checkbox" <?php checked( $options['default_css'] ); ?>>
		<?php
	}

	function setting_bar_background_color() {
		//TODO: For all these color options, implement a color picker.
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[bar_background_color]" id="bar_backround_color" type="text" value="<?php echo tdd_pb_sanitize_color_hex_raw( $options['bar_background_color'] ); ?>" size="6" class="color" />
		<?php
	}

	function setting_default_single_height() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[single_height]" id="single_height" type="text" value="<?php echo absint( $options['single_height'] ); ?>" size="3" />px
		<?php
	}

	function setting_default_race_height() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[race_height]" id="race_height" type="text" value="<?php echo absint( $options['race_height'] ); ?>" size="3" />px
		<?php
	}

	//Percentage displays section header
	function percentheader() {
		_e( "<p>This is a global control to turn on and off text being displayed on the bar. Individual bars may optionally show the percentage, text (x of y), or both, however nothing will show if this box is unchecked.</p>", 'tdd_pb' );
	}

	//Percent Display Checkbox
	function setting_perecent_display() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[display_percentage]" id="display_percentage" type="checkbox" <?php checked( $options['display_percentage'] ) ?>>
		<?php
	}

	//percentage_color
	function setting_percentage_color() {
		$options = $this->get_options();
		?>
		<input name="tdd_pb_options[percentage_color]" id="percentage_color" class="color" type="text" value="<?php echo tdd_pb_sanitize_color_hex_raw( $options['percentage_color'] ); ?>" size="6" />
		<?php
	}

	//validate
	function validate( $input ) {
		//whitelist checkboxes (add them back in, even if false)
		$output['display_percentage'] =  isset( $input['display_percentage'] ) ? true : false;
		$output['animate'] =  isset( $input['animate'] ) ? true : false;
		$output['default_css'] =  isset( $input['default_css'] ) ? true : false;

		//Sanitize other options
		if ( ! empty( $input['bar_background_color'] ) )
			$output['bar_background_color'] = tdd_pb_sanitize_color_hex_raw( $input['bar_background_color'] );
		else

			unset( $output['bar_background_color'] );

		if ( ! empty( $input['single_height'] ) )
			$output['single_height'] = absint( $input['single_height'] );
		else
			unset( $output['single_height'] );

		if ( ! empty( $input['race_height'] ) )
			$output['race_height'] = absint( $input['race_height'] );
		else
			unset( $output['race_height'] );

		if ( ! empty( $input['percentage_color'] ) )
			$output['percentage_color'] = tdd_pb_sanitize_color_hex_raw( $input['percentage_color'] );
		else
			unset( $output['percentage_color'] );

		//We now need to set all the array indexes that are blank to their default values.
		$mergedoutput = $this->get_options( $output );
		return $mergedoutput;
	}

}
new TDD_PB_Admin_Settings;