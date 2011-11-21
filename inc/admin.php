<?php
/*
* Setup custom meta boxes for the tdd_bp custom post type page
*/
function tdd_pb_metabox_create() {
	add_meta_box( 'tdd_pb_meta', __( 'Progress Bar Options' ), 'tdd_pb_metabox_display', 'tdd_pb' );
}
add_action( 'add_meta_boxes', 'tdd_pb_metabox_create' );

/*
* Post type meta box display
*/
function tdd_pb_metabox_display($post) {
	$tdd_pb_color = get_post_meta( $post->ID, '_tdd_pb_color', true );
	$tdd_pb_percentage = get_post_meta( $post->ID, '_tdd_pb_percentage', true );

?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="td_pb_color">Bar Color</label></th>
			<td><select name="tdd_pb_color">
				<option value="strawberry" <?php selected( $tdd_pb_color, 'strawberry' ); ?>><?php _e( 'Strawberry', 'tdd_pb' ); ?></option>
				<option value="fuchsia" <?php selected( $tdd_pb_color, 'fuchsia' ); ?>><?php _e( 'Fuchsia', 'tdd_pb' ); ?></option>
				<option value="purple" <?php selected( $tdd_pb_color, 'purple' ); ?>><?php _e( 'Purple', 'tdd_pb' ); ?></option>
				<option value="blue" <?php selected( $tdd_pb_color, 'blue' ); ?>><?php _e( 'Blue', 'tdd_pb' ); ?></option>
				<option value="lightblue" <?php selected( $tdd_pb_color, 'lightblue' ); ?>><?php _e( 'Light Blue', 'tdd_pb' ); ?></option>
				<option value="teal" <?php selected( $tdd_pb_color, 'teal' ); ?>><?php _e( 'Teal', 'tdd_pb' ); ?></option>
				<option value="green" <?php selected( $tdd_pb_color, 'green' ); ?>><?php _e( 'Green', 'tdd_pb' ); ?></option>
				<option value="yellow" <?php selected( $tdd_pb_color, 'yellow' ); ?>><?php _e( 'Yellow', 'tdd_pb' ); ?></option>
				<option value="orange" <?php selected( $tdd_pb_color, 'orange' ); ?>><?php _e( 'Orange', 'tdd_pb' ); ?></option>
				<option value="red" <?php selected( $tdd_pb_color, 'red' ); ?>><?php _e( 'Red', 'tdd_pb' ); ?></option>
				</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="tdd_pb_percentage">Percent Complete</label></th>
			<td><input name="tdd_pb_percentage" type="text" maxlength="3" size="3" value="<?php echo esc_attr( $tdd_pb_percentage ); ?>">%</td>
		</tr>
	</table>
	<?php $id = get_the_ID(); ?>
	<p><?php _e( "Example shortcode: <code>[progress id={$id}]</code>", 'tdd_pb'); ?> </p>
<?php
}

/*
* Saves the meta box info for the post
*/
function tdd_pb_metabox_save( $post_id ){
	if ( isset( $_POST['tdd_pb_color'] ) ){
		update_post_meta( $post_id, '_tdd_pb_color', strip_tags( $_POST['tdd_pb_color'] ) );
	}
	
	if ( isset( $_POST['tdd_pb_percentage'] ) ){
		//remove any percent signs ppl decided to put in
		$percentage =  trim( $_POST['tdd_pb_percentage'], " %" );
		update_post_meta( $post_id, '_tdd_pb_percentage', strip_tags( $percentage ) );
	}
	
}
add_action( 'save_post', 'tdd_pb_metabox_save' );

/*
* Adds custom columns to the Progress Bars view
*/
function set_edit_tdd_pb_columns($columns) {
    unset($columns['date']);
    return array_merge($columns, 
              array('percentage' => 'Percentage',
					'shortcode' => 'Shortcode',
                    'date' => __('Date'),
                    ));
}
add_filter('manage_edit-tdd_pb_columns' , 'set_edit_tdd_pb_columns');


/*
* Put content in those custom columns
*/
function tdd_pb_custom_columns( $column ){
	global $post;
	
	switch ( $column ){
		case 'percentage':
			printf( __( '%1$s%% complete', 'tdd_pb'), get_post_meta( $post->ID, '_tdd_pb_percentage', true ) );
			break;
		case 'shortcode':
			echo '<code>[progress id='. $post->ID .']</code>';
			break;
	}
}
add_action( 'manage_posts_custom_column' , 'tdd_pb_custom_columns' );


/*
* View Settings admin page
*/
function tdd_pb_view_settings(){
$tdd_pb_options = get_option( 'tdd_pb_options');

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
	add_settings_field( 'default_css', __( 'Use Default CSS', 'tdd_pb' ), 'tdd_pb_admin_form_default_css', __FILE__, 'tdd_pb_sas');

	//register Percent section & controls
	add_settings_section( 'tdd_pb_percent', __( 'Percentage Displays', 'tdd_pb' ), 'tdd_pb_admin_percentheader', __FILE__ );
	add_settings_field( 'display_percentage', __( 'Display Percent Complete', 'tdd_pb' ), 'tdd_pb_admin_form_perecent_display', __FILE__, 'tdd_pb_percent' );
	add_settings_field( 'percentage_color', __( 'Color of Percentage Text', 'tdd_pb' ), 'tdd_pb_admin_form_percentage_color', __FILE__, 'tdd_pb_percent' );
	add_settings_field( 'bar_background_color', __( 'Color of Bar Background', 'tdd_pb' ), 'tdd_pb_admin_form_bar_background_color', __FILE__, 'tdd_pb_percent' );

}
add_action( 'admin_init', 'tdd_pb_admin_init' );

//Scripts & Styles section header
function tdd_pb_admin_sasheader(){
	_e( "<p>The following two boxes allow you to stop including the animation javascript and the default CSS on each page load. It's highly suggested that you don't turn off the Default CSS option unless you have a replacement in mind. The Animate Bars option can be turned off freely if you'd prefer the bars didn't have that cool animation (or you want to save HTTP requests).</p>", 'tdd_pb' );
}

//Animate Checkbox
function tdd_pb_admin_form_animate(){
	$options = get_option('tdd_pb_options');
	$checked = ($options['animate']) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[animate]' id='animate' type='checkbox' ". $checked ."> <br /> ";
	_e( "<small>This script depends on jQuery, so it will ensure that is loaded as well</small>", 'tdd_pb' );
}

//Default CSS
function tdd_pb_admin_form_default_css(){
	$options = get_option('tdd_pb_options');
	$checked = ($options['default_css']) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[default_css]' id='default_css' type='checkbox' ".$checked.">";
}


//Percentage displays section header
function tdd_pb_admin_percentheader(){
	_e( "<p>By default, the percentage progress is displayed in the bar. This allows you to optionally turn that off and/or set the color of the text</p>", 'tdd_pb' );
}


//Percent Display
function tdd_pb_admin_form_perecent_display(){
	$options = get_option('tdd_pb_options');
	$checked = ($options['display_percentage']) ? ' checked="checked" ' : '';
	echo "<input name='tdd_pb_options[display_percentage]' id='display_percentage' type='checkbox' ". $checked .">";
}

//percentage_color
function tdd_pb_admin_form_percentage_color(){
	$options = get_option('tdd_pb_options');
	_e( "#<input name='tdd_pb_options[percentage_color]' id='percentage_color' type='text' value='{$options['percentage_color']}' maxlength='6' size='6' />", 'tdd_pb' );
}

function tdd_pb_admin_form_bar_background_color(){
	$options = get_option('tdd_pb_options');
	echo "#<input name='tdd_pb_options[bar_background_color]' id='bar_backround_color' type='text' value='{$options['bar_background_color']}' maxlength='6' size='6' />";
}

//validate
function tdd_pb_options_validate( $input ){
	
		//whitelist checkboxes (add them back in, even if false)
		$input['display_percentage'] =  ( isset( $input['display_percentage'] ) ) ? $input['display_percentage'] : false;
		$input['animate'] =  ( isset( $input['animate'] ) ) ? $input['animate'] : false;
		$input['default_css'] =  ( isset( $input['default_css'] ) ) ? $input['default_css'] : false;
	
	return $input;
}