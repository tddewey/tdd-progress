<?php
/*
Sidebar Widget Class for the TDD Progress Bar
*/

//Initalize/Register widget
function tdd_progress_register_widget() {
	register_widget( 'TDD_Progress_Widget' );
}
add_action( 'widgets_init', 'tdd_progress_register_widget' );

/**
 * Enqueue Chosen.js and related CSS
 */
function enqueue_chosen( $hook ){
	if ( 'widgets.php' == $hook ){
		wp_enqueue_script( 'chosen' , plugins_url( '/js/chosen/chosen/chosen.jquery.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.9.8', false );
		wp_enqueue_style( 'chosen', plugins_url( 'js/chosen/chosen/chosen.css', dirname( __FILE__ ) ), false, '0.9.8' );
		wp_enqueue_style( 'chosen-overrides', plugins_url( 'css/mychosen.css', dirname( __FILE__ ) ), false, '0.9.8' );
	}
}
add_action( 'admin_enqueue_scripts', 'enqueue_chosen' );

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
			'ids' => array(),
			'desc' => '',
		);

		//merge defaults with existing instance
		$instance = wp_parse_args ( (array) $instance, $defaults );
		$title = $instance['title'];
		$ids = $instance['ids'];
		$desc = $instance['desc'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title: </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'ids' ); ?>">Progress Bars: </label><br>
			<select class="widefat chzn-select" style="width:220px" data-placeholder="Select progress bars to show" id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>[]" multiple>
				<?php
					$bars = new WP_Query( array(
						'post_type' => 'tdd_pb',
						'posts_per_page' => -1
					));
					while ( $bars->have_posts() ): $bars->the_post();
						if ( in_array( get_the_ID(), (array) $ids ) )
							$selected = "selected='selected'";
						else
							$selected = '';
						echo '<option value="' . get_the_ID() . '"' . $selected . '>';
						the_title();
						echo '</option>';
					endwhile;
					wp_reset_postdata();
				?>
			</select><br>
			<script>jQuery(".chzn-select").chosen();</script> <?php /* This entire form is ajax refreshed, putting the call here makes it stay bound on-save */ ?>
			<small>Maximum of 100, but that's a crazy-large amount, don't you think?</small>
			<!--<input class="widefat" id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>" type="text" value="<?php echo esc_attr( $ids ); ?>" /><br><small>Comma separated</small>--></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'desc' ); ?>">Description:</label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'desc '); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>"><?php echo esc_attr($desc); ?></textarea>
		</p>

		<?php
	}

	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = strip_tags ( $new_instance['title'] );
		$instance['ids'] = array_map( 'absint', $new_instance['ids'] );
		$instance['desc'] = strip_tags( $new_instance['desc'] );
		return $instance;
	}

	function widget( $args, $instance ){
		extract($args);

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( !empty($title) ){ echo $before_title . sanitize_text_field( $title ) . $after_title; }

		$args = array(
			'ids' => array_map( 'absint', (array) $instance['ids'] ),
			);

		echo tdd_pb_get_bars( $args );

		echo ( $instance['desc'] ) ? wpautop( esc_html( $instance['desc'] ) ) : '' ;

		echo $after_widget;
	}

}