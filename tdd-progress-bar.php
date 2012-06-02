<?php
/*
Plugin Name: TDD Progress Bars
Plugin URI: http://github.com/tddewey/tdd-progress
Description: Manage and display progress bars
Version: 0.5.2
Author: Taylor D. Dewey
Author URI: http://websitesthatdontsuck.com
Licence: GPLv3
*/

/*
* set up text domain
*/
function tdd_pb_loadtextdomain(){
	load_plugin_textdomain( 'tdd_pb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'tdd_pb_loadtextdomain' );


//Allows us to filter loading of things based on options
$tdd_pb_options = get_option( 'tdd_pb_options');


/* load some default settings during plugin activation */
register_activation_hook( __FILE__, 'tdd_pb_install' );
function tdd_pb_install() {
	$tdd_pb_options = array(
		'animate' => 1,
		'default_css' => 1,
		'bar_background_color' => '333333',
		'display_percentage' => 1,
		'percentage_color' => 'ececec',
	);

	update_option( 'tdd_pb_options', $tdd_pb_options );
}

/*
* Loads the default CSS stylesheet
*
* Action hook only fires if $default_css = true
*/
function tdd_pb_load_styles() {
	if (WP_DEBUG) {
		wp_enqueue_style( 'tdd_pb_style', plugins_url( 'css/default.css', __FILE__ ), '', '.5.2' );
	} else {
		wp_enqueue_style( 'tdd_pb_style', plugins_url( 'css/default.min.css', __FILE__ ), '', '.5.2' );
	}
}

if ( $tdd_pb_options['default_css'] ){
	add_action( 'init', 'tdd_pb_load_styles', 99 );
	add_action( 'admin_print_styles-'.__FILE__, 'tdd_pb_load_styles' );
}


/*
* Loads the animation javascript
*
* Loads the animation javascript if $animate = true.
* Since it requires jquery, there is a lot of overhead, with little payoff unless jquery is already loaded
* It does look cool though...
* @requires jquery
*/
function tdd_pb_load_js(){
	wp_enqueue_script( 'jquery' );
	if (WP_DEBUG) {
		wp_enqueue_script( 'tdd_pb_js', plugins_url( 'js/animate.js', __FILE__ ), 'jquery', '.3', true );
	} else {
		wp_enqueue_script( 'tdd_pb_js', plugins_url( 'js/animate.min.js', __FILE__ ), 'jquery', '.3', true );
	}
}

if ( $tdd_pb_options['animate'] ){
	add_action( 'init', 'tdd_pb_load_js', 99 );
	add_action( 'admin_print_scripts-'.__FILE__, 'tdd_pb_load_js' );
}

/*
* Registers the tdd_pb custom post type. One "post" for each bar.
* It's not public (we'll give it a small UI wrapper in custom admin)
*
* public set to true for debugging
*/
function tdd_pb_register_post_type(){
	$labels = array(
		'name' => __( 'Progress Bars', 'tdd_pb' ),
		'singular_name' => __( 'Progress Bar', 'tdd_pb' ),
		'add_new' => __( 'Add New Progress Bar', 'tdd_pb' ),
		'add_new_item' => __('Add New Progress Bar', 'tdd_pb' ),
		'edit_item' => __( 'Edit Progress Bar', 'tdd_pb' ),
		'new_item' => __( 'New Progress Bar', 'tdd_pb' ),
		'view_item' => __( 'View Progress Bar', 'tdd_pb' ),
		'search_items' => __( 'Search Progress Bars', 'tdd_pb' ),
		'not_found' => __( 'No Progress Bars Found', 'tdd_pb' ),
		'not_found_in_trash' => __( 'No Progress Bars Found in the Trash', 'tdd_pb' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'supports' => array(
			'title'
			),
		);
	register_post_type( 'tdd_pb', $args );
}
add_action( 'init', 'tdd_pb_register_post_type' );


/*
* Admin pages and related functions
*/
include plugin_dir_path( __FILE__ ). 'inc/admin.php';

/*
* Widget Class
*/
include plugin_dir_path( __FILE__ ). 'inc/widget.php';

/*
* Shortcode
*
* [progress]
* [progress id=3]
* [progress id=3,2,4]
* [progress ids=3,2,4]
* [progress ids=1]
* [progress ids=3,4 width='50px']

* Also any of the above formats using shortcode [tdd_pb]
*/
function tdd_pb_shortcode( $args ){

	$args = shortcode_atts( array(
		'id' => '',
		'ids' => '',
		'width' => 'auto',
		'class' => '',
		'height' => '',
		), $args );


	//explode "id" and "ids" on their commas separately, then merge the arrays together
	$idarr = explode( ',', $args['id'] );
	$idsarr = explode( ',', $args['ids'] );
	$idsarr = array_merge( $idarr, $idsarr );

	//Request some bars
	$return = tdd_pb_get_bars(array(
		'ids' => $idsarr,
		'width' => $args['width'],
		'class' => $args['class'],
		'height' => $args['height']
	));

	//Return them bars
	return $return;
}

add_shortcode('progress', 'tdd_pb_shortcode' );
add_shortcode('tdd_pb', 'tdd_pb_shortcode' );

/**
 * Returns the HTML for a single bar. Function can be used to generate any random bar at any time.
 * Arguments
 * - $percentage => Will directly affect how much of the bar is shown
 * - $text_on_bar => Whatever text to display on the bar in the numbers div.
 * - $title => Title attribute for the bar container
 * - $classes => An array of classes to put in the bar container
 * - $Width => any valid CSS width, defaults to auto (for the bar container)
 * - $height => any valid CSS height, defaults to the setting in Progress Bars->settings
 * - $custom_color => If there's a custom color, will be added as the CSS style background-color
 *
 * @param  arr $args See above for arguments
 * @return [type]       [description]
 */
function tdd_pb_render_bar( $args ){

	$tdd_pb_options = get_option( 'tdd_pb_options');

	$args = extract( wp_parse_args( $args, $defaults = array(
		'percentage' => 0,
		'text_on_bar' => '',
		'title' => '',
		'classes' => array(),
		'width' => 'auto',
		'height' => '',
		'color_class' => 'tdd_pb_red',
		'custom_color' => '',
		) ) );

	$container_classes = 'tdd_pb_bar_container ';
	foreach ( $classes as $class ){
		$container_classes .= sanitize_html_class( $class );
	}

	$container_styles = 'width: ' . esc_attr( $width ) . ';';
	$container_styles .= ' background-color: #' . tdd_pb_sanitize_color_hex_raw( $tdd_pb_options["bar_background_color"] ) . ';';
	if ( $height )
		$container_styles .= ' height: ' . esc_attr( $height ) . ';';

	$bar_classes = 'tdd_pb_bar ';
	$bar_classes .= sanitize_html_class( $color_class );

	$bar_styles = 'width: ' . floatval( $percentage ) . '%;';
	if ( $custom_color )
		$bar_styles .= ' background-color: #' . tdd_pb_sanitize_color_hex_raw( $custom_color ) . ';';


	$return = '<div class="' . $container_classes . '" style="' . $container_styles . '" aria-valuemax="100" aria-valuemin="0" aria-valuenow="' . round( floatval( $percentage ), 2 ) . '" role="progressbar" title="' . esc_attr( $title ) . ': ' . round( floatval( $percentage ), 2 ) . '%">';

	//display percentage option is now a global control to show text on the bar (percentage or xofy). Name is for back compat
	if ($tdd_pb_options['display_percentage']){
		$return .= '<div class="tdd_pb_numbers" style="color: #'. esc_attr( $tdd_pb_options["percentage_color"] ).'">' . esc_html( $text_on_bar ) . '</div>';
	}

	$return .= '<div class="' . $bar_classes . '" style="' . $bar_styles . '"></div>';

	$return .= '</div>'; // end of the container

	return $return;
}


/*
* Returns a progress bar container with 1 or more progress bars
* TODO: Cleanup, this function is a mess.
* TODO: Cache.
*
* @param	array	$ids			An array of the ids of progress bars to fetch.
* @param	str		$width			Width, specified in pixels, percents, em, whatever of the container
* @param	str		$class			Any additional CSS classes to add to the global container (can force a race this way)
* @param	str		$default_color	If the color of a bar isn't specified, this is the fallback.
*/
function tdd_pb_get_bars( $args ){
	$args = wp_parse_args( $args, $defaults = array(
		'ids' => array(),
		'width' => 'auto',
		'height' => '',
		'class' => '',
		'default_color' => 'tdd_pb_red',
		) );

	$tdd_pb_options = get_option( 'tdd_pb_options');

	//Filter the array to ensure we're getting things that look like integers. Will also filter out blank array items
	$idsarr = array_filter( $args['ids'], 'is_numeric' );

	//count if this is a race (more than one progress bar being displayed. The race format can also be forced by passing "tdd_pb_race" in the $class argument)
	if ( count ( $idsarr ) > 1 ){
		$race = 'tdd_pb_race';
		if ( ! $args['height'] ){
			$args['height'] = $tdd_pb_options['race_height'] . 'px';
		}
	} else {
		$race = '';
		if ( ! $args['height'] ){
			$args['height'] = $tdd_pb_options['single_height'] . 'px';
		}
	}


	$race = ( count( $idsarr ) > 1 ) ? 'tdd_pb_race' : '';

	//Set up our global container
	$return = '<div class="tdd_pb_global_container ' . $race . ' '. sanitize_html_class( $args['class'] );
	$return .= '" style="width:' . esc_attr(strip_tags( $args['width'] ) ) . '">';

	//If there are no ids to display, this is kind of a moot proccess - so let's say so:
	if ( count( $idsarr ) <= 0 ){
		$return .= '<p>' . __( 'No progress bars were set, so there is nothing to display', 'tdd_pb' ) . '</p></div>';
		return $return;
	}

	//Setup a new WP_Query for our progress bars.
	$tdd_pb_query = new WP_Query();
	$tdd_pb_query->query(array(
		'post_type' => 'tdd_pb',
		'posts_per_page' => 100,
		'post__in' => $idsarr,
		'no_found_rows' => true,
	));

	//If there were no posts to display, again, this is moot, so let's say so:
	if ( !$tdd_pb_query->have_posts() ) {
		$return .= '<p>'. __( 'No progress bars found', 'tdd_pb' ) . '</p></div>';
		return $return;
	}

	while ( $tdd_pb_query->have_posts() ): $tdd_pb_query->the_post();
		$color              = get_post_meta( get_the_ID(), '_tdd_pb_color', true );
		$custom_color       = get_post_meta( get_the_ID(), '_tdd_pb_custom_color', true );
		$percentage         = get_post_meta( get_the_ID(), '_tdd_pb_percentage', true );
		$start              = get_post_meta( get_the_ID(), '_tdd_pb_start', true );
		$end                = get_post_meta( get_the_ID(), '_tdd_pb_end', true );
		$input_method       = get_post_meta( get_the_ID(), '_tdd_pb_input_method', true );
		$percentage_display = get_post_meta( get_the_ID(), '_tdd_pb_percentage_display', true );
		$xofy_display       = get_post_meta( get_the_ID(), '_tdd_pb_xofy_display', true );

		//Get the calculated percentage
		if ( $input_method == 'xofy' && $end > 0 ){
			$start = floatval( $start );
			$end = floatval( $end );
			$calcpercentage = round( $start/$end*100, 2 );
		} else {
			$calcpercentage = $percentage;
		}

		//This filter allows you to hook in and modify the percentage, perhaps based on a fancy API call...
		$calcpercentage = apply_filters( 'tdd_pb_calculated_percentage', $calcpercentage, get_the_ID() );

		//Fallback to default bar color
		$color_class = ( $color ) ? sanitize_html_class( 'tdd_pb_' . $color ) : $args['default_color'];

		//Are we displaying text on the bar? Potentially... (there's a global setting that can override this)
		$text_on_bar = '';
		if ( $percentage_display == 'on' || $percentage_display === '' )
			$text_on_bar .= round( floatval( $calcpercentage ), 2 ) .'%';

		if ( $xofy_display == 'on' )
			$text_on_bar .='&nbsp;&nbsp;' .intval( $start ) . ' ' . __( 'of', 'tdd_pb' ) . ' ' . intval( $end );

		$barargs = array(
			'percentage' => $calcpercentage,
			'text_on_bar' => $text_on_bar,
			'title' => get_the_title(),
			'classes' => array(),
			'width' => esc_attr( $args['width'] ),
			'height' => esc_attr( $args['height'] ),
			'color_class' => $color_class,
			'custom_color' => tdd_pb_sanitize_color_hex_raw( $custom_color )
			);

		$return .= tdd_pb_render_bar( $barargs );

	endwhile;

	//Close the progress bar container, and return everything to screen.
	$return .= '</div>';
	return $return;
}

/**
 * Function to sanitize a hex color string. Taken from this ticket, so it may be in core eventually:
 * http://core.trac.wordpress.org/attachment/ticket/19100/19100.2.diff
 * @param  str $color suspected hex color string
 * @return str        sanitized color hex string
 */
function tdd_pb_sanitize_color_hex_raw( $color ) {
	$default = '';
	$color = trim( strtolower( urldecode( $color ) ), "# \t\n\r\0\x0B" );

	if ( ! in_array( strlen( $color ), array( 3, 6 ) ) )
	        return $default;

	if ( ! preg_match( "/(([a-f0-9]){3}){1,2}$/i", $color ) )
	    return $default;

	return $color;
}