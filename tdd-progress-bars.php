<?php
/*
Plugin Name: TDD Progress Bars
Plugin URI: http://github.com/tddewey/tdd-progress
Description: Manage and display progress bars
Version 0.1.1
Author: Taylor D. Dewey
Author URI: http://websitesthatdontsuck.com
Licence: GPLv3
*/

/*
		global options:
			- animate (on/off) - default=on
			- use default CSS (on/off) - default=on
			- bar background-color - default=#333;
			- container background-color - default=#ccc;
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
	wp_enqueue_style( 'tdd_pb_style', plugins_url( 'css/default.css', __FILE__ ), '', '.1' );
}

if ( $tdd_pb_options['default_css'] ){
	add_action( 'init', 'tdd_pb_load_styles', 99 );
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
	wp_enqueue_script( 'tdd_pb_js', plugins_url( 'js/animate.js', __FILE__ ), 'jquery', '.1', true );
}

if ( $tdd_pb_options['animate'] ){
	add_action( 'init', 'tdd_pb_load_js', 99 );
}

/*
* Registers the tdd_pb custom post type. One "post" for each bar.
* It's not public (we'll give it a small UI wrapper in custom admin)
*
* public set to true for debugging
*/
function tdd_pb_register_post_type(){
	$labels = array(
		'name' => 'Progress Bars',
		'singular_name' => 'Progress Bar',
		'add_new' => 'Add New Progress Bar',
		'add_new_item' => 'Add New Progress Bar',
		'edit_item' => 'Edit Progress Bar',
		'new_item' => 'New Progress Bar',
		'view_item' => 'View Progress Bar',
		'search_items' => 'Search Progress Bars',
		'not_found' => 'No Progress Bars Found',
		'not_found_in_trash' => 'No Progress Bars Found in the Trash',
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
	add_submenu_page( 'edit.php?post_type=tdd_pb', 'TDD Progress Bars - Settings', 'Settings', 'manage_options', 'tdd-pb-admin-settings-menu', 'tdd_pb_view_settings' );
}
add_action( 'admin_menu', 'tdd_pb_admin_menu' );

/*
* Admin pages and related functions
*/
include plugin_dir_path( __FILE__ ). 'inc/admin.php';
	
/*
* Shortcode
*
* [progress]
* [progress id=3]
* [progress id=3,2,4]
* [progress ids=3,2,4]
* [progress ids=1]
* [progress ids=3,4 width='50px']
*/

function tdd_pb_shortcode($args){
	
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
	
	//Filter the array to ensure we're getting things that look like integers.
	$idsarr = array_filter( $idsarr, 'is_numeric' );
	
	//count if this is a race (more than one progress bar being displayed)
	$race = ( count( $idsarr ) > 1 ) ? 'race' : '';
	//Load up the $return var with our progress bar container
	$return = '<div class="tdd_pb_global_container '. $race .' '.$args["class"].'" style="width:'.strip_tags($args["width"]).'">';
	
	//Setup a new WP_Query for our progress bars.
	$tdd_pb_query = new WP_Query();
	$tdd_pb_query->query(array(
		'post_type' => 'tdd_pb',
		'posts_per_page' => -1,
		'post__in' => $idsarr,
		'meta_key' => '_tdd_pb_percentage'
	));
	
	while ( $tdd_pb_query->have_posts() ): $tdd_pb_query->the_post();
		$percentage = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_percentage', true ) );
		$color = strip_tags( get_post_meta( get_the_ID(), '_tdd_pb_color', true ) );
		//if no color, define a default
		$color = (!$color) ? 'red' : $color;
		$return .= '<div title="'.get_the_title() .'" class="tdd_pb_bar_container"><div class="numbers">'. $percentage .'%</div>';
		$return .= '<div class="tdd_pb_bar '. $color .'" style="width:'. $percentage .'%"></div></div>';
	
	endwhile;
	
	//Close the progress bar container, and return everything to screen.
	$return .= '</div>';
	return $return;
}
add_shortcode('progress', 'tdd_pb_shortcode' );