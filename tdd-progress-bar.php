<?php
/*
Plugin Name: TDD Progress Bars
Plugin URI: http://github.com/tddewey/tdd-progress
Description: Manage and display progress bars
Version: 0.4
Author: Taylor D. Dewey
Author URI: http://websitesthatdontsuck.com
Licence: GPLv3
*/

/*
		global options:
			- animate (on/off) - default=on
			- use default CSS (on/off) - default=on
			- bar background-color - default=#333;
			- display percentage - default=true
			- percentage color - default=#ececec;

		shortcode options:
			- race height (if multiple ID's involved)
			- width

		bar specific options:
			- color/graphic
			- percentage (or API call)


		@todo:
			make a variety of the colored bars. Assemble into a sprite and work-in above. Only need the width to be long enough to feel random. Blend the seam.

			Global options page

			Page to add progress bars.
				- Name
				- Bar style
				- Perecentage / or API call.

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
		wp_enqueue_style( 'tdd_pb_style', plugins_url( 'css/default.css', __FILE__ ), '', '.3' );
	} else {
		wp_enqueue_style( 'tdd_pb_style', plugins_url( 'css/default.min.css', __FILE__ ), '', '.3' );
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
* Set up admin menus
*/
function tdd_pb_admin_menu(){
	add_submenu_page( 'edit.php?post_type=tdd_pb', 'TDD Progress Bars - Settings', 'Settings', 'manage_options',  __FILE__, 'tdd_pb_view_settings' );
}
add_action( 'admin_menu', 'tdd_pb_admin_menu' );

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
		), $args );


	//explode "id" and "ids" on their commas separately, then merge the arrays together
	$idarr = explode( ',', $args['id'] );
	$idsarr = explode( ',', $args['ids'] );
	$idsarr = array_merge($idarr, $idsarr);

	//Request some bars
	$return = tdd_pb_get_bars(array(
		'ids' => $idsarr,
		'width' => $args['width'],
		'class' => $args['class'],
	));

	//Return them bars
	return $return;
}

add_shortcode('progress', 'tdd_pb_shortcode' );
add_shortcode('tdd_pb', 'tdd_pb_shortcode' );


/*
* Returns a progress bar container with 1 or more progress bars
*
* @param	array	$ids			An array of the ids of progress bars to fetch.
* @param	str		$width			Width, specified in pixels, percents, em, whatever of the container
* @param	str		$class			Any additional CSS classes to add to the global container (can force a race this way)
* @param	str		$default_color	If the color of a bar isn't specified, this is the fallback.
*/
function tdd_pb_get_bars( $args ){
	$defaults = array(
		'ids' => array(),
		'width' => 'auto',
		'class' => '',
		'default_color' => 'tdd_pb_red',
		);

	$tdd_pb_options = get_option( 'tdd_pb_options');

	//parse incoming arguments against default.
	$args = wp_parse_args( $args, $defaults );

	//Filter the array to ensure we're getting things that look like integers. Will also filter out blank array items (i.e. '' )
	$idsarr = array_filter( $args['ids'], 'is_numeric' );

	//count if this is a race (more than one progress bar being displayed. The race format can also be forced by passing "race" in the $class argument)
	$race = ( count( $idsarr ) > 1 ) ? 'tdd_pb_race' : '';

	//Set up our global container
	$return = '<div class="tdd_pb_global_container '.$race.' '.$args['class'].'" style="width:'.strip_tags( $args['width'] ).'">';

	//If there are no ids to display, this is kind of a moot proccess - so let's say so:
	if ( count( $idsarr ) <= 0 ){
		$return .= '<p>' . __( 'No progress bars were set, so there is nothing to display', 'tdd_pb' ). '</p></div>';
		return $return;
	}

	//Setup a new WP_Query for our progress bars.
	$tdd_pb_query = new WP_Query();
	$tdd_pb_query->query(array(
		'post_type' => 'tdd_pb',
		'posts_per_page' => -1,
		'post__in' => $idsarr,
		'no_found_rows' => true,
//		'meta_key' => '_tdd_pb_percentage' //Used to be this was the only, and required key. Is no longer the case...
	));

	//If there were no posts to display, again, this is moot, so let's say so:
	if ( !$tdd_pb_query->have_posts() ) {
		$return .= '<p>'. __( 'No progress bars found', 'tdd_pb' ) . '</p></div>';
		return $return;
		}

	while ( $tdd_pb_query->have_posts() ): $tdd_pb_query->the_post();
		$percentage = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_percentage', true ) );
		$start = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_start', true ) );
		$end = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_end', true ) );
		$input_method = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_input_method', true ) );
		$percentage_display = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_percentage_display', true ) );
		$xofy_display = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_xofy_display', true ) );

		if ( $input_method == 'xofy' ){
			$calcpercentage = round( ($start/$end)*100, 2 );
		} else {
			$calcpercentage = $percentage;
		}

		//This filter allows you to hook in and modify the percentage, perhaps based on a fancy API call...
		$calcpercentage = apply_filters( 'tdd_pb_calculated_percentage', $calcpercentage, get_the_ID() );

		$color = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_color', true ) );
		//if no color, define a default
		$color = (!$color) ? $args['default_color'] : 'tdd_pb_'.$color;
		$return .= '<div title="'.get_the_title() .': '.$calcpercentage.'%" class="tdd_pb_bar_container" style="background-color: #'. $tdd_pb_options["bar_background_color"] .'" role="progressbar" aria-valuenow="'.$calcpercentage.'" aria-valuemax="100" aria-valuemin="0">';
		if ($tdd_pb_options['display_percentage']):
			$return .= '<div class="tdd_pb_numbers" style="color: #'.$tdd_pb_options["percentage_color"].'">';

			if ( $percentage_display == 'on' || $percentage_display === '' ){
				$return .= $calcpercentage .'%';
			}

			if ( $xofy_display == 'on' ){
				$return .='&nbsp;&nbsp;' . $start . ' ' . __( 'of', 'tdd_pb' ) . ' ' . $end;
			}

			$return .='</div>';
		endif;
		$return .= '<div class="tdd_pb_bar '. $color .'" style="width:'. $calcpercentage .'%"></div></div>';

	endwhile;

	//Close the progress bar container, and return everything to screen.
	$return .= '</div>';
	return $return;
}