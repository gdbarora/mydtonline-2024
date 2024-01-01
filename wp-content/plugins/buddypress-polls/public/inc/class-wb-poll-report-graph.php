<?php
/**
 * Create a custom widget class.
 */
class Wb_Poll_Report extends WP_Widget {

	/**
	 * Constructor function.
	 */
	public function __construct() {
		parent::__construct(
			'wb_poll_report', // Widget ID.
			'WB Poll Report', // Widget name.
			array( 'description' => 'A custom widget for your WordPress site' ) // Widget description.
		);
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	function enqueue_scripts() {
		wp_enqueue_script( 'bpolls-poll-activity-graph-js', BPOLLS_PLUGIN_URL . '/public/js/wbpoll-graph-repoet.js', array( 'jquery' ) );
	}

	// Widget front-end display.
	public function widget( $args, $instance ) {
		global $wpdb, $current_user;

		/*
		  * Widget output.
		 */
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		/*
			* Widget content.
		 */
		$poll_id = $instance['wb_activity_default'];
		echo '<h4 class="widget-title"><span>' . esc_html__( $instance['title'], 'buddypress-polls' ) . '</span></h4>';
		if ( $instance['wb_poll_type'] == 'all_voted_poll' ) {

			echo WBPollHelper::show_backend_single_poll_widget_result_all_voted( esc_html( $poll_id ), 'shortcode', 'text' ); //phpcs:ignore
		} else {
			echo WBPollHelper::show_backend_single_poll_widget_result( esc_html( $poll_id ), 'shortcode', 'text' ); //phpcs:ignore
		}?>
		<script>
			jQuery(document).ready(function() {
			jQuery('#poll_seletect').change(function() {
			  // Perform AJAX request here
			  var pollid = jQuery(this).val();
			const data = {
				pollid: pollid,
			};
			var siteUrl =wbpollpublic.url;
			jQuery.ajax({
				url: siteUrl + '/wp-json/wbpoll/v1/listpoll/result/poll',
				type: 'POST',
				contentType: 'application/json',
				data: JSON.stringify(data),
				success: function (response) {
					if (response.success) {
						jQuery('.all_polll_result').html(response.result);
					} else {
						alert('Failed to unpublish poll.');
					}
				}
			});
			});
		  });
		</script>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// Widget form back-end display.
	public function form( $instance ) {
		global $activities_template;

		/*
		  * Back up the global.
		 */
		$old_activities_template = $activities_template;

		$act_args = array(
			'action' => 'activity_poll',
			'type'   => 'activity_poll',
		);

		$defaults = array(
			'title'               => __( 'WB Poll Report', 'buddypress-polls' ),
			'wb_activity_default' => '',
			'wb_poll_type'        => '',
		);

		$instance            = wp_parse_args( (array) $instance, $defaults );
		$title               = wp_strip_all_tags( $instance['title'] );
		$wb_activity_default = wp_strip_all_tags( $instance['wb_activity_default'] );
		$wb_poll_type        = wp_strip_all_tags( $instance['wb_poll_type'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'buddypress-polls' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'wb_poll_type' ) ); ?>"><?php esc_html_e( 'Poll Result Type :', 'buddypress-polls' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'wb_poll_type' ) ); ?>" id="wb_poll_type">	
				<option value="all_voted_poll" <?php selected( $wb_poll_type, 'all_voted_poll' ); ?>>All Voted Poll</option>	
				<option value="single_poll" <?php selected( $wb_poll_type, 'single_poll' ); ?>>Single poll</option>			
			</select>
		</p>
		<p class="default_seting" style="
		<?php
		if ( $wb_poll_type == 'single_poll' ) {
			echo 'display:block;';
		} else {
			echo 'display:none;'; }
		?>
		">
			<label for="<?php echo esc_attr( $this->get_field_id( 'wb_activity_default' ) ); ?>"><?php esc_html_e( 'Default Poll to display:', 'buddypress-polls' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'wb_activity_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'wb_activity_default' ) ); ?>">
				<?php

				$args = array(
					'post_type'      => 'wbpoll', // Replace 'your_post_type' with the desired post type.
					'post_status'    => 'publish',
					'posts_per_page' => -1, // Retrieve all posts of the specified post type.
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						// Access post properties.
						$post_id    = get_the_ID();
						$post_title = get_the_title();
						?>
						<option value="<?php echo esc_html( $post_id ); ?>" <?php selected( $wb_activity_default, $post_id ); ?>><?php echo esc_html( $post_title ); ?></option>
						<?php
						// Do something with the post data.
					}
				} else {
					?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'wb_activity_default' ) ); ?>"><?php esc_html_e( 'No polls are created yet.', 'buddypress-polls' ); ?></label>
				<?php	} ?>
			</select>
		</p>
		<script>
			jQuery(document).ready(function() {
				
					jQuery('select#wb_poll_type').on('change', function() {
					var selectval = jQuery(this).val();
					if(selectval == 'all_voted_poll'){
						jQuery('.default_seting').css('display', 'none');
					}
					if(selectval == 'single_poll'){
						jQuery('.default_seting').css('display', 'block');
					}
				});
			});
		</script>
		<?php
		// Restore the global.
		$activities_template = $old_activities_template;
	}

	/**
	 * Update widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']               = wp_strip_all_tags( $new_instance['title'] );
		$instance['wb_activity_default'] = wp_strip_all_tags( $new_instance['wb_activity_default'] );
		$instance['wb_poll_type']        = wp_strip_all_tags( $new_instance['wb_poll_type'] );
		return $instance;
	}
}

/**
 * Register the widget.
 */
function register_custom_widget() {
	register_widget( 'wb_poll_report' );
}
add_action( 'widgets_init', 'register_custom_widget' );
