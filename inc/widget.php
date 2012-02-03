<?php
/*
Sidebar Widget Class for the TDD Progress Bar
*/

//Initalize/Register widget
add_action( 'widgets_init', 'tdd_progress_register_widget' );
function tdd_progress_register_widget() {
	register_widget( 'TDD_Progress_Widget' );
}

class TDD_Progress_Widget extends WP_Widget {

	function TDD_Progress_Widget() {
		$widget_opts = array(
			'classname' => 'tdd-progress-widget',
			'description' => 'Display progress bars in the sidebar',
			);
		$this->WP_Widget( 'TDD_Progress_Widget', 'TDD Progress Bars', $widget_opts );
	}
	
	function form($instance){
		$defaults = array(
			'title' => 'Progress',
			'ids' => '',
			'desc' => '',
		);
		
		//merge defaults with existing instance
		$instance = wp_parse_args ( (array) $instance, $defaults );
		$title = $instance['title'];
		$ids = $instance['ids'];
		$desc = $instance['desc'];
		?>
		<p>Title: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p>Progress Bar IDs: <input class="widefat" id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>" type="text" value="<?php echo esc_attr( $ids ); ?>" /><br><small>Comma separated</small></p>
		
		<p>Description:
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'desc '); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>"><?php echo esc_attr($desc); ?></textarea>

		<?php
	}

	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = strip_tags ( $new_instance['title'] );
		$instance['ids'] = strip_tags( $new_instance['ids'] );
		$instance['desc'] = strip_tags( $new_instance['desc'] );
		return $instance;
	}
	
	function widget( $args, $instance ){
		extract($args);

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( !empty($title) ){ echo $before_title . $title . $after_title; }
		
		$ids = explode ( ',', $instance['ids'] );
		
		$args = array(
			'ids' => $ids,
			);

		echo tdd_pb_get_bars( $args );
		
		echo ( $instance['desc'] ) ? wpautop($instance['desc']) : '' ;
		
		echo $after_widget;
	}

}