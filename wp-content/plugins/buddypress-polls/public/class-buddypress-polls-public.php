<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Polls_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $wp_styles, $post;
		$option_value        = get_option( 'wbpolls_settings' );
		$poll_dashboard_page = isset( $option_value['poll_dashboard_page'] ) ? $option_value['poll_dashboard_page'] : '';
		$poll_create_page    = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';

		$rtl_css = is_rtl() ? '-rtl' : '';
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/buddypress-polls-public.css', array(), time(), 'all' );
		wp_register_style( $this->plugin_name . '-time', plugin_dir_url( __FILE__ ) . 'css/jquery.datetimepicker.css', array(), time(), 'all' );

		if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
			wp_register_style( 'wb-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
		}

		wp_register_style(
			$handle = 'wb-icons',
			$src    = plugin_dir_url( __FILE__ ) . 'css/wb-icons.css',
			$deps   = array(),
			$ver    = time(),
			$media  = 'all'
		);

		wp_register_style( 'buddypress-multi-polls', plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/buddypress-multi-polls.css', array(), $this->version );

		// Create Poll CSS file.
		wp_register_style(
			'wbpolls-create-poll',
			plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/create-poll.css',
			array(),
			$this->version
		);

		wp_register_style(
			'wbpolls-dashboard',
			plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/polls-dashboard.css',
			array(),
			$this->version
		);

		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			$_elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
			if ( $_elementor_data != '' && str_contains( $_elementor_data, 'bp_newsfeed_element_widget' ) || str_contains( $_elementor_data, 'buddypress_shortcode_activity_widget' ) || str_contains( $_elementor_data, 'bbp-activity' ) ) {
				$current_component = 'activity';
			}
		}
		$srcs = array_map( 'basename', (array) wp_list_pluck( $wp_styles->registered, 'src' ) );
		if ( function_exists( 'is_buddypress' ) && is_buddypress()
			|| is_active_widget( false, false, 'bp_poll_activity_graph_widget', true )
			|| is_active_widget( false, false, 'bp_poll_create_poll_widget', true )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bp_polls' ) ) )
			|| ( is_single() && get_post_type() == 'business' )
			|| 'activity' === $current_component
			|| function_exists( 'bp_search_is_search' ) && bp_search_is_search()
			) {
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( $this->plugin_name . '-time' );
			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome' );
			}
			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}
		}

		if ( is_page() && get_the_ID() == $poll_dashboard_page ) {
			wp_enqueue_style( 'buddypress-multi-polls' );

			// Loads dynamic inline color style.
			$wbpolls_color_css = $this->wbpolls_load_color_palette();
			wp_add_inline_style( 'buddypress-multi-polls', $wbpolls_color_css );

			wp_enqueue_style( 'wbpolls-dashboard' );
			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}
		}

		if ( ( is_page() && get_the_ID() == $poll_create_page )
				|| ( is_single() && get_post_type() == 'wbpoll' )
				|| ( is_archive() && get_post_type() == 'wbpoll' )
				|| ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wbpoll' ) )
			) {
			wp_enqueue_media();
			wp_enqueue_style( 'buddypress-multi-polls' );

			// Loads dynamic inline color style.
			$wbpolls_color_css = $this->wbpolls_load_color_palette();
			wp_add_inline_style( 'buddypress-multi-polls', $wbpolls_color_css );

			wp_enqueue_style( 'wbpolls-create-poll' );
			wp_enqueue_style( $this->plugin_name . '-time' );
			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global  $post;
		$option_value        = get_option( 'wbpolls_settings' );
		$poll_dashboard_page = isset( $option_value['poll_dashboard_page'] ) ? $option_value['poll_dashboard_page'] : '';
		$poll_create_page    = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';

		wp_register_script( $this->plugin_name . '-timejs', plugin_dir_url( __FILE__ ) . 'js/jquery.datetimepicker.js', array( 'jquery' ), time(), false );
		wp_register_script( $this->plugin_name . '-timefulljs', plugin_dir_url( __FILE__ ) . 'js/jquery.datetimepicker.full.js', array( 'jquery' ), time(), false );
		wp_register_script( 'buddypress-multi-polls', plugin_dir_url( __FILE__ ) . 'js/buddypress-multi-polls.js', array( 'jquery' ), time(), false );

		wp_register_script( 'wbpoll-base64', plugin_dir_url( __FILE__ ) . 'js/jquery.base64.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'pristine', plugin_dir_url( __FILE__ ) . 'js/pristine.min.js', array(), $this->version, true );
		wp_register_script( 'wbpoll-publicjs', plugin_dir_url( __FILE__ ) . 'js/wbpoll-public.js', array( 'jquery', 'wbpoll-base64', 'pristine' ), $this->version, true );

		wp_register_script( 'buddypress-multi-polls', plugin_dir_url( __FILE__ ) . 'js/buddypress-multi-polls.js', array( 'jquery', 'ebpoll-base64', 'pristine' ), $this->version, true );

		// Create poll JS file.
		wp_register_script( 'wbpolls-create-poll', plugin_dir_url( __FILE__ ) . 'js/create-poll.js', array( 'jquery' ), $this->version, true );

		wp_register_script( 'wbpolls-poll-dashboard-js', plugin_dir_url( __FILE__ ) . 'js/poll-dashboard.js', array( 'jquery' ), $this->version, true );

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-polls-public.js', array( 'jquery' ), time(), false );

		if ( 'REIGN' === wp_get_theme() || 'REIGN Child' === wp_get_theme() ) {
			$body_polls_class = true;
		} else {
			$body_polls_class = false;
		}

		$rt_poll_fix = false;
		if ( class_exists( 'RTMedia' ) ) {
			$rt_poll_fix = true;
		}

		$active_template = get_option( '_bp_theme_package_id' );
		$nouveau         = '';
		if ( 'legacy' === $active_template ) {
			$nouveau = false;
		} elseif ( 'nouveau' === $active_template ) {
			$nouveau = true;
		}

		$bpolls_settings = get_site_option( 'bpolls_settings' );
		wp_localize_script(
			$this->plugin_name,
			'bpolls_ajax_object',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'         => wp_create_nonce( 'bpolls_ajax_security' ),
				'submit_text'        => __( 'Submitting vote', 'buddypress-polls' ),
				'optn_empty_text'    => __( 'Please select your choice.', 'buddypress-polls' ),
				'reign_polls'        => $body_polls_class,
				'rt_poll_fix'        => $rt_poll_fix,
				'nouveau'            => $nouveau,
				'buddyboss'          => ( class_exists( 'buddypress' ) ) ? buddypress()->buddyboss : '',
				'polls_option_lmit'  => ( isset( $bpolls_settings['options_limit'] ) ) ? $bpolls_settings['options_limit'] : 5,
				'poll_limit_voters'  => ( isset( $bpolls_settings['poll_limit_voters'] ) ) ? $bpolls_settings['poll_limit_voters'] : 3,
				/* translators: %d: Polls Max Options */
				'poll_max_options'   => __( 'The max number of allowed options is %d.', 'buddypress-polls' ),
				'add_poll_text'      => __( 'Add a poll', 'buddypress-polls' ),
				'delete_polls_title' => __( 'Delete option', 'buddypress-polls' ),
				'delete_polls_msg'   => __( 'Are you sure that you want to delete this option from the poll?', 'buddypress-polls' ),
				'cancel_polls_btn'   => __( 'Cancel', 'buddypress-polls' ),
				'delete_polls_btn'   => __( 'Delete', 'buddypress-polls' ),
				'poll_revoting'      => ( isset( $bpolls_settings['poll_revoting'] ) ) ? $bpolls_settings['poll_revoting'] : '',
				'poll_user'          => get_current_user_id(),
				'allowed_polls'      => $this->bpolls_is_user_allowed_polls(),
				'hide_poll_icon'     => ( isset( $bpolls_settings['hide_poll_icon'] ) ) ? $bpolls_settings['hide_poll_icon'] : '',
			)
		);

		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			//$_elementor_controls_usage = get_post_meta( $post->ID, '_elementor_controls_usage', true );
			$_elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
			if ( $_elementor_data != '' && str_contains( $_elementor_data, 'bp_newsfeed_element_widget' ) || str_contains( $_elementor_data, 'buddypress_shortcode_activity_widget' ) || str_contains( $_elementor_data, 'bbp-activity' ) ) {
				$current_component = 'activity';
			}
		}

		if ( function_exists( 'is_buddypress' ) && is_buddypress()
				|| is_active_widget( false, false, 'bp_poll_activity_graph_widget', true )
				|| is_active_widget( false, false, 'bp_poll_create_poll_widget', true )
				|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
				|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
				|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bp_polls' ) ) )
				|| ( is_single() && get_post_type() == 'business' )
				|| 'activity' === $current_component
				) {
			if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			wp_enqueue_media();
			wp_enqueue_script( $this->plugin_name . '-timejs' );
			wp_enqueue_script( $this->plugin_name . '-timefulljs' );

			wp_enqueue_script( $this->plugin_name );

		}
		wp_enqueue_script( 'wbpoll-publicjs' );
		if ( is_page() && get_the_ID() == $poll_dashboard_page ) {
			wp_enqueue_script( 'wbpoll-publicjs' );
			wp_enqueue_script( 'wbpolls-poll-dashboard-js' );
		}

		if ( ( is_page() && get_the_ID() == $poll_create_page )
				|| ( is_single() && get_post_type() == 'wbpoll' )
				|| ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wbpoll' ) )
			) {
			wp_enqueue_script( 'buddypress-multi-polls' );
			wp_enqueue_script( $this->plugin_name . '-timejs' );
			wp_enqueue_script( $this->plugin_name . '-timefulljs' );
			wp_enqueue_script( 'wbpoll-publicjs' );
			wp_enqueue_script( 'wbpolls-create-poll' );
		}

		wp_localize_script(
			'wbpoll-publicjs',
			'wbpollpublic',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'no_answer_error' => esc_html__( 'Please select at least one answer', 'buddypress-polls' ),
				'url'             => site_url(),
			)
		);

	}

	/**
	 * Bpolls_is_user_allowed_polls
	 */
	public function bpolls_is_user_allowed_polls() {
		$bpolls_settings = get_site_option( 'bpolls_settings' );
		global $current_user;

		if ( ! empty( $current_user->roles ) && in_array( 'administrator', $current_user->roles ) ) {
			return true;
		}

		if ( isset( $bpolls_settings['limit_poll_activity'] ) && 'user_role' === $bpolls_settings['limit_poll_activity'] ) {
			$exists             = false;
			$allowed_user_roles = ( isset( $bpolls_settings['poll_user_role'] ) ) ? $bpolls_settings['poll_user_role'] : array();
			if ( count( $allowed_user_roles ) > 0 ) {
				foreach ( $current_user->roles as $role ) {
					if ( in_array( $role, $allowed_user_roles, true ) ) {
						$exists = true;
					}
				}
				if ( ! $exists ) {
					return false;
				}
			} else {
				return false;
			}
		}

		if ( isset( $bpolls_settings['limit_poll_activity'] ) && 'member_type' === $bpolls_settings['limit_poll_activity'] ) {
			$member_type = bp_get_member_type( $current_user->ID );
			if ( ! isset( $bpolls_settings['poll_member_type'] ) || ! in_array( $member_type, $bpolls_settings['poll_member_type'], true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Function to render polls html.
	 *
	 * @since    1.0.0
	 */
	public function bpolls_polls_update_html() {
		if ( $this->bpolls_is_user_allowed_polls() ) {
			$bpolls_settings   = get_site_option( 'bpolls_settings' );
			$polls_option_lmit = ( isset( $bpolls_settings['options_limit'] ) ) ? $bpolls_settings['options_limit'] : 5;

			$hidepoll = isset( $bpolls_settings['hide_poll_icon'] ) ? $bpolls_settings['hide_poll_icon'] : '';
			if ( $hidepoll != 'yes' ) {
				?>
				<div class="post-elements-buttons-item bpolls-html-container">
					<span class="bpolls-icon bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="<?php esc_attr_e( 'Add a poll', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-bar-chart"></i></span>
				</div>
				<?php
			}
		}
	}

	/**
	 * Function to render poll options html
	 *
	 * @since 3.7.3
	 */
	public function bppolls_polls_options_container() {

		if ( ! $this->bpolls_is_user_allowed_polls() ) {
			return false;
		}
		$bpolls_settings = get_site_option( 'bpolls_settings' );

		global $current_user;
		$multi_true = false;
		if ( isset( $bpolls_settings['multiselect'] ) ) {
			$multi_true = true;
		}
		$add_option_true = false;
		if ( isset( $bpolls_settings['user_additional_option'] ) ) {
			$add_option_true = true;
		}
		$hide_results = true;
		if ( isset( $bpolls_settings['hide_results'] ) ) {
			$hide_results = false;
		}

		$poll_cdate = false;
		if ( isset( $bpolls_settings['close_date'] ) ) {
			$poll_cdate = true;
		}

		$image_attachment = false;
		if ( isset( $bpolls_settings['enable_image'] ) ) {
			$image_attachment = true;
		}
		?>
		<div class="bpolls-polls-option-html">
			<div class="bpolls-cancel-div">
				<a class="bpolls-cancel" href="JavaScript:void(0);"><?php esc_html_e( 'Cancel Poll', 'buddypress-polls' ); ?></a>
			</div>
			<div class="polls-option-image-div">
				<div class="bpolls-option-actions-wrap">
					<div class="bpolls-sortable">
						<div class="bpolls-option">
							<a class="bpolls-sortable-handle" title="Move" href="#"><i class="fa fa-arrows-alt"></i></a>
							<input name="bpolls_input_options[]" class="bpolls-input bpolls_input_options" placeholder="<?php esc_html_e( 'Option 1', 'buddypress-polls' ); ?>" type="text">
							<a class="bpolls-option-delete" title="Delete" href="JavaScript:void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
						</div>
						<?php if ( isset( $bpolls_settings['options_limit'] ) && $bpolls_settings['options_limit'] > 1 ) : ?>
						<div class="bpolls-option">
							<a class="bpolls-sortable-handle" title="Move" href="#"><i class="fa fa-arrows-alt"></i></a>
							<input name="bpolls_input_options[]" class="bpolls-input bpolls_input_options" placeholder="<?php esc_html_e( 'Option 2', 'buddypress-polls' ); ?>" type="text">
							<a class="bpolls-option-delete" title="Delete" href="JavaScript:void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
						</div>
						<?php endif; ?>
					</div>
					<div class="bpolls-option-action">
						<a href="JavaScript:void(0);" class="bpolls-add-option button"><?php esc_html_e( 'Add new option', 'buddypress-polls' ); ?></a>
						<?php if ( $poll_cdate ) { ?>
							<div class="bpolls-date-time">
								<input id="bpolls-datetimepicker" name="bpolls-close-date" type="textbox" value="" placeholder="<?php esc_html_e( 'Poll closing date & time', 'buddypress-polls' ); ?>">
							</div>
						<?php } ?>
					</div>
					<?php if ( $multi_true ) { ?>
						<div class="bpolls-checkbox">
							<input id="bpolls-alw-multi" name="bpolls_multiselect" class="bpolls-allow-multiple" type="checkbox" value="yes">
							<label class="lbl" for="bpolls-alw-multi"><?php esc_html_e( 'Allow members to choose multiple poll options.', 'buddypress-polls' ); ?></label>
						</div>
					<?php } ?>

					<?php if ( $add_option_true ) { ?>
						<div class="bpolls-checkbox">
							<input id="bpolls-alw-user-additional-option" name="bpolls_user_additional_option" class="bpolls-allow-user-additional-option" type="checkbox" value="yes">
							<label class="lbl" for="bpolls-alw-user-additional-option"><?php esc_html_e( 'Allow members to add their poll options.', 'buddypress-polls' ); ?></label>
						</div>
					<?php } ?>

					<?php if ( $hide_results ) { ?>
						<div class="bpolls-checkbox">
							<input id="bpolls-alw-user-hide-results" name="bpolls_user_hide_results" class="bpolls-allow-user-hide-results" type="checkbox" value="yes">
							<label class="lbl" for="bpolls-alw-user-hide-results"><?php esc_html_e( 'Hide poll results from members who have not voted yet. ', 'buddypress-polls' ); ?></label>
						</div>
					<?php } ?>

					<?php if ( isset( $bpolls_settings['enable_thank_you_message'] ) ) { ?>
						<div class="bpolls-checkbox bpolls-feedback">
							<span><?php esc_html_e( 'Follow-up', 'buddypress-polls' ); ?></span>
							<input type="text" id="bpolls-thankyou-feedback" name="bpolls_thankyou_feedback" class="bpolls-thankyou-feedback"  value="" placeholder="<?php esc_html_e( 'Enter Message', 'buddypress-polls' ); ?>">

						</div>
					<?php } ?>
					<?php if ( $image_attachment ) { ?>
						<button type='button' class="dashicons dashicons-admin-media" id="bpolls-attach-image"></button>
					<?php } ?>
				</div>
				<?php if ( $image_attachment ) { ?>
					<div class="bpolls-image-upload">
						<img id="bpolls-image-preview" />
						<input type="hidden" id="bpolls-attachment-url" name="bpolls-attachment-url">
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Filters the default activity types to add poll type activity.
	 *
	 * @since 1.0.0
	 *
	 * @param array $types Default activity types to moderate.
	 */
	public function bpolls_add_polls_type_activity( $types ) {
		$types[] = 'activity_poll';
		return $types;
	}

	/**
	 * Register the activity stream actions for poll updates.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_register_activity_actions() {
		$bp = buddypress();
		bp_activity_set_action(
			$bp->activity->id,
			'activity_poll',
			__( 'Polls Update', 'buddypress-polls' ),
			array( $this, 'bp_activity_format_activity_action_activity_poll' ),
			__( 'Poll', 'buddypress-polls' ),
			array( 'activity', 'group', 'member', 'member_groups' )
		);
	}

	/**
	 * Bpolls_activity_action_wall_posts
	 *
	 * @since 1.0.0
	 * @param retval   $retval retval.
	 * @param activity $activity activity.
	 */
	public function bpolls_activity_action_wall_posts( $retval, $activity ) {
		if ( 'activity_poll' !== $activity->type ) {
			return $retval;
		}

		// $retval = sprintf( __( '%s created a poll', 'buddypress-polls' ), bp_core_get_userlink( $activity->user_id ) );
		return $retval;
	}

	/**
	 * Format 'activity_poll' activity actions.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action   Static activity action.
	 * @param object $activity Activity data object.
	 * @return string $action
	 */
	public function bp_activity_format_activity_action_activity_poll( $action, $activity ) {
		/* translators: %s: */
		$action = sprintf( __( '%s created a poll', 'buddypress-polls' ), bp_core_get_userlink( $activity->user_id ) );

		/**
		 * Filters the formatted activity action update string.
		 *
		 * @since 1.0.0
		 *
		 * @param string               $action   Activity action string value.
		 * @param BP_Activity_Activity $activity Activity item object.
		 */
		return apply_filters( 'bp_activity_new_poll_action', $action, $activity );
	}

	/**
	 * To set activity action for poll type activity in group.
	 *
	 * @param string $activity_action The group activity action.
	 * @since 1.0.0
	 */
	public function bpolls_groups_activity_new_update_action( $activity_action ) {
		global $bp;
		$user_id = bp_loggedin_user_id();

		// if (isset($_POST['bpolls_input_options']) && !empty($_POST['bpolls_input_options']) && is_array($_POST['bpolls_input_options']) ) {
		// $activity_action = sprintf(__('%1$s created a poll in the group %2$s', 'buddypress'), bp_core_get_userlink($user_id), '<a href="' . bp_get_group_permalink($bp->groups->current_group) . '">' . esc_attr($bp->groups->current_group->name) . '</a>');
		// }.

		$_check_type = '';
		$_check_type = get_option( 'temp_poll_type' );

		if ( $_check_type && 'yes' === $_check_type ) {
			/* translators: %s: */
			$activity_action = sprintf( __( '%1$s created a poll in the group %2$s', 'buddypress-polls' ), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>' );
		}
		return $activity_action;
	}

	/**
	 * Function to set activity type activity_poll.
	 *
	 * @since 1.0.0
	 * @param array $activity Activity object.
	 */
	public function bpolls_update_poll_type_activity( $activity ) {
		// if (isset($_POST['bpolls_input_options']) && !empty($_POST['bpolls_input_options']) && is_array($_POST['bpolls_input_options']) ) {
		// $activity->type = 'activity_poll';
		// }.

		$_check_type = '';
		$_check_type = get_option( 'temp_poll_type' );

		if ( $_check_type && 'yes' === $_check_type ) {
			$activity->type = 'activity_poll';
			delete_option( 'temp_poll_type' );
		}
	}

	/**
	 * Action performed to save the activity meta on poll update.
	 *
	 * @param string $content The actvity content.
	 * @param int    $user_id User id.
	 * @param int    $activity_id Activity id.
	 * @param int    $g_activity_id Group Activity id.
	 * @since 1.0.0
	 */
	public function bpolls_update_poll_activity_meta( $content, $user_id, $activity_id, $g_activity_id = null ) {

		if ( isset( $g_activity_id ) ) {
			$activity_id = $g_activity_id;
		}

		/* Edit activity then return */
		if ( isset( $_REQUEST['edit_activity'] ) && $_REQUEST['edit_activity'] == 'true' ) { //phpcs:ignore
			return; 
		}
		global $wpdb;

		$activity_tbl = $wpdb->base_prefix . 'bp_activity';

		if ( isset( $_POST['bpolls_input_options'] ) && ! empty( $_POST['bpolls_input_options'] ) ) { //phpcs:ignore
			if ( isset( $_POST['bpolls_multiselect'] ) && 'yes' === $_POST['bpolls_multiselect'] ) { //phpcs:ignore
				$multiselect = 'yes';
			} else {
				$multiselect = 'no';
			}

			if ( isset( $_POST['bpolls_user_additional_option'] ) && 'yes' === $_POST['bpolls_user_additional_option'] ) { //phpcs:ignore
				$user_additional_option = 'yes';
			} else {
				$user_additional_option = 'no';
			}

			if ( isset( $_POST['bpolls_user_hide_results'] ) && 'yes' === $_POST['bpolls_user_hide_results'] ) { //phpcs:ignore
				$user_hide_results = 'yes';
			} else {
				$user_hide_results = 'no';
			}

			if ( isset( $_POST['bpolls-close-date'] ) && ! empty( $_POST['bpolls-close-date'] ) ) { //phpcs:ignore
				$close_date = isset( $_POST['bpolls-close-date'] ) ? $_POST['bpolls-close-date'] : ''; // phpcs:ignore 
			} else {
				$close_date = 0;
			}

			$poll_optn_arr = array();
			foreach ( (array) $_POST['bpolls_input_options'] as $key => $value ) { // phpcs:ignore
				if ( '' !== $value ) {
					$poll_key                   = str_replace( '%', '', sanitize_title( $value ) );
					$poll_optn_arr[ $poll_key ] = $value;
				}
			}

			$bpolls_thankyou_feedback = '';
			if ( isset( $_POST['bpolls_thankyou_feedback'] ) && ! empty( $_POST['bpolls_thankyou_feedback'] ) ) { //pcs:ignore
				$bpolls_thankyou_feedback = isset( $_POST['bpolls_thankyou_feedback'] ) ? $_POST['bpolls_thankyou_feedback'] : ''; // phpcs:ignore 
			}
			$poll_meta = array(
				'poll_option'              => $poll_optn_arr,
				'multiselect'              => $multiselect,
				'user_additional_option'   => $user_additional_option,
				'user_hide_results'        => $user_hide_results,
				'close_date'               => $close_date,
				'bpolls_thankyou_feedback' => $bpolls_thankyou_feedback,
			);
			bp_activity_update_meta( $activity_id, 'bpolls_meta', $poll_meta );

			$poll_image = get_option( 'temp_poll_image' );
			if ( $poll_image ) {
				bp_activity_update_meta( $activity_id, 'bpolls_image', $poll_image );
				delete_option( 'temp_poll_image' );
			}
		}
		delete_option( 'temp_poll_image' );
	}

	/**
	 * Filters the new poll activity content for current activity item.
	 *
	 * @since 1.0.0
	 * @param string $act Activity content.
	 * @param string $activity_obj Activity content posted by user.
	 */
	public function bpolls_update_poll_activity_content( $act = null, $activity_obj = array() ) {
		global $current_user;
		$user_id      = get_current_user_id();
		$poll_user_id = isset( $activity_obj->user_id ) ? $activity_obj->user_id : '';
		$activity_id  = isset( $activity_obj->id ) ? $activity_obj->id : '';

		if ( isset( $act ) && null !== $act ) {
			$activity_id = $act;
		}
		$activity_poll_type = '';

		if ( ! empty( $activity_obj ) && '' !== $activity_obj->type ) {
			$activity_poll_type = $activity_obj->type;
		}

		$bpolls_settings        = get_site_option( 'bpolls_settings' );
		$poll_options_result    = ( isset( $bpolls_settings['poll_options_result'] ) ) ? true : false;
		$poll_revoting          = ( isset( $bpolls_settings['poll_revoting'] ) ) ? true : false;
		$polls_background_color = ( isset( $bpolls_settings['polls_background_color'] ) ) ? $bpolls_settings['polls_background_color'] : '#4caf50';

		$polls_bg_style = '';
		if ( $polls_background_color != '' ) {
			$polls_bg_style = ";background-color:$polls_background_color";
		}

				$polls_btn_style = '';
		if ( $polls_background_color != '' ) {
			$polls_btn_style = 'style="color: ' . $polls_background_color . '!important;border-color: ' . $polls_background_color . '!important"';
		}
		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
		$total_votes   = bp_activity_get_meta( $activity_id, 'bpolls_total_votes', true );
		$poll_image    = bp_activity_get_meta( $activity_id, 'bpolls_image', true );

		$submit       = false;
		$hide_results = false;

		$poll_style = '';
		if ( ! $poll_revoting ) {
			$poll_style = 'style="display:none;"';
		}
		$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );
		if ( $bpoll_user_vote ) {
			if ( ! array_key_exists( $activity_id, $bpoll_user_vote ) ) {
				$submit = true;
				if ( isset( $bpolls_settings['hide_results'] ) ) {
					$hide_results = true;
				}
				if ( isset( $activity_meta['user_hide_results'] ) && $activity_meta['user_hide_results'] == 'yes' ) {
					$hide_results = true;
				}
				$poll_style = '';
			}
		} else {
			$submit = true;
			if ( isset( $bpolls_settings['hide_results'] ) ) {
				$hide_results = true;
			}
			if ( isset( $activity_meta['user_hide_results'] ) && $activity_meta['user_hide_results'] == 'yes' ) {
				$hide_results = true;
			}
			$poll_style = '';
		}

		$poll_closing = false;
		if ( isset( $activity_meta['close_date'] ) && isset( $bpolls_settings['close_date'] ) && $activity_meta['close_date'] != 0 ) {
			$current_time    = new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) );
			$close_date      = $activity_meta['close_date'];
			$close_date_time = new DateTime( $close_date );
			if ( $close_date_time > $current_time ) {
				$poll_closing = true;
			}
		} else {
			$poll_closing = true;
		}

		$u_meta = array();
		if ( isset( $bpoll_user_vote[ $activity_id ] ) ) {
			$u_meta = $bpoll_user_vote[ $activity_id ];
		}
		if ( 'activity_poll' === $activity_poll_type || isset( $activity_meta['poll_option'] ) ) {
			$poll_options = ( isset( $activity_meta['poll_option'] ) ) ? $activity_meta['poll_option'] : array();

			$user_polls_option      = get_user_meta( $user_id, 'bpolls_user_options', true );
			$activity_content       = '';
			$user_additional_option = 'no';
			if ( isset( $activity_meta['user_additional_option'] ) && 'yes' === $activity_meta['user_additional_option'] ) {
				$user_additional_option = 'yes';
			}

			if ( isset( $activity_meta['multiselect'] ) && 'yes' === $activity_meta['multiselect'] ) {
				$optn_typ = 'checkbox';
			} else {
				$optn_typ = 'radio';
			}

			if ( ! empty( $poll_options ) && is_array( $poll_options ) ) {
				$activity_content .= "<div class='bpolls-options-attach-container'>";

				if ( $poll_image ) {
					$activity_content .= "<div class='bpolls-image-container'>";
					$activity_content .= "<img src='" . $poll_image . "'>";
					$activity_content .= '</div>';
				}
				$activity_content .= "<div class='bpolls-options-attach-items'><form class='bpolls-vote-submit-form' method='post' action=''>";

				foreach ( $poll_options as $key => $value ) {

					if ( isset( $activity_meta['poll_total_votes'] ) ) {
						$total_votes = $activity_meta['poll_total_votes'];
					} else {
						$total_votes = 0;
					}

					if ( isset( $activity_meta['poll_optn_votes'] ) && array_key_exists( $key, $activity_meta['poll_optn_votes'] ) ) {
						$this_optn_vote = $activity_meta['poll_optn_votes'][ $key ];
					} else {
						$this_optn_vote = 0;
					}

					if ( 0 != $total_votes ) {
						$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
					} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
						$vote_percent = '100%';
					} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && ! in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
						$vote_percent = '0%';
					} else {
						$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
					}

					$bpolls_votes_txt = '(' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . ')';

					if ( $hide_results && ! in_array( 'administrator', (array) $current_user->roles, true ) ) {
						$vote_percent     = '';
						$bpolls_votes_txt = '';
					}

					if ( in_array( $key, $u_meta, true ) ) {
						$checked = 'checked';
					} else {
						$checked = '';
					}

					$activity_content .= "<div class='bpolls-item'>";
					$activity_content .= "<div class='bpolls-item-inner'>";

					$output                 = '';
					$activity_votes_content = '';
					$count                  = 0;

					// $activity_content .= "<span class='bpolls-votes'>" . $bpolls_votes_txt . '</span>';
					$poll_optn_user_votes = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();

					if ( ! empty( $poll_optn_user_votes ) && isset( $bpolls_settings['poll_list_voters'] ) && ! $hide_results ) {

						$count       = count( $poll_optn_user_votes );
						$liked_count = $count - $bpolls_settings['poll_limit_voters'];
						foreach ( $poll_optn_user_votes as $userkey => $user_id ) {
							// Get Users Image.
							$img_path = bp_core_fetch_avatar(
								array(
									'item_id' => $user_id,
									'type'    => 'thumb',
									'html'    => true,
								)
							);
							// How Much User Visible.
							if ( $userkey < $bpolls_settings['poll_limit_voters'] ) {

								// Get User Image Code.
								$output .= '<a data-polls-tooltip="' . bp_core_get_user_displayname( $user_id ) . '" href="' . bp_core_get_user_domain( $user_id ) . '">' . bp_core_fetch_avatar(
									array(
										'html'    => true,
										'type'    => 'thumb',
										'item_id' => $user_id,
									)
								) . '</a>';
							}
						}

						if ( isset( $output ) ) {

							if ( $liked_count > 0 ) {
								// Display Show More.
								$output .= '<a class="bp-polls-show-voters bp-polls-view-all" data-activity-id="' . $activity_id . '" data-option-id="' . $key . '" data-polls-tooltip="' . __( 'View All', 'buddypress-polls' ) . '">+' . $liked_count . '</a>';
							}
							/* translators: %s: Vote Count */
							$activity_votes_content = '<div class="bpolls-post-voted">' . $output . '</div><span class="bp-polls-voters">' . sprintf( _n( '%s Vote', '%s Votes', $count, 'buddypress-polls' ), $count ) . '</span>';

						}
					}

					if ( $poll_options_result ) {
						$activity_votes_content .= "<span class='bpolls-percent'>" . $vote_percent . '</span>';
					}


					$activity_content .= '<div id="activity-id-' . $activity_id . '-' . $key . '" class="bpolls-result-votes">' . $activity_votes_content . '</div>';

					$activity_content .= '<div class="bpolls-check-radio-wrap">';
					if ( ( $submit && $poll_closing && is_user_logged_in() ) || ( $poll_revoting && $poll_closing && is_user_logged_in() ) ) {
						$activity_content .= "<input id='" . $key . "' name='bpolls_vote_optn[]' value='" . $key . "' type='" . $optn_typ . "' " . $checked . ' ' . $poll_style . '>';
					}
					$activity_content .= "<label for='" . $key . "' class='bpolls-option-lbl'>" . $value . '</label>';
					$activity_content .= '</div>';

					$activity_content .= "<div class='bpolls-item-width-wrapper'>";
					// $activity_content .= "<div class='bpolls-item-width' style='width:" . $vote_percent . ";'></div>";
					$activity_content .= "<div class='bpolls-item-width' style='width:" . $vote_percent . $polls_bg_style . ";'></div>";
					$activity_content .= "<div class='bpolls-check-radio-div'></div>";
					$activity_content .= '</div>';
					
					$activity_content .= '</div>';
					if ( isset( $user_polls_option[ 'activity-id-' . $activity_id . '-' . $key ] ) ) {
						$activity_content .= "<a href='javascript:void(0);' class='bpolls-delete-user-option' data-activity-id='" . $activity_id . "' data-option='" . $key . "' data-user-id='" . $user_id . "'><i class='wb-icons wb-icon-x'></i></a>";
					}
					$activity_content .= '</div>';
				}

				/* Add option from user end */
				if ( $user_additional_option == 'yes' ) {
					if ( ( $submit && $poll_closing && is_user_logged_in() ) || ( $poll_revoting && $poll_closing && is_user_logged_in() ) ) {
						$activity_content .= "<div class='bpolls-add-user-item'>";
						$activity_content .= '<input type="text" class="bpoll-add-user-option" name="bpoll_user_option" value="" placeholder="' . esc_html__( 'Add poll option...', 'buddypress-polls' ) . '" data-activity-id="' . $activity_id . '" data-user-id="' . $user_id . '"/>';
						$activity_content .= '<input type="button" class="bpoll-add-option" name="bpoll_add_option" value="' . esc_html__( 'Add option', 'buddypress-polls' ) . '" data-activity-id="' . $activity_id . '" data-user-id="' . $user_id . '"/>';
						$activity_content .= '</div>';
						$activity_content .= '<p class="bpolls-add-option-error" style="display:none">' . esc_html__( 'Poll option field is empty.', 'buddypress-polls' ) . '</p>';
					}
				}

				$activity_content .= "<input type='hidden' name='bpoll_activity_id' value='" . $activity_id . "'>";
				$activity_content .= "<input type='hidden' name='bpoll_multi' value='" . $activity_meta['multiselect'] . "'>";
				$activity_content .= "<input type='hidden' name='bpoll_user_id' value='" . $user_id . "'>";

				if ( ( $submit && $poll_closing && is_user_logged_in() ) || ( $poll_revoting && $poll_closing && is_user_logged_in() ) ) {
					$activity_content .= "<a class='bpolls-vote-submit' href='javascript:void(0)' " . $polls_btn_style . '>' . __( 'Submit', 'buddypress-polls' ) . '</a>';
				}
				$activity_content .= '</form></div></div>';

				if ( isset( $act ) && $act != null ) {
					return $activity_content;
				} else {
					echo $activity_content; // phpcs:ignore WordPress.Security.EscapeOutput
				}
			}
		}
	}

	/**
	 * Ajax request to save poll vote.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_save_poll_vote() {
		if ( isset( $_POST['action'] ) && 'bpolls_save_poll_vote' === $_POST['action'] ) {
			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
			$user_id         = get_current_user_id();
			$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );

			parse_str( $_POST['poll_data'], $poll_data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$poll_data = filter_var_array( $poll_data );

			$activity_id = $poll_data['bpoll_activity_id'];

			$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
			$total_votes   = bp_activity_get_meta( $activity_id, 'bpolls_total_votes', true );
			if ( ! $total_votes ) {
				$total_votes = (int) 1;
			} else {
				$total_votes = (int) $total_votes + (int) 1;
			}
			if ( array_key_exists( 'poll_optn_votes', $activity_meta ) ) {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {

					if ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
						if ( ( $ukey = array_search( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) !== false ) {
							$activity_meta['poll_optn_votes'][ $key ] = $activity_meta['poll_optn_votes'][ $key ] - 1;
						}
					}

					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
						$activity_meta['poll_optn_votes'][ $key ] = $activity_meta['poll_optn_votes'][ $key ] + 1;
					}
				}
			} else {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
						$ed = 1;
					} else {
						$ed = 0;
					}
					$poll_optn_votes[ $key ] = $ed;
				}
				$activity_meta['poll_optn_votes'] = $poll_optn_votes;
			}

			if ( array_key_exists( 'poll_total_votes', $activity_meta ) ) {
				if ( ! isset( $bpoll_user_vote[ $activity_id ] ) ) {
					$activity_meta['poll_total_votes'] = $activity_meta['poll_total_votes'] + 1;
				}
			} else {
				$activity_meta['poll_total_votes'] = 1;
			}

			/* Saved user id in poll option wise */
			if ( array_key_exists( 'poll_optn_user_votes', $activity_meta ) ) {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {

					if ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && is_array( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
						if ( ( $ukey = array_search( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) !== false ) {
							unset( $activity_meta['poll_optn_user_votes'][ $key ][ $ukey ] );
						}
					}

					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {

						$polls_existing_useid                          = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();
						$activity_meta['poll_optn_user_votes'][ $key ] = array_unique( array_merge( $polls_existing_useid, array( $user_id ) ) );
					}
				}
			} else {
				$poll_optn_user_votes = array();
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					$poll_optn_user_votes[ $key ] = array();
					if ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
						if ( ( $key = array_search( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) !== false ) {
							unset( $activity_meta['poll_optn_user_votes'][ $key ][ $key ] );
						}
					}

					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
						$poll_optn_user_votes[ $key ] = array( $user_id );
					}
				}

				$activity_meta['poll_optn_user_votes'] = $poll_optn_user_votes;
			}

			/* saved User id in activity meta */
			$existing_useid              = isset( $activity_meta['poll_users'] ) ? $activity_meta['poll_users'] : array();
			$activity_meta['poll_users'] = array_unique( array_merge( $existing_useid, array( $user_id ) ) );

			/* Total Poll Votes count base on poll users */
			$activity_meta['poll_total_votes'] = count( $activity_meta['poll_users'] );

			bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );

			bp_activity_update_meta( $activity_id, 'bpolls_total_votes', $total_votes );

			$user_vote = array();
			foreach ( $activity_meta['poll_option'] as $key => $value ) {

				if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
					$user_vote[] = $key;
				}
			}

			if ( $bpoll_user_vote ) {
				// if ( ! array_key_exists( $activity_id, $bpoll_user_vote ) )
				{
					$bpoll_user_vote[ $activity_id ] = $user_vote;
					update_user_meta( $user_id, 'bpoll_user_vote', $bpoll_user_vote );
				}
			} else {
				$vote[ $activity_id ] = $user_vote;
				update_user_meta( $user_id, 'bpoll_user_vote', $vote );
			}

			$updated_votes = $this->bpolls_ajax_calculate_votes( $activity_id );

			do_action( 'bp_polls_after_submit_polls', $user_id, $activity_id );

			echo wp_json_encode( $updated_votes );
			die;
		}
	}

	/**
	 * Calculate poll activity votes.
	 *
	 * @since 1.0.0
	 * @param string $activity_id activity_id.
	 */
	public function bpolls_ajax_calculate_votes( $activity_id ) {
		$user_id = get_current_user_id();

		$bpolls_settings     = get_site_option( 'bpolls_settings' );
		$poll_options_result = ( isset( $bpolls_settings['poll_options_result'] ) ) ? true : false;

		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );

		$poll_options = isset( $activity_meta['poll_option'] ) ? $activity_meta['poll_option'] : '';

		$uptd_votes = array();
		if ( ! empty( $poll_options ) && is_array( $poll_options ) ) {
			foreach ( $poll_options as $key => $value ) {
				if ( isset( $activity_meta['poll_total_votes'] ) ) {
					$total_votes = $activity_meta['poll_total_votes'];
				} else {
					$total_votes = 0;
				}

				if ( isset( $activity_meta['poll_optn_votes'] ) && array_key_exists( $key, $activity_meta['poll_optn_votes'] ) ) {
					$this_optn_vote = $activity_meta['poll_optn_votes'][ $key ];
				} else {
					$this_optn_vote = 0;
				}

				if ( $total_votes != 0 ) {
					$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
				} else {
					$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
				}

				if ( 0 != $total_votes ) {
					$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
				} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
					$vote_percent = '100%';
				} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && ! in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
					$vote_percent = '0%';
				} else {
					$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
				}

				$bpolls_votes_txt = '(' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . ')';

				$output       = '';
				$vote_content = '';
				$count        = 0;

				// $activity_content .= "<span class='bpolls-votes'>" . $bpolls_votes_txt . '</span>';
				$poll_optn_user_votes = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();

				if ( ! empty( $poll_optn_user_votes ) && isset( $bpolls_settings['poll_list_voters'] ) ) {

					$count       = count( $poll_optn_user_votes );
					$liked_count = $count - $bpolls_settings['poll_limit_voters'];
					foreach ( $poll_optn_user_votes as $userkey => $user_id ) {
						// Get Users Image.
						$img_path = bp_core_fetch_avatar(
							array(
								'item_id' => $user_id,
								'type'    => 'thumb',
								'html'    => true,
							)
						);
						// How Much User Visible.
						if ( $userkey < $bpolls_settings['poll_limit_voters'] ) {

							// Get User Image Code.
							$output .= '<a data-polls-tooltip="' . bp_core_get_user_displayname( $user_id ) . '" href="' . bp_core_get_user_domain( $user_id ) . '">' . bp_core_fetch_avatar(
								array(
									'html'    => true,
									'type'    => 'thumb',
									'item_id' => $user_id,
								)
							) . '</a>';
						}
					}

					if ( isset( $output ) ) {

						if ( $liked_count > 0 ) {
							// Display Show More.
							$output .= '<a class="bp-polls-show-voters bp-polls-view-all" data-activity-id="' . $activity_id . '" data-option-id="' . $key . '" data-polls-tooltip="' . __( 'View All', 'buddypress-polls' ) . '">+' . $liked_count . '</a>';
						}
						/* translators: %s: Vote Count */
						$vote_content = '<div class="bpolls-post-voted">' . $output . '</div><span class="bp-polls-voters">' . sprintf( _n( '%s Vote', '%s Votes', $count, 'buddypress-polls' ), $count ) . '</span>';

					}
				}

				$uptd_votes[ $key ] = array(
					'vote_percent'         => $vote_percent,
					'bpolls_votes_txt'     => $bpolls_votes_txt,
					'bpolls_votes_content' => $vote_content,
				);
			}
		}
		$uptd_votes['bpolls_thankyou_feedback'] = ( isset( $activity_meta['bpolls_thankyou_feedback'] ) && $activity_meta['bpolls_thankyou_feedback'] != '' ) ? $activity_meta['bpolls_thankyou_feedback'] : '';

		return $uptd_votes;
	}

	/**
	 * Function to show poll activity entry content while embedding.
	 *
	 * @since 1.0.0
	 * @param content                 $content content.
	 * @param global_activity_content $global_activity_content global_activity_content.
	 */
	public function bpolls_bp_activity_get_embed_excerpt( $content, $global_activity_content ) {
		$activity_id = $GLOBALS['activities_template']->activity->id;
		return $content . $this->bpolls_update_poll_activity_content( $activity_id, '' );
	}

	/**
	 * Function to show poll activity entry content while embedding.
	 *
	 * @since 1.0.0
	 * @param activity_content $activity_content activity_content.
	 * @param activity_obj     $activity_obj activity_obj.
	 */
	public function bpquotes_update_pols_activity_content( $activity_content, $activity_obj ) {
		$activity_id = $activity_obj->id;
		return $activity_content . $this->bpolls_update_poll_activity_content( $activity_id, $activity_obj );
	}

	/**
	 * Function to add poll css for activity embedding.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_activity_embed_add_inline_styles() {
		$css = file_get_contents( BPOLLS_PLUGIN_PATH . '/public/css/buddypress-polls-public.css' );
		$css = wp_kses( $css, array( "\'", '\"' ) );
		printf( '<style type="text/css">%s</style>', esc_attr( $css ) );
	}

	/**
	 * Bpolls_set_poll_type_true
	 *
	 * @since 1.0.0
	 */
	public function bpolls_set_poll_type_true() {
		if ( isset( $_POST['action'] ) && 'bpolls_set_poll_type_true' === $_POST['action'] ) {

			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );

			$is_poll = ( isset( $_POST['is_poll'] ) ) ? wp_unslash( $_POST['is_poll'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			update_option( 'temp_poll_type', $is_poll );
		}
		wp_die();
	}

	/**
	 * Bpolls_set_poll_type_true
	 *
	 * @since 1.0.0
	 */
	public function bpolls_update_prev_polls_total_votes() {
		$args = array(
			'show_hidden' => true,
			'action'      => 'activity_poll',
			'count_total' => true,
		);

		if ( function_exists( 'bp_has_activities' ) && bp_has_activities( $args ) ) {
			global $activities_template;
			foreach ( $activities_template->activities as $key => $act_obj ) {
				$activity_meta = (array) bp_activity_get_meta( $act_obj->id, 'bpolls_meta' );
				$total_votes   = bp_activity_get_meta( $act_obj->id, 'bpolls_total_votes', true );
				if ( array_key_exists( 'poll_total_votes', $activity_meta ) && ! $total_votes ) {
					bp_activity_update_meta( $act_obj->id, 'bpolls_total_votes', (int) $activity_meta['poll_total_votes'] );
				}
			}
		}
	}

	/**
	 * Bpolls_save_image
	 *
	 * @since 1.0.0
	 */
	public function bpolls_save_image() {
		if ( isset( $_POST['action'] ) && 'bpolls_save_image' === $_POST['action'] ) {
			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
			$image_url = ( isset( $_POST['image_url'] ) ) ? wp_unslash( $_POST['image_url'] ) : ' '; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			update_option( 'temp_poll_image', $image_url );
			exit();
		}
	}


	/**
	 * Function to unset media library when user upload image using wp-media on fronted
	 *
	 * @since 3.3.0
	 * @param tabs $tabs tabs.
	 */
	public function bpolls_remove_media_library_tab( $tabs ) {

		if ( current_user_can( 'subscriber' ) || current_user_can( 'contributor' ) ) {
			unset( $tabs['library'] );

			$contributor = get_role( 'contributor' );
			$contributor->add_cap( 'upload_files' );

			$subscriber = get_role( 'subscriber' );
			$subscriber->add_cap( 'upload_files' );

		}
		return $tabs;
	}

	/**
	 * Function to unset media library title when user upload image using wp-media on fronted
	 *
	 * @since 3.3.0
	 * @param strings $strings strings.
	 */
	public function bpolls_remove_medialibrary_tab( $strings ) {
		if ( current_user_can( 'subscriber' ) || current_user_can( 'contributor' ) ) {
			unset( $strings['mediaLibraryTitle'] );
			return $strings;
		} else {
			return $strings;
		}
	}

	/**
	 * Function added the edit acivity or not with BB Plateform.
	 *
	 * @since 3.7.2
	 *
	 * @param bool   $can_edit Whether the user can edit the item.
	 * @param object $activity Current activity item object.
	 */
	public function bpolls_activity_can_edit( $can_edit, $activity ) {
		global $activities_template;

		// Try to use current activity if none was passed.
		if ( empty( $activity ) && ! empty( $activities_template->activity ) ) {
			$activity = $activities_template->activity;
		}

		// If current_comment is set, we'll use that in place of the main activity.
		if ( isset( $activity->current_comment ) ) {
			$activity = $activity->current_comment;
		}

		// Assume the user cannot edit the activity item.
		$can_edit = false;

		// Only logged in users can edit activity and Activity must be of type 'activity_update', 'activity_comment', 'activity_poll'.
		if ( is_user_logged_in() && in_array( $activity->type, array( 'activity_update', 'activity_comment', 'activity_poll' ) ) ) {

			// Users are allowed to edit their own activity.
			if ( isset( $activity->user_id ) && ( bp_loggedin_user_id() === $activity->user_id ) ) {
				$can_edit = true;
			}

			// Viewing a single item, and this user is an admin of that item.
			if ( bp_is_single_item() && bp_is_item_admin() ) {
				$can_edit = true;
			}
		}

		if ( $can_edit ) {

			// Check activity edit time expiration.
			$activity_edit_time        = (int) bp_get_activity_edit_time(); // for 10 minutes, 600.
			$bp_dd_get_time            = bp_core_current_time( true, 'timestamp' );
			$activity_edit_expire_time = strtotime( $activity->date_recorded ) + $activity_edit_time;

			// Checking if expire time still greater than current time.
			if ( - 1 !== $activity_edit_time && $activity_edit_expire_time <= $bp_dd_get_time ) {
				$can_edit = false;
			}
		}

		/**
		 * Filters whether the current user can edit an activity item.
		 *
		 * @param bool   $can_edit Whether the user can edit the item.
		 * @param object $activity Current activity item object.
		 *
		 * @since BP Polls 3.9.0
		 */
		return (bool) apply_filters( 'bppolls_activity_user_can_edit', $can_edit, $activity );
	}

	/**
	 *  Display all voters of activity polls.
	 *
	 * @return void
	 */
	public function bpolls_activity_all_voters() {
		if ( isset( $_POST['action'] ) && 'bpolls_activity_all_voters' === $_POST['action'] ) {
			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );

			$activity_id          = isset( $_POST['activity_id'] ) ? sanitize_text_field( wp_unslash( $_POST['activity_id'] ) ) : '';
			$option_id            = isset( $_POST['option_id'] ) ? sanitize_text_field( wp_unslash( $_POST['option_id'] ) ) : '';
			$activity_meta        = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
			$poll_optn_user_votes = isset( $activity_meta['poll_optn_user_votes'][ $option_id ] ) ? $activity_meta['poll_optn_user_votes'][ $option_id ] : array();

			ob_start();

			if ( ! empty( $poll_optn_user_votes ) ) {
				echo '<div class="bpolls-icon-dialog bpolls-user-votes-dialog is-visible">';

				echo '<div class="bpolls-icon-dialog-container">';

				echo '<div class="bpolls-modal-title"><i class="fa fa-users"></i>' . esc_html__( 'People who voted for this option', 'buddypress-polls' ) . '<i class="fa fa-times bpolls-modal-close bpolls-modal-close-icon"></i></div>';

				echo '<div class="bpolls-icon-dialog-msg">';
				echo '<div class="bpolls-users-who-list">';

				foreach ( $poll_optn_user_votes as $user_id ) {

					?>

					<div class="bpolls-list-item">
						<a href="<?php echo esc_url( bp_core_get_user_domain( $user_id ) ); ?>" class="bpolls-item-avatar">
											<?php
											echo bp_core_fetch_avatar( // phpcs:ignore WordPress.Security.EscapeOutput
												array(
													'type' => 'thumb',
													'item_id' => $user_id,
												)
											);
											?>
									</a>
						<div class="bpolls-item-data">
							<div class="bpolls-item-name"><?php echo esc_url( bp_core_get_userlink( $user_id ) ); ?></div>
							<div class="bpolls-item-meta">@<?php echo esc_html( bp_core_get_username( $user_id ) ); ?></div>
						</div>
					</div>

					<?php
				}

				echo '</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';

			} 
			
			$result_html = ob_get_contents();

			ob_end_clean();

			wp_send_json_success( $result_html );

		}
	}

	/**
	 * Embed polls activity data in rest api activity endpoint.
	 *
	 * @param  object $response get response data.
	 * @param  object $request get request data.
	 * @param  array  $activity get activity data.
	 * @return $response
	 */
	public function bpolls_activity_data_embed_rest_api( $response, $request, $activity ) {
		$bppolls_meta                                     = bp_activity_get_meta( $activity->id, 'bpolls_meta', true );
		$bppolls_vote                                     = bp_activity_get_meta( $activity->id, 'bpolls_total_votes', true );
		$response->data['bp_polls']['bpolls_meta']        = $bppolls_meta;
		$response->data['bp_polls']['bpolls_total_votes'] = $bppolls_vote;
		return $response;
	}

	/**
	 * Show the Polls Activity Data using shortcode.
	 *
	 * @param array  $atts Shortcode Attributes.
	 * @param string $content Shortcode content.
	 * @since BP Polls 4.0.0
	 */
	public function bppolls_rest_api_shortcode( $atts, $content = null ) {
		global $wpdb;
		$atts        = shortcode_atts(
			array(
				'activity_id' => null,
			),
			$atts
		);
		$activity_id = $atts['activity_id'];
		$activity_id = is_numeric( $activity_id ) ? (int) $activity_id : $activity_id;

		/* activity id is not integer then return */
		if ( ! is_int( $activity_id ) ) {
			$activity_id = 0;
			return $content;
		}
		$activity_content = '';
		ob_start();
		$activity_content .= '<div id="buddypress">';
		if ( function_exists( 'bp_is_active' ) ) {
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( $this->plugin_name . '-time' );
			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome' );
			}
			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}
			wp_enqueue_script( $this->plugin_name . '-timejs' );
			wp_enqueue_script( $this->plugin_name . '-timefulljs' );
			wp_enqueue_script( $this->plugin_name );

			global $current_user;
			$user_id                = get_current_user_id();
			$bpolls_settings        = get_site_option( 'bpolls_settings' );
			$poll_options_result    = ( isset( $bpolls_settings['poll_options_result'] ) ) ? true : false;
			$poll_revoting          = ( isset( $bpolls_settings['poll_revoting'] ) ) ? true : false;
			$polls_background_color = ( isset( $bpolls_settings['polls_background_color'] ) ) ? $bpolls_settings['polls_background_color'] : '#4caf50';
			$polls_bg_style         = '';
			if ( $polls_background_color != '' ) {
				$polls_bg_style = ";background-color:$polls_background_color";
			}

			$polls_btn_style = '';
			if ( $polls_background_color != '' ) {
				$polls_btn_style = 'style="color: ' . $polls_background_color . '!important;border-color: ' . $polls_background_color . '!important"';
			}

			$submit       = false;
			$hide_results = false;
			$poll_style   = '';
			if ( ! $poll_revoting ) {
				$poll_style = 'style="display:none;"';
			}
			$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );
			if ( $bpoll_user_vote ) {
				if ( ! array_key_exists( $activity_id, $bpoll_user_vote ) ) {
					$submit = true;
					if ( isset( $bpolls_settings['hide_results'] ) ) {
						$hide_results = true;
					}
					$poll_style = '';
				}
			} else {
				$submit = true;
				if ( isset( $bpolls_settings['hide_results'] ) ) {
					$hide_results = true;
				}
				$poll_style = '';
			}
			$activity_data = bp_activity_get_specific(
				array(
					'activity_ids' => $activity_id,
					'show_hidden'  => true,
					'spam'         => 'all',
				)
			);

			$polls_act_content = stripslashes_deep( $activity_data['activities'][0]->content );
			$activity_type     = $activity_data['activities'][0]->type;

			$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
			$total_votes   = bp_activity_get_meta( $activity_id, 'bpolls_total_votes', true );
			$poll_image    = bp_activity_get_meta( $activity_id, 'bpolls_image', true );
			$poll_closing  = false;

			if ( isset( $activity_meta['close_date'] ) && isset( $bpolls_settings['close_date'] ) && $activity_meta['close_date'] != 0 ) {
				$current_time    = new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) );
				$close_date      = $activity_meta['close_date'];
				$close_date_time = new DateTime( $close_date );
				if ( $close_date_time > $current_time ) {
					$poll_closing = true;
				}
			} else {
				$poll_closing = true;
			}
					$u_meta = array();
			if ( isset( $bpoll_user_vote[ $activity_id ] ) ) {
				$u_meta = $bpoll_user_vote[ $activity_id ];
			}

			if ( isset( $activity_meta['poll_option'] ) && $activity_type == 'activity_poll' ) {
				$poll_options = ( isset( $activity_meta['poll_option'] ) ) ? $activity_meta['poll_option'] : array();

				if ( isset( $activity_meta['multiselect'] ) && 'yes' === $activity_meta['multiselect'] ) {
					$optn_typ = 'checkbox';
				} else {
					$optn_typ = 'radio';
				}

				if ( ! empty( $poll_options ) && is_array( $poll_options ) ) {
					$activity_content .= "<div class='bpolls-options-attach-shortcode-wrapper'>";
					$activity_content .= "<div class='bpolls-options-attach-container'>";
					$activity_content .= '<p>';
					$activity_content .= $polls_act_content;
					$activity_content .= '</p>';
					if ( $poll_image ) {
						$activity_content .= "<div class='bpolls-image-container'>";
						$activity_content .= "<img src='" . $poll_image . "'>";
						$activity_content .= '</div>';
					}
					$activity_content .= "<div class='bpolls-options-attach-items'><form class='bpolls-vote-submit-form' method='post' action=''>";
					foreach ( $poll_options as $key => $value ) {

						if ( isset( $activity_meta['poll_total_votes'] ) ) {
							$total_votes = $activity_meta['poll_total_votes'];
						} else {
							$total_votes = 0;
						}

						if ( isset( $activity_meta['poll_optn_votes'] ) && array_key_exists( $key, $activity_meta['poll_optn_votes'] ) ) {
							$this_optn_vote = $activity_meta['poll_optn_votes'][ $key ];
						} else {
							$this_optn_vote = 0;
						}

						if ( 0 != $total_votes ) {
							$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
						} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
							$vote_percent = '100%';
						} elseif ( isset( $activity_meta['poll_optn_user_votes'][ $key ] ) && ! in_array( $user_id, $activity_meta['poll_optn_user_votes'][ $key ] ) ) {
							$vote_percent = '0%';
						} else {
							$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
						}

						$bpolls_votes_txt = '(' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . ')';

						if ( $hide_results && ! in_array( 'administrator', (array) $current_user->roles, true ) ) {
							$vote_percent     = '';
							$bpolls_votes_txt = '';
						}

						if ( in_array( $key, $u_meta, true ) ) {
							$checked = 'checked';
						} else {
							$checked = '';
						}

						$activity_content .= "<div class='bpolls-item'>";

						$output                 = '';
						$activity_votes_content = '';
						$count                  = 0;

						// $activity_content .= "<span class='bpolls-votes'>" . $bpolls_votes_txt . '</span>';
						$poll_optn_user_votes = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();

						if ( ! empty( $poll_optn_user_votes ) && isset( $bpolls_settings['poll_list_voters'] ) && ! $hide_results ) {

							$count       = count( $poll_optn_user_votes );
							$liked_count = $count - $bpolls_settings['poll_limit_voters'];
							foreach ( $poll_optn_user_votes as $userkey => $user_id ) {
								// Get Users Image.
								$img_path = bp_core_fetch_avatar(
									array(
										'item_id' => $user_id,
										'type'    => 'thumb',
										'html'    => true,
									)
								);
								// How Much User Visible.
								if ( $userkey < $bpolls_settings['poll_limit_voters'] ) {

									// Get User Image Code.
									$output .= '<a data-polls-tooltip="' . bp_core_get_user_displayname( $user_id ) . '" href="' . bp_core_get_user_domain( $user_id ) . '">' . bp_core_fetch_avatar(
										array(
											'html'    => true,
											'type'    => 'thumb',
											'item_id' => $user_id,
										)
									) . '</a>';
								}
							}

							if ( isset( $output ) ) {

								if ( $liked_count > 0 ) {
									// Display Show More.
									$output .= '<a class="bp-polls-show-voters bp-polls-view-all" data-activity-id="' . $activity_id . '" data-option-id="' . $key . '" data-polls-tooltip="' . __( 'View All', 'buddypress-polls' ) . '">+' . $liked_count . '</a>';
								}
								/* translators: %s: Vote Count */
								$activity_votes_content = '<div class="bpolls-post-voted">' . $output . '</div><span class="bp-polls-voters">' . sprintf( _n( '%s Vote', '%s Votes', $count, 'buddypress-polls' ), $count ) . '</span>';

							}
						}

						if ( $poll_options_result ) {
							$activity_votes_content .= "<span class='bpolls-percent'>" . $vote_percent . '</span>';
						}

						$activity_content .= '<div id="activity-id-' . $activity_id . '-' . $key . '" class="bpolls-result-votes">' . $activity_votes_content . '</div>';

						$activity_content .= '<div class="bpolls-check-radio-wrap">';
						if ( ( $submit && $poll_closing && is_user_logged_in() ) || ( $poll_revoting && $poll_closing && is_user_logged_in() ) ) {
							$activity_content .= "<input id='" . $key . "' name='bpolls_vote_optn[]' value='" . $key . "' type='" . $optn_typ . "' " . $checked . ' ' . $poll_style . '>';
						}
						$activity_content .= "<label for='" . $key . "' class='bpolls-option-lbl'>" . $value . '</label>';
						$activity_content .= '</div>';

						$activity_content .= "<div class='bpolls-item-width-wrapper'>";
						// $activity_content .= "<div class='bpolls-item-width' style='width:" . $vote_percent . ";'></div>";
						$activity_content .= "<div class='bpolls-item-width' style='width:" . $vote_percent . $polls_bg_style . ";'></div>";
						$activity_content .= "<div class='bpolls-check-radio-div'></div>";
						$activity_content .= '</div>';
						$activity_content .= '</div>';
					}
					$activity_content .= "<input type='hidden' name='bpoll_activity_id' value='" . $activity_id . "'>";
					$activity_content .= "<input type='hidden' name='bpoll_multi' value='" . $activity_meta['multiselect'] . "'>";
					$activity_content .= "<input type='hidden' name='bpoll_user_id' value='" . $user_id . "'>";

					if ( ( $submit && $poll_closing && is_user_logged_in() ) || ( $poll_revoting && $poll_closing && is_user_logged_in() ) ) {
						$activity_content .= "<a class='bpolls-vote-submit' href='javascript:void(0)' " . $polls_btn_style . '>' . __( 'Submit', 'buddypress-polls' ) . '</a>';
					}
					$activity_content .= '</form></div></div></div>';

				}
			} else {
				$activity_content .= esc_html( 'This is not a poll activity', 'buddypress-polls' );
			}
		}
		$activity_content .= '</div>';
		$activity_content .= ob_get_clean();
		return $activity_content;
	}

	/**
	 * Fires when preparing to serve a REST API request.
	 *
	 * @return void
	 *
	 * @since BP Polls 4.0.0
	 */
	public function bppolls_register_user_meta() {
		register_rest_field(
			'user',
			'bp_polls_user_data',
			array(
				'get_callback' => array( $this, 'bp_polls_user_meta_callback' ),
				'schema'       => null,
			)
		);

	}

	/**
	 * Get callback fuction of rest API.
	 *
	 * @param  int    $user User ID.
	 * @param  string $field_name Field Name.
	 * @param  data   $request API Request.
	 *
	 * @since BP Polls 4.0.0
	 */
	public function bp_polls_user_meta_callback( $user, $field_name, $request ) {
		return get_user_meta( $user['id'], 'bpoll_user_vote', true );
	}


	/**
	 * Add Activotity poll option when the user add from fronted
	 *
	 * @since BP Polls 4.0.1
	 */
	public function bpolls_activity_add_user_option() {
		check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
		$user_id         = get_current_user_id();
		$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );
		$activity_id     = isset( $_POST['activity_id'] ) ? sanitize_text_field( wp_unslash( $_POST['activity_id'] ) ) : '';
		$user_option     = isset( $_POST['user_option'] ) ? sanitize_text_field( wp_unslash( $_POST['user_option'] ) ) : '';

		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );

		$poll_key                                  = str_replace( '%', '', sanitize_title( $user_option ) );
		$activity_meta['poll_option'][ $poll_key ] = $user_option;
		/* Update Activity Poll Option */
		bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );

		$user_polls_option = get_user_meta( $user_id, 'bpolls_user_options', true );
		if ( empty( $user_polls_option ) ) {
			$user_polls_option = array();
		}
		$user_polls_option[ 'activity-id-' . $activity_id . '-' . $poll_key ] = $user_option;
		/* Update  Activity Poll Option in user meta*/
		update_user_meta( $user_id, 'bpolls_user_options', $user_polls_option );

		$user_vote                       = array();
		$poll_data['bpolls_vote_optn'][] = $poll_key;
		foreach ( $activity_meta['poll_option'] as $key => $value ) {

			if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
				$user_vote[] = $key;
			}
		}

		if ( $bpoll_user_vote ) {
			$bpoll_user_vote[ $activity_id ] = $user_vote;
			update_user_meta( $user_id, 'bpoll_user_vote', $bpoll_user_vote );
		} else {
			$vote[ $activity_id ] = $user_vote;
			update_user_meta( $user_id, 'bpoll_user_vote', $vote );
		}

		$bpolls_settings        = get_site_option( 'bpolls_settings' );
		$poll_options_result    = ( isset( $bpolls_settings['poll_options_result'] ) ) ? true : false;
		$poll_revoting          = ( isset( $bpolls_settings['poll_revoting'] ) ) ? true : false;
		$polls_background_color = ( isset( $bpolls_settings['polls_background_color'] ) ) ? $bpolls_settings['polls_background_color'] : '#4caf50';
		$polls_bg_style         = '';
		if ( $polls_background_color != '' ) {
			$polls_bg_style = ";background-color:$polls_background_color";
		}

		if ( isset( $activity_meta['multiselect'] ) && 'yes' === $activity_meta['multiselect'] ) {
			$optn_typ = 'checkbox';
		} else {
			$optn_typ = 'radio';
		}
		ob_start();
		?>
		<div class="bpolls-item">
			<div class='bpolls-item-inner'>
				<div id="activity-id-<?php echo esc_attr( $activity_id ); ?>-<?php echo esc_attr( $poll_key ); ?>" class="bpolls-result-votes"><span class="bpolls-percent"></span></div>
				<div class="bpolls-check-radio-wrap">
					<input id="<?php echo esc_attr( $poll_key ); ?>" name="bpolls_vote_optn[]" value="<?php echo esc_attr( $poll_key ); ?>" type="<?php echo esc_attr( $optn_typ ); ?>" checked>
					<label for="option-2" class="bpolls-option-lbl"><?php echo esc_html( $user_option ); ?></label>
				</div>
				<div class="bpolls-item-width-wrapper">
					<div class="bpolls-item-width"></div>
					<div class="bpolls-check-radio-div"></div>
				</div>				
			</div>
			<a href="javascript:void(0);" class="bpolls-delete-user-option" data-activity-id="<?php echo esc_attr( $activity_id ); ?>" data-option="<?php echo esc_attr( $poll_key ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>"><i class="wb-icons wb-icon-x"></i></a>
		</div>
		<?php
		$add_poll_option = ob_get_clean();
		echo json_encode(
			array(
				'success'         => true,
				'add_poll_option' => $add_poll_option,
			)
		);
		wp_die();
	}

	/**
	 * Delete Activotity poll option when the user delete from fronted
	 *
	 * @since BP Polls 4.0.1
	 */
	public function bpolls_activity_delete_user_option() {
		check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
		$user_id     = get_current_user_id();
		$activity_id = isset( $_POST['activity_id'] ) ? sanitize_text_field( wp_unslash( $_POST['activity_id'] ) ) : '';
		$user_option = isset( $_POST['user_option'] ) ? sanitize_text_field( wp_unslash( $_POST['user_option'] ) ) : '';

		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
		$poll_key      = str_replace( '%', '', sanitize_title( $user_option ) );
		/* Delete Activity Poll Option */
		unset( $activity_meta['poll_option'][ $poll_key ] );

		/*Remove Unused "poll_optn_votes" Option */
		if ( isset( $activity_meta['poll_optn_votes'][ $poll_key ] ) ) {
			unset( $activity_meta['poll_optn_votes'][ $poll_key ] );
		}
		/*Remove Unused "poll_optn_votes" Option */
		if ( isset( $activity_meta['poll_optn_user_votes'][ $poll_key ] ) ) {
			unset( $activity_meta['poll_optn_user_votes'][ $poll_key ] );
		}
		$user_vote = array();
		foreach ( $activity_meta['poll_optn_user_votes'] as $user_key => $user_value ) {
			foreach ( $user_value as $user ) {
				$user_vote[] = $user;
			}
		}

		if ( ! empty( $user_vote ) ) {
			foreach ( $activity_meta['poll_users'] as $u_key => $user_value ) {
				if ( ! in_array( $user_value, $user_vote ) ) {
					unset( $activity_meta['poll_users'][ $u_key ] );
				}
			}
		}
		/* Count Total User votes*/
		$activity_meta['poll_total_votes'] = count( $activity_meta['poll_users'] );
		bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );

		$user_polls_option = get_user_meta( $user_id, 'bpolls_user_options', true );
		if ( empty( $user_polls_option ) ) {
			$user_polls_option = array();
		}
		unset( $user_polls_option[ 'activity-id-' . $activity_id . '-' . $poll_key ] );
		/* Update  Activity Poll Option in user meta*/
		update_user_meta( $user_id, 'bpolls_user_options', $user_polls_option );

		$updated_votes = $this->bpolls_ajax_calculate_votes( $activity_id );

		echo wp_json_encode( $updated_votes );
		wp_die();
	}

	public function bpolls_wp_footer() {

		if ( $this->bpolls_is_user_allowed_polls() && ( ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() ) || ( function_exists( 'bp_is_group_activity' ) && bp_is_group_activity() ) ) ) {
			$bpolls_settings   = get_site_option( 'bpolls_settings' );
			$polls_option_lmit = ( isset( $bpolls_settings['options_limit'] ) ) ? $bpolls_settings['options_limit'] : 5;
			?>
			<div class="bpolls-icon-dialog">
				<div class="bpolls-icon-dialog-container">
					<div class="bpolls-icon-dialog-header">
						<i class="fa fa-exclamation-triangle"></i>
					</div>
					<div class="bpolls-icon-dialog-msg">
						<div class="bpolls-icon-dialog-desc">
							<div class="bpsts-icon-dialog-title">
								<strong><?php esc_html_e( 'Oops!', 'buddypress-polls' ); ?></strong>
							</div>
							<div class="bpolls-icon-dialog-content">
								<?php echo sprintf( esc_html__( 'You are not allowed to enter more than ', 'buddypress-polls' ) . esc_attr( $polls_option_lmit ) . esc_html__( ' options.', 'buddypress-polls' ) ); ?>
							</div>
						</div>
					</div>
					<ul class="bpolls-icon-dialog-buttons">
						<li>
							<a class="bpolls-icon-dialog-cancel">
								<?php esc_html_e( 'Got it!', 'buddypress-polls' ); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<?php
		}
	}

	function wb_poll_locate_template( $file_name, $load = false ) {

		$template_name = 'template/' . $file_name;

		if ( file_exists( STYLESHEETPATH . $template_name ) ) {
			$template_part = STYLESHEETPATH . $template_name;
		} elseif ( file_exists( TEMPLATEPATH . $template_name ) ) {
			$template_part = TEMPLATEPATH . $template_name;
		} else {
			$template_part = BPOLLS_PLUGIN_PATH . 'template/' . $file_name;
		}
		if ( $template_part && $load ) {
			load_template( $template_part, false );
			return false;
		}

		return apply_filters( 'wb_poll_locate_template', $template_part, $file_name );
	}

	/**
	 * Filter the_content with the business template part.
	 *
	 * @since 1.0.0
	 */
	public function wb_poll_add_new_content( $content ) {
		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$poll_dashboard_page = isset( $option_value['poll_dashboard_page'] ) ? $option_value['poll_dashboard_page'] : '';
			if ( $poll_dashboard_page != '' && is_page( $poll_dashboard_page ) ) {
				// Modify the content as needed
				ob_start();
				$modified_content        = self::wb_poll_locate_template( 'poll-dashboard.php', true );
				$custom_template_content = ob_get_clean();
				return $custom_template_content;
			} else {
				remove_filter( 'the_content', 'wb_poll_locate_template', 10 );
			}
		}

		if ( ! empty( $option_value ) ) {
			$create_poll_page = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';
			if ( $create_poll_page != '' && is_page( $create_poll_page ) ) {
				ob_start();
				$modified_content        = self::wb_poll_locate_template( 'create-poll.php', true );
				$custom_template_content = ob_get_clean();
				return $custom_template_content;
			} else {
				remove_filter( 'the_content', 'wb_poll_locate_template', 10 );
			}
		}

		// For other pages, return the original content
		return $content;

	}

	/**
	 * Append poll with the poll post type description
	 *
	 * @param $content
	 *
	 * @return string
	 * @throws Exception
	 */
	function wbpoll_the_content( $content ) {
		if ( in_array( 'get_the_excerpt', $GLOBALS['wp_current_filter'] ) ) {
			return $content;
		}

		global $post;

		// for single or archive wbpoll where 'the_content' hook is available
		if ( isset( $post->post_type ) && ( $post->post_type == 'wbpoll' ) ) {
			$post_id  = intval( $post->ID );
			$content .= WBPollHelper::wbpoll_single_display( $post_id, 'content_hook', '', '', 0 );
		}

		return $content;

	}//end wbpoll_the_content()

	/**
	 * Auto integration for 'the_excerpt'
	 *
	 * @param $content
	 *
	 * @return string
	 * @throws Exception
	 */
	public function wbpoll_the_excerpt( $content ) {
		global $post;

		// for single or archive wbpoll where 'the_content' hook is available
		if ( isset( $post->post_type ) && ( $post->post_type == 'wbpoll' ) ) {
			$post_id  = intval( $post->ID );
			$content .= WBPollHelper::wbpoll_single_display( $post_id, 'content_hook', '', '', 0 );
		}

		return $content;
	}//end wbpoll_the_excerpt()

	/**
	 * ajax function for vote
	 */
	function wbpoll_user_vote() {
		// security check
		check_ajax_referer( 'wbpolluservote', 'nonce' );

		global $wpdb;
		$votes_name = WBPollHelper::wb_poll_table_name();

		$poll_result          = array();
		$poll_result['error'] = 0;

		// $setting_api = get_option('wbpoll_global_settings');

		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;

		$poll_id = intval( $_POST['poll_id'] );

		$user_answer_t = base64_decode( $_POST['user_answer'] );

		$user_answer_t = maybe_unserialize( $user_answer_t ); // why maybe
		parse_str( $user_answer_t, $user_answer );

		$user_answer_final = array();
		foreach ( $user_answer as $answer ) {
			$user_answer_final[] = $answer;
		}

		$user_answer_final = maybe_serialize( $user_answer_final );
		$poll_answers      = get_post_meta( $poll_id, '_wbpoll_answer', true );
		if ( isset( $poll_answers ) && ! empty( $poll_answers ) ) {
			$poll_ans_title = array();
			foreach ( $user_answer as $answer ) {
				$poll_ans_title[] = isset( $poll_answers[ $answer ] ) ? $poll_answers[ $answer ] : '';
			}
			//$poll_ans_id    = isset($user_answer['wbpoll_user_answer']) ? $user_answer['wbpoll_user_answer'] : "";
			//$poll_ans_title = isset($poll_answers[ $poll_ans_id ]) ? $poll_answers[ $poll_ans_id ] : "";
		}
		$chart_type = esc_attr( sanitize_text_field( $_POST['chart_type'] ) );
		$reference  = esc_attr( sanitize_text_field( $_POST['reference'] ) );

		$poll_info = get_post( $poll_id );

		if ( $user_id == 0 ) {
			$user_session = '';
			// $user_session   = $_COOKIE[ BPOLLS_COOKIE_NAME ]; //this is string
			$user_ip        = WBPollHelper::get_ipaddress();
			$this_user_role = array( 'guest' );

		} elseif ( is_user_logged_in() ) {
			$user_session = 'user-' . $user_id; // this is string
			$user_ip      = WBPollHelper::get_ipaddress();
			global $current_user;
			$this_user_role = $current_user->roles;
		}

		// poll informations from meta

		$poll_start_date = get_post_meta( $poll_id, '_wbpoll_start_date', true ); // poll start date
		$poll_end_date   = get_post_meta( $poll_id, '_wbpoll_end_date', true ); // poll end date
		$poll_user_roles = get_post_meta( $poll_id, '_wbpoll_user_roles', true ); // poll user roles
		if ( ! is_array( $poll_user_roles ) ) {
			$poll_user_roles = array();
		}

		$poll_never_expire              = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_never_expire',
				true
			)
		); // poll never epire
		$poll_show_result_before_expire = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_show_result_before_expire',
				true
			)
		); // poll never epire

		$poll_votes_per_session = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_vote_per_session',
				true
			)
		); // Votes per session
		$poll_result_chart_type = get_post_meta( $poll_id, '_wbpoll_result_chart_type', true ); // chart type

		$poll_is_voted = WBPollHelper::is_poll_voted( $poll_id );

		$poll_result_chart_type = get_post_meta( $poll_id, '_wbpoll_result_chart_type', true );
		$poll_result_chart_type = ( $chart_type != '' ) ? $chart_type : $poll_result_chart_type; // honor shortcode or widget  as user input

		// fallback as text if addon no installed
		$poll_result_chart_type = WBPollHelper::chart_type_fallback( $poll_result_chart_type ); // make sure that if chart type is from pro addon then it's installed

		$poll_answers = get_post_meta( $poll_id, '_wbpoll_answer', true );

		$poll_answers = is_array( $poll_answers ) ? $poll_answers : array();
		$poll_colors  = get_post_meta( $poll_id, '_wbpoll_answer_color', true );

		$log_method       = 'both';
		$current_datetime = current_datetime()->format( 'Y-m-d H:i:s' );

		$is_poll_expired = new DateTime( $poll_end_date ) < new DateTime( $current_datetime ); // check if poll expired from it's end data
		$is_poll_expired = ( $poll_never_expire == 1 ) ? false : $is_poll_expired; // override expired status based on the meta information

		$poll_allowed_user_group = $poll_user_roles;

		$allowed_user_groups = array_intersect( $poll_allowed_user_group, $this_user_role );

		if ( new DateTime( $poll_start_date ) > new DateTime( $current_datetime ) ) {
			$poll_result['error'] = 1;
			$poll_result['text']  = esc_html__( 'Sorry, poll didn\'t start yet.', 'buddypress-polls' );

			echo json_encode( $poll_result );
			die();
		}

		if ( $is_poll_expired ) {

			$poll_result['error'] = 1;
			$poll_result['text']  = esc_html__( 'Sorry, you can not vote. Poll has already expired.', 'buddypress-polls' );

			echo json_encode( $poll_result );
			die();

		}

		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$wppolls_who_can_vote = $option_value['wppolls_who_can_vote'];
		}
		if ( ! is_array( $wppolls_who_can_vote ) ) {
			$wppolls_who_can_vote = array();
		}

		if ( ! empty( $poll_user_roles ) ) {
			$allowed_user_group = array_intersect( $poll_user_roles, $this_user_role );
		} else {
			$allowed_user_group = array_intersect( $wppolls_who_can_vote, $this_user_role );
		}

		// check if the user has permission to vote
		if ( ( sizeof( $allowed_user_group ) ) < 1 ) {
			$poll_result['error'] = 1;
			$poll_result['text']  = esc_html__( 'Sorry, you are not allowed to vote.', 'buddypress-polls' );

			echo json_encode( $poll_result );
			die();
		}

		do_action( 'wbpoll_form_validation', $poll_result, $poll_id );

		$insertArray['poll_id']      = $poll_id;
		$insertArray['poll_title']   = $poll_info->post_title;
		$insertArray['user_name']    = ( $user_id == 0 ) ? 'guest' : $current_user->user_login;
		$insertArray['is_logged_in'] = ( $user_id == 0 ) ? 0 : 1;
		$insertArray['user_cookie']  = ( $user_id != 0 ) ? 'user-' . $user_id : $_COOKIE[ BPOLLS_COOKIE_NAME ];
		$insertArray['user_ip']      = WBPollHelper::get_ipaddress();
		$insertArray['user_id']      = $user_id;

		$insertArray['user_answer'] = $user_answer_final;

		$status                      = 1;
		$status                      = apply_filters( 'wbpoll_vote_status', $status, $poll_id );
		$insertArray['published']    = $status; // need to make this col as published 1 or 0, 2= spam
		$insertArray['answer_title'] = isset( $poll_ans_title ) ? maybe_serialize( $poll_ans_title ) : '';
		$insertArray['comment']      = '';
		$insertArray['guest_hash']   = '';
		$insertArray['guest_name']   = '';
		$insertArray['guest_email']  = '';
		$insertArray['created']      = time();
		// $insertArray['paused']            = 0;

		$insertArray = apply_filters( 'wbpoll_form_extra_process', $insertArray, $poll_id );

		$count = 0;

		// for logged in user ip or cookie or ip-cookie should not be used, those option should be used for guest user

		if ( $log_method == 'cookie' ) {

			$sql   = $wpdb->prepare(
				"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_cookie = %s",
				$insertArray['poll_id'],
				$user_id,
				$user_session
			);
			$count = $wpdb->get_var( $sql );

		} elseif ( $log_method == 'ip' ) {

			$sql   = $wpdb->prepare(
				"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s",
				$insertArray['poll_id'],
				$user_id,
				$user_ip
			);
			$count = $wpdb->get_var( $sql );

		} else {
			if ( $log_method == 'both' ) {

				// find cookie count
				$sql               = $wpdb->prepare(
					"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_cookie = %s",
					$insertArray['poll_id'],
					$user_id,
					$user_session
				);
				$vote_count_cookie = $wpdb->get_var( $sql );

				// find ip count
				$sql           = $wpdb->prepare(
					"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s",
					$insertArray['poll_id'],
					$user_id,
					$user_ip
				);
				$vote_count_ip = $wpdb->get_var( $sql );

				if ( $vote_count_cookie >= 1 || $vote_count_ip >= 1 ) {
					$count = 1;
				}
			}
		}

		$count = apply_filters( 'wbpoll_is_user_voted', $count );

		// check guest user if voted from same email before

		$poll_result['poll_id'] = $poll_id;

		$poll_result['chart_type'] = $poll_result_chart_type;

		// already voted
		if ( $count != 0 && $count >= $poll_votes_per_session ) {
			// already voted, just show the result

			$poll_result['error'] = 1;
			$poll_result['text']  = esc_html__( 'You already voted this poll !', 'buddypress-polls' );

			echo json_encode( $poll_result );
			die();

		} else {
			// user didn't vote and good to go

			// add the vote
			$vote_id = WBPollHelper::update_poll( $insertArray ); // let the user vote

			if ( $vote_id !== false ) {
				// poll vote action
				// update the post as at least on vote is done to restrict for sorting order and edit answer labels
				do_action( 'wbpoll_on_vote', $insertArray, $vote_id, $insertArray['published'] );

			} else {

				// at least we show some msg for such case.

				$poll_result['error'] = 1;
				$poll_result['text']  = esc_html__(
					'Sorry, something wrong while voting, please refresh this page',
					'buddypress-polls'
				);

				echo json_encode( $poll_result );
				die();
			}
		}

		// $poll_result['user_answer'] = $user_answer;
		$poll_result['user_answer'] = $user_answer_final;
		$poll_result['reference']   = $reference;
		$poll_result['colors']      = wp_json_encode( $poll_colors );
		$poll_result['answers']     = wp_json_encode( $poll_answers );

		$total_results = WBPollHelper::get_pollResult( $insertArray['poll_id'] );

		$total_votes = WBPollHelper::count_pollResult( $poll_id );

		$poll_result['total']       = $total_votes;
		$poll_result['show_result'] = ''; // todo: need to check if user allowed to view result with all condition

		$poll_answers_weight = array();

		foreach ( $total_results as $result ) {
			$user_ans = maybe_unserialize( $result['user_answer'] );
			if ( is_array( $user_ans ) ) {
				foreach ( $user_ans as $u_ans ) {
					$old_val                       = isset( $poll_answers_weight[ $u_ans ] ) ? intval( $poll_answers_weight[ $u_ans ] ) : 0;
					$poll_answers_weight[ $u_ans ] = ( $old_val + 1 );
				}
			} else {
				// backword compatible
				$user_ans                         = intval( $user_ans );
				$old_val                          = isset( $poll_answers_weight[ $user_ans ] ) ? intval( $poll_answers_weight[ $user_ans ] ) : 0;
				$poll_answers_weight[ $user_ans ] = ( $old_val + 1 );

			}
		}

		$poll_result['answers_weight'] = $poll_answers_weight;

		// ready mix :)
		$poll_weighted_labels = array();
		foreach ( $poll_answers as $index => $answer ) {
			$poll_weighted_labels[ $answer ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
		}
		$poll_result['weighted_label'] = $poll_weighted_labels;

		// this will help to show vote result easily
		update_post_meta(
			$poll_id,
			'_wbpoll_total_votes',
			absint( $total_votes )
		); // can help for showing most voted poll //meta added

		// create vote logs

		$insertArray_log = array(
			'poll_id'      => $poll_id,
			'user_name'    => ( $user_id == 0 ) ? 'guest' : $current_user->user_login,
			'is_logged_in' => ( $user_id == 0 ) ? 0 : 1,
			'user_ip'      => WBPollHelper::get_ipaddress(),
			'user_id'      => $user_id,
			'user_action'  => 'vote',
			'poll_status'  => 'accepted',
			'details'      => $user_answer_final,
			'created'      => time(),
			'useragent'    => WBPollHelper::get_useragent(),

		);

		$wpdb->insert( $wpdb->prefix . 'wppoll_log', $insertArray_log );
		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {

			$wppolls_show_result = isset( $option_value['wppolls_show_result'] ) ? $option_value['wppolls_show_result'] : '';
		}

		// we will only show result if permitted and for successful voting only

		// at least a successful vote happen
		// let's check if permission to see result >> as has vote capability to can see result
		// let's check if has permission to see before expire

		if ( $wppolls_show_result == 'yes' ) {
			 $poll_result['text'] = esc_html__( 'Thanks for voting!', 'buddypress-polls' );
			if ( $poll_show_result_before_expire == 0 ) {
				$poll_result['show_result'] = 1;
				$poll_result['html']        = WBPollHelper::show_single_poll_result( $poll_id, $reference, $chart_type );

			}
		} else {
			$poll_result['text'] = esc_html__( 'Thanks for the voting. Results are hidden by the admin for this poll', 'buddypress-polls' );
		}

		echo wp_json_encode( $poll_result );
		update_option( 'permalink_structure', '/%postname%/' );
		die();

	}//end wbpoll_user_vote()


	public function wbpoll_additional_field() {
		$post_id = $_POST['post_id']; //phpcs:ignore

		if ( ! empty( $_POST['_wbpoll_answer'] ) && isset( $_POST['_wbpoll_answer'][0] ) && ! empty( $_POST['_wbpoll_answer'][0] ) ) { //phpcs:ignore

			$old_ans = get_post_meta( $post_id, '_wbpoll_answer', true );
			$new_ans = $_POST['_wbpoll_answer']; //phpcs:ignore
			foreach ( $new_ans as $key => $value ) {
				if ( $value === '' ) {
					unset( $new_ans[ $key ] );
				}
			}
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer' );
			}
		}
		if ( ! empty( $_POST['_wbpoll_answer_extra'] ) && isset( $_POST['_wbpoll_answer_extra'][0] ) && ! empty( $_POST['_wbpoll_answer_extra'][0] ) ) { //phpcs:ignore
			$old_ans_extra = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			$new_ans_extra = $_POST['_wbpoll_answer_extra']; //phpcs:ignore
			$answers_extra = array_merge( $old_ans_extra, $new_ans_extra );

			if ( isset( $answers_extra ) ) {

				$extra = array();
				foreach ( $answers_extra as $key => $extra_type ) {
					$extra[]['type'] = 'default';
				}
				update_post_meta( $post_id, '_wbpoll_answer_extra', $extra );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer_extra' );
			}
		}

		$data = array(
			'success' => 'Added additional field',
			'post_id' => $post_id,
		);
		return rest_ensure_response( $data );

	}


	public function wbpoll_additional_field_image() {
		$post_id = $_POST['post_id']; //phpcs:ignore
		if ( ! empty( $_POST['_wbpoll_answer'] ) && isset( $_POST['_wbpoll_answer'][0] ) && ! empty( $_POST['_wbpoll_answer'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_answer', true );
			$new_ans = $_POST['_wbpoll_answer']; //phpcs:ignore

			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer' );
			}
		}
		if ( ! empty( $_POST['_wbpoll_answer_extra'] ) && isset( $_POST['_wbpoll_answer_extra'][0] ) && ! empty( $_POST['_wbpoll_answer_extra'][0] ) ) { //phpcs:ignore
			$old_ans_extra = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			$new_ans_extra = $_POST['_wbpoll_answer_extra']; //phpcs:ignore
			$answers_extra = array_merge( $old_ans_extra, $new_ans_extra );

			if ( isset( $answers_extra ) ) {

				$extra = array();
				foreach ( $answers_extra as $key => $extra_type ) {
					$extra[]['type'] = 'image';
				}
				update_post_meta( $post_id, '_wbpoll_answer_extra', $extra );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer_extra' );
			}
		}
		// Full size image answer

		if ( ! empty( $_POST['_wbpoll_full_size_image_answer'] ) && isset( $_POST['_wbpoll_full_size_image_answer'][0] ) && ! empty( $_POST['_wbpoll_full_size_image_answer'][0] ) ) { //phpcs:ignore
			$old_ans_image = get_post_meta( $post_id, '_wbpoll_full_size_image_answer', true );
			$new_ans_image = $_POST['_wbpoll_full_size_image_answer']; //phpcs:ignore
			$answers_image = array_merge( $old_ans_image, $new_ans_image );

			if ( isset( $answers_image ) ) {
				$titles = array();
				foreach ( $answers_image as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_full_size_image_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_full_size_image_answer' );
			}
		}

		$data = array(
			'success' => 'Added additional field',
			'post_id' => $post_id,
		);
		return rest_ensure_response( $data );

	}

	public function wbpoll_additional_field_video() {
		$post_id = $_POST['post_id']; //phpcs:ignore

		if ( ! empty( $_POST['_wbpoll_answer'] ) && isset( $_POST['_wbpoll_answer'][0] ) && ! empty( $_POST['_wbpoll_answer'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_answer', true );
			$new_ans = $_POST['_wbpoll_answer']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer' );
			}
		}
		if ( ! empty( $_POST['_wbpoll_answer_extra'] ) && isset( $_POST['_wbpoll_answer_extra'][0] ) && ! empty( $_POST['_wbpoll_answer_extra'][0] ) ) { //phpcs:ignore
			$old_ans_extra = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			$new_ans_extra = $_POST['_wbpoll_answer_extra']; //phpcs:ignore
			$answers_extra = array_merge( $old_ans_extra, $new_ans_extra );

			if ( isset( $answers_extra ) ) {

				$extra = array();
				foreach ( $answers_extra as $key => $extra_type ) {
					$extra[]['type'] = 'video';
				}
				update_post_meta( $post_id, '_wbpoll_answer_extra', $extra );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer_extra' );
			}
		}

		// Full size video  answer

		if ( ! empty( $_POST['_wbpoll_video_answer_url'] ) && isset( $_POST['_wbpoll_video_answer_url'][0] ) && ! empty( $_POST['_wbpoll_video_answer_url'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_video_answer_url', true );
			$new_ans = $_POST['_wbpoll_video_answer_url']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_video_answer_url', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_video_answer_url' );
			}
		}

		// video suggestion  answer

		if ( ! empty( $_POST['_wbpoll_video_import_info'] ) && isset( $_POST['_wbpoll_video_import_info'][0] ) && ! empty( $_POST['_wbpoll_video_import_info'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_video_import_info', true );
			$new_ans = $_POST['_wbpoll_video_import_info']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_video_import_info', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_video_import_info' );
			}
		}

		$data = array(
			'success' => 'Added additional field',
			'post_id' => $post_id,
		);
		return rest_ensure_response( $data );

	}

	public function wbpoll_additional_field_audio() {
		$post_id = $_POST['post_id']; //phpcs:ignore

		if ( ! empty( $_POST['_wbpoll_answer'] ) && isset( $_POST['_wbpoll_answer'][0] ) && ! empty( $_POST['_wbpoll_answer'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_answer', true );
			$new_ans = $_POST['_wbpoll_answer']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer' );
			}
		}
		if ( ! empty( $_POST['_wbpoll_answer_extra'] ) && isset( $_POST['_wbpoll_answer_extra'][0] ) && ! empty( $_POST['_wbpoll_answer_extra'][0] ) ) { //phpcs:ignore
			$old_ans_extra = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			$new_ans_extra = $_POST['_wbpoll_answer_extra']; //phpcs:ignore
			$answers_extra = array_merge( $old_ans_extra, $new_ans_extra );

			if ( isset( $answers_extra ) ) {

				$extra = array();
				foreach ( $answers_extra as $key => $extra_type ) {
					$extra[]['type'] = 'audio';
				}
				update_post_meta( $post_id, '_wbpoll_answer_extra', $extra );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer_extra' );
			}
		}

		// Full size audio  answer

		if ( ! empty( $_POST['_wbpoll_audio_answer_url'] ) && isset( $_POST['_wbpoll_audio_answer_url'][0] ) && ! empty( $_POST['_wbpoll_audio_answer_url'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_audio_answer_url', true );
			$new_ans = $_POST['_wbpoll_audio_answer_url']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_audio_answer_url', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_audio_answer_url' );
			}
		}

		// audio suggestion  answer

		if ( ! empty( $_POST['_wbpoll_audio_import_info'] ) && isset( $_POST['_wbpoll_audio_import_info'][0] ) && ! empty( $_POST['_wbpoll_audio_import_info'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_audio_import_info', true );
			$new_ans = $_POST['_wbpoll_audio_import_info']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_audio_import_info', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_audio_import_info' );
			}
		}

		$data = array(
			'success' => 'Added additional field',
			'post_id' => $post_id,
		);
		return rest_ensure_response( $data );

	}


	public function wbpoll_additional_field_html() {
		$post_id = $_POST['post_id']; //phpcs:ignore

		if ( ! empty( $_POST['_wbpoll_answer'] ) && isset( $_POST['_wbpoll_answer'][0] ) && ! empty( $_POST['_wbpoll_answer'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_answer', true );
			$new_ans = $_POST['_wbpoll_answer']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer' );
			}
		}
		if ( ! empty( $_POST['_wbpoll_answer_extra'] ) && isset( $_POST['_wbpoll_answer_extra'][0] ) && ! empty( $_POST['_wbpoll_answer_extra'][0] ) ) { //phpcs:ignore
			$old_ans_extra = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			$new_ans_extra = $_POST['_wbpoll_answer_extra']; //phpcs:ignore
			$answers_extra = array_merge( $old_ans_extra, $new_ans_extra );

			if ( isset( $answers_extra ) ) {

				$extra = array();
				foreach ( $answers_extra as $key => $extra_type ) {
					$extra[]['type'] = 'html';
				}
				update_post_meta( $post_id, '_wbpoll_answer_extra', $extra );

			} else {
				delete_post_meta( $post_id, '_wbpoll_answer_extra' );
			}
		}

		// Full size html  answer

		if ( ! empty( $_POST['_wbpoll_html_answer'] ) && isset( $_POST['_wbpoll_html_answer'][0] ) && ! empty( $_POST['_wbpoll_html_answer'][0] ) ) { //phpcs:ignore
			$old_ans = get_post_meta( $post_id, '_wbpoll_html_answer', true );
			$new_ans = $_POST['_wbpoll_html_answer']; //phpcs:ignore
			$answers = array_merge( $old_ans, $new_ans );

			if ( isset( $answers ) ) {
				$titles = array();
				foreach ( $answers as $index => $title ) {
					$titles[ $index ] = $title;
				}

				update_post_meta( $post_id, '_wbpoll_html_answer', $titles );

			} else {
				delete_post_meta( $post_id, '_wbpoll_html_answer' );
			}
		}

		$data = array(
			'success' => 'Added additional field',
			'post_id' => $post_id,
		);
		return rest_ensure_response( $data );

	}

	/**
	 * Load color palette
	 *
	 * @since 4.3.0
	 * @return string
	 */
	public function wbpolls_load_color_palette() {

		$colors = array(
			'primary_color' => '--wbpoll-global-primary-color',
		);

		// Customizer colors.
		$color_settings = get_option( 'wbpolls_settings' );

		if ( ! empty( $color_settings ) ) {
			$wbpolls_background_color = isset( $color_settings['wbpolls_background_color'] ) ? $color_settings['wbpolls_background_color'] : '';
		}

		$primary_color = ( isset( $wbpolls_background_color['primary_color'] ) ) ? $wbpolls_background_color['primary_color'] : '#4caf50';

		$admin_colors = array(
			'--wbpoll-global-primary-color' => $wbpolls_background_color,
		);

		$fallback_colors = array(
			'primary_color' => '#4caf50',
		);

		$color_string = '';
		foreach ( $colors as $key => $property ) {
			$fallback_color = isset( $fallback_colors[ $key ] ) ? $fallback_colors[ $key ] : '';
			$color          = get_option( $key, $fallback_color );

			if ( isset( $admin_colors[ $property ] ) ) {
				$color = $admin_colors[ $property ];
			}

			if ( $color ) {
				$color_string .= $property . ':' . $color . ';';
			}
		}

		return ':root{' . $color_string . '}';
	}


	/**
	 * Inits all shortcodes
	 */
	public function init_shortcodes() {

		add_shortcode( 'wbpoll', array( $this, 'wbpoll_shortcode' ) ); // single poll shortcode

	} //end init_shortcodes()


	public function wbpoll_shortcode( $atts, $content = null ) {
		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		$global_result_chart_type = 'text';
		$global_answer_grid_list  = 1; // 0 = list 1 = grid

		$options = shortcode_atts(
			array(
				'id'          => '',
				'reference'   => 'shortcode',
				'description' => '', // show poll description in shortcode
				'chart_type'  => $global_result_chart_type,
				'grid'        => $global_answer_grid_list,
			),
			$atts,
			'wbpoll'
		);

		$reference   = esc_attr( $options['reference'] );
		$chart_type  = esc_attr( $options['chart_type'] );
		$description = esc_attr( $options['description'] );
		$grid        = intval( $options['grid'] );

		$poll_ids = array_map( 'trim', explode( ',', $options['id'] ) );

		$output = '';
		if ( is_array( $poll_ids ) && sizeof( $poll_ids ) > 0 ) {

			foreach ( $poll_ids as $poll_id ) {

				$output .= wbpollHelper::wbpoll_single_display(
					$poll_id,
					$reference,
					$chart_type,
					$grid,
					$description
				);
			}
		}

		return $output;
	} //end wbpoll_shortcode()


	/**
	 * The function will return the archive page template.
	 *
	 * @param string $archive_template                  Archive page template path.
	 * @since    4.3.2
	 */
	public function wbpoll_archive_template( $archive_template, $type, $templates ) {
		global $wpdb, $wp_query, $post;

		if ( is_archive() && ( get_post_type() === 'wbpoll' || $wp_query->query_vars['post_type'] === 'wbpoll' ) ) {

			$template = '/buddypress-polls/archive-' . $wp_query->query_vars['post_type'] . '.php';

			if ( file_exists( STYLESHEETPATH . $template ) ) {

				$archive_template = STYLESHEETPATH . $template;

			} elseif ( file_exists( TEMPLATEPATH . $template ) ) {

				$archive_template = TEMPLATEPATH . $template;

			} else {

				$archive_template = BPOLLS_PLUGIN_PATH . 'template/archive-wbpoll.php';

			}
		}
		return $archive_template;
	}

	/**
	 * The function will return the single business template.
	 *
	 * @param string $archive_template                  single page template path.
	 * @since    1.0.0
	 */
	public function wbpoll_profile_single_template( $single_template, $type, $templates ) {
		global $wpdb, $wp_query, $post;

		if ( is_single() && ( get_post_type() === 'wbpoll' || $wp_query->query_vars['post_type'] === 'wbpoll' ) ) {

			$template = '/buddypress-polls/single-' . $wp_query->query_vars['post_type'] . '.php';

			if ( file_exists( STYLESHEETPATH . $template ) ) {

				$single_template = STYLESHEETPATH . $template;

			} elseif ( file_exists( TEMPLATEPATH . $template ) ) {

				$single_template = TEMPLATEPATH . $template;

			} else {

				$single_template = BPOLLS_PLUGIN_PATH . 'template/single-wbpoll.php';

			}
		}
		return $single_template;
	}

	public function prepareWBPoll() {
		// Check whether is an archive page, search or singular.

		if ( is_single() || is_archive() || is_search() ) :
			// Get current post type.
			$currentPostType = get_post_type();

			// Check current post type is poll
			if ( $currentPostType === 'wbpoll' ) :

				// We need to take care of the output when It's embedded
				if ( function_exists( 'is_embed' ) && is_embed() ) :
					add_action( 'embed_content', array( $this, 'showEmbededPoll' ), 99 );
					remove_all_filters( 'get_the_excerpt' );
					add_filter( 'the_excerpt_embed', '__return_empty_string' );
					add_filter( 'embed_site_title_html', '__return_empty_string' );
					remove_all_actions( 'embed_content_meta' );
				endif;
			endif;
		endif;
	}

	function showEmbededPoll() {
		the_content();
		$post_id = intval( get_the_ID() );
		echo WBPollHelper::wbpoll_single_display( $post_id, 'content_hook', '', '', 0 );//phpcs:ignore
	}

	/**
	 * Allwed Polls activity to serch in BuddyBoss Search
	 *
	 * @param array $where_conditions
	 * @param string $search_term
	 * @return array $where_conditions
	 */
	public function wbpoll_buddyboss_polls_search( $where_conditions, $search_term ) {

		if ( str_contains( $where_conditions[1], 'activity_update' ) ) {
			$where               = str_replace( "AND a.type = 'activity_update'", "AND a.type = 'activity_update' OR a.type = 'activity_poll'", $where_conditions[1] );
			$where_conditions[1] = $where;
		}

		return $where_conditions;

	}


}
