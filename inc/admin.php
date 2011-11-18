<?php
/*
* Setup custom meta boxes for the tdd_bp custom post type page
*/
function tdd_pb_metabox_create() {
	add_meta_box( 'tdd_pb_meta', 'Progress Bar Options', 'tdd_pb_metabox_display', 'tdd_pb' );
}
add_action( 'add_meta_boxes', 'tdd_pb_metabox_create' );

function tdd_pb_metabox_display($post) {
/*
		bar specific options:
			- color/graphic
			- percentage (or API call)
*/
	$tdd_pb_color = get_post_meta( $post->ID, '_tdd_pb_color', true );
	$tdd_pb_percentage = get_post_meta( $post->ID, '_tdd_pb_percentage', true );

?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="td_pb_color">Bar Color</label></th>
			<td><select name="tdd_pb_color">
				<option value="strawberry" <?php selected( $tdd_pb_color, 'strawberry' ); ?>>Strawberry</option>
				<option value="fuchsia" <?php selected( $tdd_pb_color, 'fuchsia' ); ?>>Fuchsia</option>
				<option value="purple" <?php selected( $tdd_pb_color, 'purple' ); ?>>Purple</option>
				<option value="blue" <?php selected( $tdd_pb_color, 'blue' ); ?>>Blue</option>
				<option value="lightblue" <?php selected( $tdd_pb_color, 'lightblue' ); ?>>Light Blue</option>
				<option value="teal" <?php selected( $tdd_pb_color, 'teal' ); ?>>Teal</option>
				<option value="green" <?php selected( $tdd_pb_color, 'green' ); ?>>Green</option>
				<option value="yellow" <?php selected( $tdd_pb_color, 'yellow' ); ?>>Yellow</option>
				<option value="orange" <?php selected( $tdd_pb_color, 'orange' ); ?>>Orange</option>
				<option value="red" <?php selected( $tdd_pb_color, 'red' ); ?>>Red</option>
				</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="tdd_pb_percentage">Percent Complete</label></th>
			<td><input name="tdd_pb_percentage" type="text" maxlength="3" size="3" value="<?php echo esc_attr( $tdd_pb_percentage ); ?>">%</td>
		</tr>
	</table>

	<p>Example shortcode: <code>[progress id=<?php the_ID(); ?>]</code> </p>
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
                    'color' => 'Color' ,
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
		case 'color':
			echo get_post_meta( $post->ID, '_tdd_pb_color', true );
			break;
		case 'percentage':
			echo get_post_meta( $post->ID, '_tdd_pb_percentage', true ) . '% complete';
			break;
		case 'shortcode':
			echo '<code>[progress id='. $post->ID .']';
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
	<h2>TDD Progress Bars</h2>

	<form method="post" action="">
	<h3>Scripts & Styles</h3>
	<p>The following two boxes allow you to stop including the animation javascript and the default CSS on each page load. It's highly suggested that you don't turn off the Default CSS option unless you have a replacement in mind. The Animate Bars option can be turned off freely if you'd prefer the bars didn't have that cool animation (or you want to save HTTP requests).</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="animate">Animate Bars</label></th>
			<td><input name="animate" type="checkbox" <?php checked( $tdd_pb_options['animate'], true ); ?> > <br />
			<small>This script depends on jQuery, so it will ensure that is loaded as well</small>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="default_css">Use Default CSS</label></th>
			<td><input name="default_css" type="checkbox" <?php checked( $tdd_pb_options['default_css'], true ); ?> ></td>
		</tr>
	</table>
	<h3>Percentage Displays</h3>
	<p>By default, the percentage progress is displayed in the bar. This allows you to optionally turn that off and/or set the color of the text</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="display_percentage">Display Percentage</label></th>
			<td><input name="display_percentage" type="checkbox" <?php checked( $tdd_pb_options['display_percentage'], true ); ?> ></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="percentage_color">Color of Percentage Text</label></th>
			<td>#<input name="percentage_color" type="text" maxlength="6" size="6" value="<?php echo esc_attr( $tdd_pb_options['percentage_color'] ); ?>"></td>
		</tr>
	</table>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="bar_background_color">Bar Background Color</label></th>
			<td>#<input name="bar_background_color" type="text" maxlength="6" size="6" value="<?php echo esc_attr( $tdd_pb_options['bar_background_color'] ); ?>" ></td>
		</tr>

		<tr valign="top">
			<td>
			<input type="submit" name="save" value="Save Options" class="button-primary" />
			<input type="submit" name="reset" value="Reset" class="button-secondary" />
			
	</table>

</div>
<?php
}