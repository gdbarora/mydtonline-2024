<?php
/**
 * The helper functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    wbpoll
 * @subpackage wbpoll/includes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Helper functionality of the plugin.
 *
 * lots of micro methods that help get set
 *
 * @package    wbpoll
 * @subpackage wbpoll/includes
 * @author     codeboxr <info@codeboxr.com>
 */
class WBPollHelper {

	/**
	 * initialize cookie
	 */
	public static function init_cookie() {

		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;

		if ( ! is_admin() ) {
			if ( is_user_logged_in() ) {

				$cookie_value = 'user-' . $user_id;

			} else {

				$cookie_value = 'guest-' . rand( BPOLLS_RAND_MIN, BPOLLS_RAND_MAX );
			}

			if ( ! isset( $_COOKIE[ BPOLLS_COOKIE_NAME ] ) && empty( $_COOKIE[ BPOLLS_COOKIE_NAME ] ) ) {

				setcookie(
					BPOLLS_COOKIE_NAME,
					$cookie_value,
					BPOLLS_COOKIE_EXPIRATION_14DAYS,
					SITECOOKIEPATH,
					COOKIE_DOMAIN
				);
				$_COOKIE[ BPOLLS_COOKIE_NAME ] = $cookie_value;

			} elseif ( isset( $_COOKIE[ BPOLLS_COOKIE_NAME ] ) ) {

				if ( substr( $_COOKIE[ BPOLLS_COOKIE_NAME ], 0, 5 ) != 'guest' ) {
					setcookie(
						BPOLLS_COOKIE_NAME,
						$cookie_value,
						BPOLLS_COOKIE_EXPIRATION_14DAYS,
						SITECOOKIEPATH,
						COOKIE_DOMAIN
					);
					$_COOKIE[ BPOLLS_COOKIE_NAME ] = $cookie_value;
				}
			}
		}

	}

	/**
	 * Get IP address
	 *
	 * @return string|void
	 */
	public static function get_ipaddress() {

		if ( empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

			$ip_address = $_SERVER['REMOTE_ADDR'];
		} else {

			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if ( strpos( $ip_address, ',' ) !== false ) {

			$ip_address = explode( ',', $ip_address );
			$ip_address = $ip_address[0];
		}

		return esc_attr( $ip_address );
	}

	/**
	 * Get useragent address
	 *
	 * @return string|void
	 */
	public static function get_useragent() {

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		return esc_html( $user_agent, 'buddypress-polls' );
	}

	/**
	 * Create custom post type poll
	 */
	public static function create_wbpoll_post_type() {

		$args = array(
			'labels'          => array(
				'name'               => esc_html__( 'WB Polls', 'buddypress-polls' ),
				'singular_name'      => esc_html__( 'WB Poll', 'buddypress-polls' ),
				'add_new_item'       => esc_html__( 'Add New Poll', 'buddypress-polls' ),
				'edit_item'          => esc_html__( 'Edit Poll', 'buddypress-polls' ),
				'new_item'           => esc_html__( 'New Poll', 'buddypress-polls' ),
				'view_item'          => esc_html__( 'View Poll', 'buddypress-polls' ),
				'search_items'       => esc_html__( 'Search Poll', 'buddypress-polls' ),
				'not_found'          => esc_html__( 'No Poll found', 'buddypress-polls' ),
				'not_found_in_trash' => esc_html__( 'No Poll found in trash', 'buddypress-polls' ),
			),

			'menu_icon'       => 'dashicons-chart-bar', // 16px16
			'public'          => true,
			// 'has_archive'     => true,
			'has_archive'     => 'wbpoll',
			'capability_type' => 'page',
			'supports'        => apply_filters(
				'wbpoll_post_type_supports',
				array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'comments',
				)
			),
			'rewrite'         => array( 'slug' => 'poll' ),
		);

		register_post_type( 'wbpoll', apply_filters( 'wbpoll_post_type_args', $args ) );

	}//end create_wbpoll_post_type()




	/**
	 * create table with plugin activate hook
	 */

	public static function install_table() {
		global $wpdb;
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . '/wp-admin/includes/upgrade.php';

		$votes_name = self::wb_poll_table_name();

		$sql = "CREATE TABLE $votes_name (
                  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                  poll_id int(13) NOT NULL,
                  poll_title text NOT NULL,
                  user_name varchar(255) NULL,
                  is_logged_in tinyint(1) NULL,
                  user_cookie varchar(1000) NULL,
                  user_ip varchar(45) NOT NULL,
                  user_id bigint(20) unsigned NULL,
                  user_answer text NOT NULL,
				  answer_title text NOT NULL,
                  published tinyint(3) NOT NULL DEFAULT '1',
                  comment LONGTEXT NOT NULL,
                  guest_hash VARCHAR(32) NOT NULL,
                  guest_name varchar(100) DEFAULT NULL,
                  guest_email varchar(100) DEFAULT NULL,
                  created int(20) NOT NULL,
                  PRIMARY KEY  (id)
            ) $charset_collate;";
		dbDelta( $sql );

		$votes_name = $wpdb->prefix . 'wppoll_log';

		$sql = "CREATE TABLE $votes_name (
                  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                  poll_id int(13) NOT NULL,
                  user_name varchar(255) NOT NULL,
                  is_logged_in tinyint(1) NOT NULL,
                  user_ip varchar(45) NOT NULL,
				  useragent text NOT NULL,
                  user_id bigint(20) unsigned NOT NULL,
                  user_action text NOT NULL,
				  poll_status text NOT NULL,
                  details  text NOT NULL,
                  created int(20) NOT NULL,
                  PRIMARY KEY  (id)
            ) $charset_collate;";
		dbDelta( $sql );

	}

	/**
	 * will call this later when plugin uninstalled
	 * in can also be written in uninstall.php file
	 */
	public static function delete_tables() {
		global $wpdb;
		$votes_name[] = self::wb_poll_table_name();
		$sql          = 'DROP TABLE IF EXISTS ' . implode( ', ', $votes_name );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		$votes_name[] = $wpdb->prefix . 'wppoll_log';
		$sql          = 'DROP TABLE IF EXISTS ' . implode( ', ', $votes_name );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Insert user vote
	 *
	 * @param  array $user_vote
	 *
	 * @return bool | vote id
	 */
	public static function update_poll( $user_vote ) {
		global $wpdb;
		if ( ! empty( $user_vote ) ) {
			$votes_table = self::wb_poll_table_name();

			$success = $wpdb->insert(
				$votes_table,
				$user_vote,
				array(
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
				)
			);
			return ( $success ) ? $wpdb->insert_id : false;
		}

		return false;
	}

	/**
	 * WP Poll vote table name
	 *
	 * @return string
	 */
	public static function wb_poll_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wbpoll_votes';
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public static function check_value_type( $string ) {
		$t   = gettype( $string );
		$ret = '';

		switch ( $t ) {
			case 'string':
				$ret = '\'%s\'';
				break;

			case 'integer':
				// $ret = '\'%d\'';
				$ret = '%d';
				break;
		}

		return $ret;
	}

	/**
	 * Returns all votes for any poll
	 *
	 * @param  int  $poll_id  wbpoll type post id
	 * @param  bool $is_object  array or object return type
	 *
	 * @return mixed
	 */
	public static function get_pollResult( $poll_id, $is_object = false ) {
		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql     = $wpdb->prepare( "SELECT * FROM $votes_name WHERE poll_id=%d AND published = 1", intval( $poll_id ) );
		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results;
	}//end get_pollResult()

	public static function count_pollResult( $poll_id, $is_object = false ) {
		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql     = $wpdb->prepare( "SELECT * FROM $votes_name WHERE poll_id=%d AND published = 1", intval( $poll_id ) );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		$total   = 0;
		foreach ( $results as $res ) {
			$total += count( maybe_unserialize( $res['user_answer'] ) );
		}
		return $total;
	}

	/**
	 * Is poll voted or not by vote count (not taking publish status into account)
	 *
	 * @param $poll_id
	 *
	 * @return int
	 */
	public static function is_poll_voted( $poll_id ) {
		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql         = $wpdb->prepare(
			"SELECT COUNT(*) AS total_count FROM $votes_name WHERE poll_id=%d",
			intval( $poll_id )
		);
		$total_count = intval( $wpdb->get_var( $sql ) );

		return ( $total_count > 0 ) ? 1 : 0;
	}//end is_poll_voted()

	/**
	 * Returns single vote result by id
	 *
	 * @param  int  $vote  single vote id
	 * @param  bool $is_object  array or object return type
	 *
	 * @return mixed
	 */
	public static function get_voteResult( $vote_id, $is_object = false ) {
		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql     = $wpdb->prepare( "SELECT * FROM $votes_name WHERE id=%d", $vote_id );
		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results;
	}//end get_voteResult()

	/**
	 * @param $array
	 *
	 * @return array
	 */
	public static function check_array_element_value_type( $array ) {
		$ret = array();

		if ( ! empty( $array ) ) {
			foreach ( $array as $val ) {
				$ret[] = self::check_value_type( $val );
			}
		}

		return $ret;
	} //end of function check_array_element_value_type

	/**
	 * Defination of all Poll Display/Chart Types
	 *
	 * @return array
	 */
	public static function wbpoll_display_options() {
		$methods = array();

		return apply_filters( 'wbpoll_display_options', $methods );
	}

	public static function wbpoll_display_options_backend() {
		$methods = array();

		return apply_filters( 'wbpoll_display_options_backend', $methods );
	}

	public static function wbpoll_display_options_widget_result() {
		$methods = array();

		return apply_filters( 'wbpoll_display_options_widget_result', $methods );
	}
	/**
	 * Return poll display option as associative array
	 *
	 * @param  array $methods
	 *
	 * @return array
	 */
	public static function wbpoll_display_options_linear( $methods ) {

		$linear_methods = array();

		foreach ( $methods as $key => $val ) {
			$linear_methods[ $key ] = $val['title'];
		}

		return $linear_methods;
	}

	public static function getVoteCountByStatus( $poll_id = 0 ) {

		global $wpdb;

		$votes_name = self::wb_poll_table_name();

		$where_sql = '';
		if ( $poll_id != 0 ) {
			$where_sql .= $wpdb->prepare( 'poll_id=%d', $poll_id );
		}

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}

		$sql_select = "SELECT published, COUNT(*) as vote_counts FROM $votes_name  WHERE   $where_sql GROUP BY published";

		$results = $wpdb->get_results( "$sql_select", 'ARRAY_A' );

		$total = 0;
		$data  = array(
			'0'     => 0,
			'1'     => 0,
			'2'     => 0,
			'3'     => 0,
			'total' => $total,
		);

		if ( $results != null ) {
			foreach ( $results as $result ) {
				$total                       += intval( $result['vote_counts'] );
				$data[ $result['published'] ] = $result['vote_counts'];
			}
			$data['total'] = $total;
		}

		return $data;

	}

	/**
	 * Filter the format of the sending mail
	 *
	 * @param  type $content_type
	 *
	 * @return string
	 */
	public static function wbpoll_mail_content_type( $content_type = 'text/plain' ) {
		if ( $content_type == 'html' ) {
			return 'text/html';
		} elseif ( $content_type == 'multipart' ) {
			return 'multipart/mixed';
		} else {
			return 'text/plain';
		}
	}

	/**
	 * Char Length check  thinking utf8 in mind
	 *
	 * @param $text
	 *
	 * @return int
	 */
	public static function utf8_compatible_length_check( $text ) {
		if ( seems_utf8( $text ) ) {
			$length = mb_strlen( $text );
		} else {
			$length = strlen( $text );
		}

		return $length;
	}

	/**
	 * Returns poll possible status as array, keys are value of status
	 *
	 * @return array
	 */
	public static function wbpoll_status_by_value() {
		$states = array(
			'0' => esc_html__( 'Unapproved', 'buddypress-polls' ),
			'1' => esc_html__( 'Approved', 'buddypress-polls' ),
			'2' => esc_html__( 'Spam', 'buddypress-polls' ),
			'3' => esc_html__( 'Unverified', 'buddypress-polls' ),
		);

		return apply_filters( 'wbpoll_status_by_value', $states );
	}

	/**
	 * Returns poll possible status as array, keys are slug of status
	 *
	 * @return array
	 */
	public static function wbpoll_status_by_slug() {
		$states = array(
			'unapprove'  => esc_html__( 'Unapproved', 'buddypress-polls' ),
			'approve'    => esc_html__( 'Approved', 'buddypress-polls' ),
			'spam'       => esc_html__( 'Spam', 'buddypress-polls' ),
			'unverified' => esc_html__( 'Unverified', 'buddypress-polls' ),
		);

		return apply_filters( 'wbpoll_status_by_value', $states );
	}

	/**
	 * Returns poll possible status as array, keys are value of status and values are slug
	 *
	 * @return array
	 */
	public static function wbpoll_status_by_value_with_slug() {
		$states = array(
			'0' => 'unapprove',
			'1' => 'approve',
			'2' => 'spam',
			'3' => 'unverified',
		);

		return apply_filters( 'wbpoll_status_by_value_with_slug', $states );
	}

	/**
	 * Get the user roles for voting purpose
	 *
	 * @param  string $useCase
	 *
	 * @return array
	 */
	public static function user_roles( $plain = true, $include_guest = false, $ignore = array() ) {
		global $wp_roles;

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . '/wp-admin/includes/user.php';

		}

		$userRoles = array();
		if ( $plain ) {
			foreach ( get_editable_roles() as $role => $roleInfo ) {
				if ( in_array( $role, $ignore ) ) {
					continue;
				}
				$userRoles[ $role ] = $roleInfo['name'];
			}
			if ( $include_guest ) {
				$userRoles['guest'] = esc_html__( 'Guest', 'buddypress-polls' );
			}
		} else {
			// optgroup.
			$userRoles_r = array();
			foreach ( get_editable_roles() as $role => $roleInfo ) {
				if ( in_array( $role, $ignore ) ) {
					continue;
				}
				$userRoles_r[ $role ] = $roleInfo['name'];
			}

			$userRoles = array(
				'Registered' => $userRoles_r,
			);

			if ( $include_guest ) {
				$userRoles['Anonymous'] = array(
					'guest' => esc_html__( 'Guest', 'buddypress-polls' ),
				);
			}
		}

		return apply_filters( 'wbpoll_userroles', $userRoles, $plain, $include_guest );
	}

	/**
	 * Get all  core tables list
	 */
	public static function getAllDBTablesList() {
		global $wpdb;

		$table_names                 = array();
		$table_names['wbpoll_votes'] = self::wb_poll_table_name();

		return apply_filters( 'wbpoll_table_list', $table_names );
	}

	/**
	 * List all global option name with prefix wbpoll_
	 */
	public static function getAllOptionNames() {
		global $wpdb;

		$prefix       = 'wbpoll_';
		$option_names = $wpdb->get_results(
			"SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'",
			ARRAY_A
		);

		return apply_filters( 'wbpoll_option_names', $option_names );
	}

	/**
	 * (Recommended not to use)Setup a post object and store the original loop item so we can reset it later
	 *
	 * @param  obj $post_to_setup  The post that we want to use from our custom loop
	 */
	public static function setup_admin_postdata( $post_to_setup ) {

		// only on the admin side
		if ( is_admin() ) {

			// get the post for both setup_postdata() and to be cached
			global $post;

			// only cache $post the first time through the loop
			if ( ! isset( $GLOBALS['post_cache'] ) ) {
				$GLOBALS['post_cache'] = $post;
			}

			// setup the post data as usual
			$post = $post_to_setup;
			setup_postdata( $post );
		} else {
			setup_postdata( $post_to_setup );
		}
	}//end setup_admin_postdata()


	/**
	 * (Recommended not to use)Reset $post back to the original item
	 */
	public static function wp_reset_admin_postdata() {

		// only on the admin and if post_cache is set
		if ( is_admin() && ! empty( $GLOBALS['post_cache'] ) ) {

			// globalize post as usual
			global $post;

			// set $post back to the cached version and set it up
			$post = $GLOBALS['post_cache'];
			setup_postdata( $post );

			// cleanup
			unset( $GLOBALS['post_cache'] );
		} else {
			wp_reset_postdata();
		}
	}//end wp_reset_admin_postdata()

	/**
	 * List polls
	 *
	 * @param  int    $user_id
	 * @param  int    $per_page
	 * @param  int    $page_number
	 * @param  string $chart_type
	 * @param  string $answer_grid_list
	 * @param  string $description
	 * @param  string $reference
	 *
	 * @return array
	 * @throws Exception
	 */
	public static function poll_list(
		$user_id = 0,
		$per_page = 10,
		$page_number = 1,
		$chart_type = '',
		$answer_grid_list = '',
		$description = '',
		$reference = 'shortcode'
	) {

		global $post;
		$output = array();

		$args = array(
			'post_type'      => 'wbpoll',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page_number,
		);

		if ( intval( $user_id ) > 0 ) {
			$args['author'] = $user_id;
		}

		$content = '';

		$posts_array = new WP_Query( $args );

		$total_count = intval( $posts_array->found_posts );

		if ( $posts_array->have_posts() ) {
			$output['found']         = 1;
			$output['found_posts']   = $total_count;
			$output['max_num_pages'] = ceil( $total_count / $per_page );

			// foreach ( $posts_array as $post ) : setup_postdata( $post );
			while ( $posts_array->have_posts() ) :
				$posts_array->the_post();
				$poll_id = get_the_ID();

				$content .= self::wbpoll_single_display(
					$poll_id,
					$reference,
					$chart_type,
					$answer_grid_list,
					$description
				);
				// endforeach;
			endwhile;
			wp_reset_postdata();

		} else {
			$output['found'] = 0;
		}

		$output['content'] = $content;

		return $output;
	}//end poll_list()

	/**
	 * Shows a single poll
	 *
	 * @param  int    $post_id
	 * @param  string $reference
	 * @param  string $result_chart_type
	 * @param  string $grid
	 * @param  int    $description
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function wbpoll_single_display( $post_id = 0, $reference = 'shortcode', $result_chart_type = '', $grid = '', $description = '' ) {
		// if poll id
		if ( intval( $post_id ) == 0 ) {
			return '';
		}

		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$wppolls_who_can_vote = $option_value['wppolls_who_can_vote'];
		}
		global $wpdb;

		// $setting_api  = $settings = new WBPoll_Settings();
		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;
		$user_ip      = self::get_ipaddress();
		$poll_output  = '';

		$allow_guest_sign = 'on';

		// todo: need to get it from single poll if we introduce this inside poll setting.
		$grid = 0;

		$grid_class = ( $grid != 0 ) ? 'wbpoll-form-insidewrap-grid' : '';

		if ( $user_id == 0 ) {

			// $user_session = $_COOKIE[ BPOLLS_COOKIE_NAME ]; //this is string.
			$user_session = '';
		} elseif ( is_user_logged_in() ) {
			$user_session = 'user-' . $user_id; // this is string.
		}

		// $setting_api = get_option('wbpoll_global_settings');
		$votes_name = self::wb_poll_table_name();

		// poll informations from meta.
		$poll_start_date = get_post_meta( $post_id, '_wbpoll_start_date', true ); // poll start date.
		$poll_end_date   = get_post_meta( $post_id, '_wbpoll_end_date', true ); // poll end date.
		$poll_user_roles = get_post_meta( $post_id, '_wbpoll_user_roles', true ); // poll user roles.
		if ( ! is_array( $poll_user_roles ) ) {
			$poll_user_roles = array();
		}

		$show_description = intval(
			get_post_meta(
				$post_id,
				'_wbpoll_content',
				true
			)
		); // show poll description or not.

		$poll_never_expire              = intval(
			get_post_meta(
				$post_id,
				'_wbpoll_never_expire',
				true
			)
		); // poll never epire.
		$poll_show_result_before_expire = intval(
			get_post_meta(
				$post_id,
				'_wbpoll_show_result_before_expire',
				true
			)
		); // poll never epire.

		$poll_votes_per_session = intval(
			get_post_meta(
				$post_id,
				'_wbpoll_vote_per_session',
				true
			)
		); // Votes per session.

		$poll_result_chart_type = get_post_meta( $post_id, '_wbpoll_result_chart_type', true ); // chart type.
		$poll_is_voted          = self::is_poll_voted( $post_id );

		$poll_multivote = intval( get_post_meta( $post_id, '_wbpoll_multivote', true ) ); // at least a single vote.

		$vote_input_type = ( $poll_multivote ) ? 'checkbox' : 'radio';

		$result_chart_type = ( $result_chart_type != '' ) ? $result_chart_type : $poll_result_chart_type;

		$show_description = ( $show_description != '' ) ? intval( $show_description ) : $show_description;

		// fallback as text if addon no installed.
		$result_chart_type = self::chart_type_fallback( $result_chart_type ); // make sure that if chart type is from pro addon then it's installed.

		$poll_answers = get_post_meta( $post_id, '_wbpoll_answer', true );

		$poll_answers = is_array( $poll_answers ) ? $poll_answers : array();
		$poll_colors  = get_post_meta( $post_id, '_wbpoll_answer_color', true );

		$log_method       = 'both';
		$current_datetime = current_datetime()->format( 'Y-m-d H:i:s' );

		$is_poll_expired = ( new DateTime( $poll_end_date ) < new DateTime( $current_datetime ) ) ? true : false; // check if poll expired from it's end data.
		$is_poll_expired = ( $poll_never_expire == 1 ) ? false : $is_poll_expired; // override expired status based on the meta information.

		$poll_allowed_user_group = $poll_user_roles;

		$cb_question_list_to_find_ans = array();
		foreach ( $poll_answers as $poll_answer ) {
			array_push( $cb_question_list_to_find_ans, $poll_answer );
		}

		$nonce = wp_create_nonce( 'wbpolluservote' );
		ob_start();
		?>

		<div class="wbpoll_wrapper wbpoll_wrapper-<?php echo esc_attr( $post_id ); ?> wbpoll_wrapper-<?php echo esc_attr( $reference ); ?>" data-reference ="<?php echo esc_attr( $reference ); ?>" > <?php // check if the poll started still. ?>

			<?php if ( $reference != 'content_hook' ) { ?>
				<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
				<?php
			}

			if ( $reference != 'content_hook' ) {
				// if enabled from shortcode and enabled from post meta field.
				if ( intval( $show_description ) == 1 || $description == 'true' ) {
					$poll_conobj  = get_post( $post_id );
					$poll_content = '';
					if ( is_object( $poll_conobj ) ) {
						$poll_content = $poll_conobj->post_content;
						$poll_content = strip_shortcodes( $poll_content );
						$poll_content = wpautop( $poll_content );
						$poll_content = convert_smilies( $poll_content );
						$poll_content = str_replace( ']]>', ']]&gt;', $poll_content );
					}

					if ( has_post_thumbnail( $post_id ) ) {
						?>
						<figure class="post-thumbnail"><?php echo get_the_post_thumbnail( $post_id, 'large' ); ?></figure>
					<?php } ?>

					<div class="wbpoll-description">
						<?php echo apply_filters( 'wbpoll_description', $poll_content, $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>				
					<?php
				}
			}

			if ( new DateTime( $poll_start_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {

				$poll_is_voted_by_user = 0;
				if ( $log_method == 'cookie' ) {

					$sql                   = $wpdb->prepare(
						"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_cookie = %s",
						$post_id,
						$user_id,
						$user_session
					);
					$poll_is_voted_by_user = $wpdb->get_var( $sql );

				} elseif ( $log_method == 'ip' ) {

					$sql                   = $wpdb->prepare(
						"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s",
						$post_id,
						$user_id,
						$user_ip
					);
					$poll_is_voted_by_user = $wpdb->get_var( $sql );

				} else {
					if ( $log_method == 'both' ) {

						$sql               = $wpdb->prepare(
							"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_cookie = %s",
							$post_id,
							$user_id,
							$user_session
						);
						$vote_count_cookie = $wpdb->get_var( $sql );

						$sql           = $wpdb->prepare(
							"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s",
							$post_id,
							$user_id,
							$user_ip
						);
						$vote_count_ip = $wpdb->get_var( $sql );

						if ( $vote_count_cookie >= 1 || $vote_count_ip >= 1 ) {
							$poll_is_voted_by_user = 1;
						}
					}
				}

				$poll_is_voted_by_user = apply_filters( 'wbpoll_is_user_voted', $poll_is_voted_by_user );
				if ( $is_poll_expired ) { // if poll has expired.

					$sql           = $wpdb->prepare(
						"SELECT ur.id AS answer FROM $votes_name ur WHERE  ur.poll_id=%d  ",
						$post_id
					);
					$cb_has_answer = $wpdb->get_var( $sql );

					if ( $cb_has_answer != null ) {

						if ( $poll_show_result_before_expire == 1 ) {
							echo self::show_single_poll_result( $post_id, $reference, $result_chart_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}

					$sql             = $wpdb->prepare(
						"SELECT ur.user_answer AS answer FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s AND ur.user_cookie = %s ",
						$post_id,
						$user_id,
						$user_ip,
						$user_session
					);
					$answers_by_user = $wpdb->get_var( $sql );

					$answers_by_user_html = '';
					if ( $answers_by_user !== null ) {
						$answers_by_user = maybe_unserialize( $answers_by_user );
						if ( is_array( $answers_by_user ) ) {
							$user_answers_textual = array();
							foreach ( $answers_by_user as $uchoice ) {
								$user_answers_textual[] = isset( $poll_answers[ $uchoice ] ) ? $poll_answers[ $uchoice ] : esc_html__(
									'Unknown or answer deleted',
									'buddypress-polls'
								);
							}

							$answers_by_user_html = implode( ', ', $user_answers_textual );
						} else {
							$answers_by_user      = intval( $answers_by_user );
							$answers_by_user_html = $poll_answers[ $answers_by_user ];

						}

						if ( $answers_by_user_html != '' ) {
							?>
						<p class="wbpoll-voted-info55 wbpoll-alert wbpoll-voted-info wbpoll-voted-info-<?php echo esc_attr( $post_id ); ?>">
								<?php /* translators: %s: */ ?>
								<?php echo sprintf( __( 'The Poll is out of date. You have already voted for <strong>"%s"</strong>', 'buddypress-polls' ), $answers_by_user_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</p>
						<?php } else { ?>

						<p class="wbpoll-voted-info55 wbpoll-alert wbpoll-voted-info wbpoll-voted-info-<?php echo esc_attr( $post_id ); ?>">
							<?php /* translators: %s: */ ?>
							<?php echo sprintf( __( 'The Poll is out of date. You have already voted for <strong>"%s"</strong>', 'buddypress-polls' ), $answers_by_user_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</p>

							<?php
						}
					} else {
						?>
					<p class="wbpoll-voted-info55 wbpoll-alert wbpoll-voted-info wbpoll-voted-info-<?php echo esc_attr( $post_id ); ?>">

							<?php esc_html_e( 'The Poll is out of date. You have not voted.', 'buddypress-polls' ); ?>
					</p>
						<?php
					}
				} else {  // end of if poll expired.

					if ( is_user_logged_in() ) {
						global $current_user;
						$this_user_role = $current_user->roles;
					} else {
						$this_user_role = array( 'guest' );
					}

					$allowed_user_groups = array_intersect( $poll_allowed_user_group, $this_user_role );

					if ( ! is_array( $wppolls_who_can_vote ) ) {
						$wppolls_who_can_vote = array();
					}
					$wp_allowed_user_group = array_intersect( $wppolls_who_can_vote, $this_user_role );

					if ( ( sizeof( $allowed_user_groups ) ) >= 1 || ! empty( $allowed_user_groups ) ) {
						$allowed_user_group = array_intersect( $poll_allowed_user_group, $this_user_role );
					} else {
						$allowed_user_group = array_intersect( $wp_allowed_user_group, $this_user_role );
					}
					// current user is not allowed.
					if ( ( sizeof( $allowed_user_group ) ) < 1 ) {

						// we know poll is not expired, and user is not allowed to vote
						// now we check if the user i allowed to see result and result is allow to show before expire.
						if ( $poll_show_result_before_expire == 0 ) {
							if ( $poll_is_voted ) {

								echo self::show_single_poll_result( $post_id, $reference, $result_chart_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							} else {
								?>
								<p class="wbpoll-voted-info wbpoll-alert wbpoll-voted-info-<?php echo esc_attr( $post_id ); ?>">
									<?php esc_html_e( 'You are not able to vote for this poll', 'buddypress-polls' ); ?>
								</p>
								<?php
							}
							// integrate user login for guest user.
							if ( ! is_user_logged_in() && $allow_guest_sign == 'on' ) :
								if ( is_singular() ) {
									$login_url    = wp_login_url( get_permalink() );
									$redirect_url = get_permalink();
								} else {
									global $wp;
									// $login_url =  wp_login_url( home_url( $wp->request ) );
									$login_url    = wp_login_url( home_url( add_query_arg( array(), $wp->request ) ) );
									$redirect_url = home_url( add_query_arg( array(), $wp->request ) );
								}
								?>

							<div class="wbpoll-guest-wrap">

								<p class="wbpoll-title-login">
									<?php echo esc_html__( 'Do you have an account and want to vote as a registered user? Please', 'buddypress-polls' ) . ' <a href="' . esc_url( '#' ) . '">' . esc_html__( 'login', 'buddypress-polls' ) . '</a>'; ?>
								</p>
								<?php
								$guest_login_html = wp_login_form(
									array(
										'redirect' => $redirect_url,
										'echo'     => false,
									)
								);

									$guest_login_html = apply_filters( 'wbpoll_login_html', $guest_login_html, $login_url, $redirect_url );

									$guest_register_html = '';
								?>

								<div class="wbpoll-guest-login-wrap">
									<?php echo $guest_login_html . $guest_register_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>

							</div>
								<?php
							endif;

						}
					} else {
						// current user is allowed
						// current user has voted this once.
						if ( $poll_is_voted_by_user ) {

							// find ip count.
							$sql        = $wpdb->prepare(
								"SELECT COUNT(ur.id) AS count FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d",
								$post_id,
								$user_id
							);
							$vote_count = $wpdb->get_var( $sql );

							$count = apply_filters( 'wbpoll_is_user_voted', $vote_count );

							if ( $count < $poll_votes_per_session ) {
								echo self::wbpoll_single_votting_display( $post_id, $poll_answers, $grid_class, $reference, $result_chart_type, $nonce, $poll_output, $poll_multivote, $vote_input_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								if ( $user_id != 0 && $user_id != '' ) {
									$sql = $wpdb->prepare(
										"SELECT ur.user_answer AS answer FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d",
										$post_id,
										$user_id,
									);

								} else {
									$sql = $wpdb->prepare(
										"SELECT ur.user_answer AS answer FROM $votes_name ur WHERE  ur.poll_id=%d AND ur.user_id=%d AND ur.user_ip = %s AND ur.user_cookie = %s ",
										$post_id,
										$user_id,
										$user_ip,
										$user_session
									);
								}
								$answers_by_user = $wpdb->get_var( $sql );

								$option_value = get_option( 'wbpolls_settings' );
								if ( ! empty( $option_value ) ) {

									$wppolls_show_result = isset( $option_value['wppolls_show_result'] ) ? $option_value['wppolls_show_result'] : '';
								}
								if ( $wppolls_show_result == 'yes' ) {
									if ( $answers_by_user !== null ) {
										$answers_by_user = maybe_unserialize( $answers_by_user );
										if ( is_array( $answers_by_user ) ) {
											$user_answers_textual = array();
											foreach ( $answers_by_user as $uchoice ) {
												$user_answers_textual[] = isset( $poll_answers[ $uchoice ] ) ? $poll_answers[ $uchoice ] : esc_html__(
													'Unknown or answer deleted',
													'buddypress-polls'
												);
											}

											$answers_by_user_html = implode( ', ', $user_answers_textual );
										} else {
											$answers_by_user      = intval( $answers_by_user );
											$answers_by_user_html = $poll_answers[ $answers_by_user ];

										}

										if ( $answers_by_user_html != '' ) {
											?>
											<p class="wbpoll-voted-info wbpoll-alert wbpoll-voted-info-<?php esc_attr( $post_id ); ?>">
												<?php /* translators: %s: */ ?>
												<?php echo sprintf( __( 'You have already voted for <strong>"%s"</strong>', 'buddypress-polls' ), $answers_by_user_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</p>

											<?php
											if ( $poll_show_result_before_expire == 0 ) {

												echo self::show_single_poll_result( $post_id, $reference, $result_chart_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
										} else {
											?>

											<p class="wbpoll-voted-info wbpoll-alert wbpoll-voted-info-<?php esc_attr( $post_id ); ?>">
												<?php esc_html_e( 'You have already voted ', 'buddypress-polls' ); ?>
											</p>
											<?php
										}
									} else {
										if ( $poll_show_result_before_expire == 0 ) {
											echo self::show_single_poll_result( $post_id, $reference, $result_chart_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									}
								} else {
									?>
								<p class="wbpoll-voted-info wbpoll-alert">
										<?php esc_html_e( 'Result hide by Admin', 'buddypress-polls' ); ?>
								</p>
									<?php
								}
							}
						} else {
							echo self::wbpoll_single_votting_display( $post_id, $poll_answers, $grid_class, $reference, $result_chart_type, $nonce, $poll_output, $poll_multivote, $vote_input_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						// end of if voted.
					}
					// end of allowed user.
				}
				// end of pole expires.
			} else { // poll didn't start yet.
				?>

			<p class="wbpoll-voted-info wbpoll-alert"><?php esc_html_e( 'Poll Status: Yet to start', 'buddypress-polls' ); ?></p>

			<?php } ?>

		</div><?php // end of wbpoll_wrapper. ?>

		<?php
		// return $poll_output;
		return ob_get_clean();
	}//end wbpoll_single_display()


	public static function wbpoll_single_votting_display( $post_id, $poll_answers, $grid_class, $reference, $result_chart_type, $nonce, $poll_output, $poll_multivote, $vote_input_type ) {

		// image, video, audio, html.
		$poll_ans_image        = get_post_meta( $post_id, '_wbpoll_full_size_image_answer', true );
		$poll_answers_video    = get_post_meta( $post_id, '_wbpoll_video_answer_url', true );
		$poll_video_suggestion = get_post_meta( $post_id, '_wbpoll_video_import_info', true );
		$poll_answers_audio    = get_post_meta( $post_id, '_wbpoll_audio_answer_url', true );
		$poll_audio_suggestion = get_post_meta( $post_id, '_wbpoll_audio_import_info', true );
		$poll_answers_html     = get_post_meta( $post_id, '_wbpoll_html_answer', true );
		// thumbnails image, video, audio, html.
		$thumbnail_poll_ans_image     = get_post_meta( $post_id, '_wbpoll_full_thumbnail_image_answer', true );
		$thumbnail_poll_answers_video = get_post_meta( $post_id, '_wbpoll_video_thumbnail_image_url', true );
		$thumbnail_poll_answers_audio = get_post_meta( $post_id, '_wbpoll_audio_thumbnail_image_url', true );

		// current user didn't vote yet.
		$poll_form_html = '';

		$poll_form_html = apply_filters( 'wbpoll_form_html_before', $poll_form_html, $post_id );

		$allow_guest_sign = 'on';

		if ( ! is_user_logged_in() && $allow_guest_sign == 'on' ) :
			if ( is_singular() ) {
				$login_url    = wp_login_url( get_permalink() );
				$redirect_url = get_permalink();
			} else {
				global $wp;
				// $login_url =  wp_login_url( home_url( $wp->request ) );
				$login_url    = wp_login_url( home_url( add_query_arg( array(), $wp->request ) ) );
				$redirect_url = home_url( add_query_arg( array(), $wp->request ) );
			}

			$guest_html = '<div class="wbpoll-guest-wrap">';

			$guest_html      .= '<p class="wbpoll-title-login wbpoll-alert">' . __( 'Do you have account and want to vote as registered user? Please <a  href="#">login</a>', 'buddypress-polls' ) . '</p>';
			$guest_login_html = wp_login_form(
				array(
					'redirect' => $redirect_url,
					'echo'     => false,
				)
			);

			$guest_login_html = apply_filters( 'wbpoll_login_html', $guest_login_html, $login_url, $redirect_url );

			$guest_register_html = '';

			if ( get_option( 'users_can_register' ) ) {
				$register_url = add_query_arg( 'redirect_to', urlencode( $redirect_url ), wp_registration_url() );
				/* translators: %s: */
				$guest_register_html .= '<p class="wbpoll-guest-register wbpoll-error">' . sprintf( __( 'No account yet? <a href="%s">Register</a>', 'buddypress-polls' ), $register_url ) . '</p>';
				$guest_register_html  = apply_filters( 'wbpoll_register_html', $guest_register_html, $redirect_url );
			}

			$guest_html .= '<div class="wbpoll-guest-login-wrap">' . $guest_login_html . $guest_register_html . '</div>';

			$guest_html .= '</div>';

			// $poll_form_html .= $guest_html;
		endif;

		$class = 'wbpoll-default';

		foreach ( $poll_answers as $index => $answer ) {
			if ( ! empty( $poll_ans_image[ $index ] ) || ! empty( $thumbnail_poll_ans_image[ $index ] ) ) {
				$class = 'wbpoll-image';
			} elseif ( ! empty( $poll_answers_video[ $index ] ) || ! empty( $thumbnail_poll_answers_video[ $index ] ) ) {
				$class = 'wbpoll-video';
			} elseif ( ! empty( $poll_answers_audio[ $index ] ) || ! empty( $thumbnail_poll_answers_audio[ $index ] ) ) {
				$class = 'wbpoll-audio';
			} else {
				$class = 'wbpoll-default';
			}
		}
			$poll_form_html .= '<div class="wbpoll_answer_wrapper wbpoll_answer_wrapper-' . $post_id . '" data-id="' . $post_id . '">';
			$poll_form_html .= '<form class="wbpoll-form wbpoll-form-' . $post_id . '" sction="" method="post" novalidate="true">';
			$poll_form_html .= '<div class="wbpoll-form-insidewrap ' . $grid_class . ' wbpoll-form-insidewrap-' . $post_id . '">';

			$poll_form_html = apply_filters( 'wbpoll_form_html_before_question', $poll_form_html, $post_id );

			$poll_answer_list_class = 'wbpoll-form-ans-list wbpoll-form-ans-list-' . $post_id;

			$poll_form_html .= '<div class="wbpolls-question-results ' . $class . ' ' . apply_filters(
				'wbpoll_form_answer_list_style_class',
				$poll_answer_list_class,
				$post_id
			) . '">';

			$poll_form_html = apply_filters( 'wbpoll_form_answer_start', $poll_form_html, $post_id );

		// listing poll answers as radio button.
		foreach ( $poll_answers as $index => $answer ) {

			$poll_answers_extra_single = isset( $poll_answers_extra[ $index ] ) ? $poll_answers_extra[ $index ] : array( 'type' => 'default' );

			$input_name = 'wbpoll_user_answer';
			if ( $poll_multivote ) {
				$input_name .= '-' . $index;
			}

			$poll_answer_listitem_class = 'wbpoll-form-ans-listitem wbpoll-form-ans-listitem-' . $post_id;

			$extra_list_style = '';
			$extra_list_attr  = '';
			$poll_form_html  .= '<div class="wbpoll-question-choices-item ' . apply_filters(
				'wbpoll_form_answer_listitem_style_class',
				$poll_answer_listitem_class,
				$post_id,
				$index,
				$answer,
				$poll_answers_extra_single
			) . '" style="' . apply_filters(
				'wbpoll_form_answer_listitem_style',
				$extra_list_style,
				$post_id,
				$index,
				$answer,
				$poll_answers_extra_single
			) . '" ' . apply_filters(
				'wbpoll_form_answer_listitem_attr',
				$extra_list_attr,
				$post_id,
				$index,
				$answer,
				$poll_answers_extra_single
			) . '>';

			$wbpoll_form_answer_listitem_inside_html_start = '';
			$poll_form_html                               .= apply_filters(
				'wbpoll_form_answer_listitem_inside_html_start',
				$wbpoll_form_answer_listitem_inside_html_start,
				$post_id,
				$index,
				$answer,
				$poll_answers_extra_single
			);
			$poll_form_html                               .= '<div class="wbpoll-question-choices-item-container">';
			$poll_form_html                               .= '<input type="' . $vote_input_type . '" value="' . $index . '" class="wbpoll_single_answer wbpoll_single_answer-radio wbpoll_single_answer-radio-' . $post_id . '" data-pollcolor = "" data-post-id="' . $post_id . '" name="' . $input_name . '"  data-answer="' . $answer . ' " id="wbpoll_single_answer-radio-' . $index . '-' . $post_id . '"  />';
			$poll_form_html                               .= '<label class="wbpoll-single-answer-label wbpoll_single_answer_label_radio" for="wbpoll_single_answer-radio-' . $index . '-' . $post_id . '">';
			$poll_form_html                               .= '<div class="wbpoll-question-choices-item-wrapper">';

			// image.
			if ( isset( $poll_ans_image[ $index ] ) && ! empty( $poll_ans_image[ $index ] ) && empty( $thumbnail_poll_ans_image[ $index ] ) ) {
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-image">';
				$poll_form_html .= '<span class="poll-image-view" data-id="' . $index . '"></span>';
				$poll_form_html .= '<img src="' . $poll_ans_image[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';

				$poll_form_html .= '<div class="wb-poll-lightbox poll-image-lightbox lightbox-' . $index . '" style="display:none;">';
				$poll_form_html .= '<div class="close" data-id="' . $index . '">';
				$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '<div class="content-area">';
				$poll_form_html .= '<img src="' . $poll_ans_image[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
			} elseif ( isset( $thumbnail_poll_ans_image[ $index ] ) && ! empty( $thumbnail_poll_ans_image[ $index ] ) && empty( $poll_ans_image[ $index ] ) ) {
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-image">';
				$poll_form_html .= '<img src="' . $thumbnail_poll_ans_image[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
			} elseif ( isset( $thumbnail_poll_ans_image[ $index ] ) && ! empty( $thumbnail_poll_ans_image[ $index ] ) && isset( $poll_ans_image[ $index ] ) && ! empty( $poll_ans_image[ $index ] ) ) {
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-thumb-image poll-image-view" data-id="' . $index . '">';
				$poll_form_html .= '<img src="' . $thumbnail_poll_ans_image[ $index ] . '">';
				$poll_form_html .= '</div>';

				$poll_form_html .= '<div class="wb-poll-lightbox poll-image-lightbox lightbox-' . $index . '" style="display:none;">';
				$poll_form_html .= '<div class="close" data-id="' . $index . '">';
				$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '<div class="content-area">';
				$poll_form_html .= '<img src="' . $poll_ans_image[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
			}

			// video.
			if ( isset( $poll_answers_video[ $index ] ) && ! empty( $poll_answers_video[ $index ] ) && empty( $thumbnail_poll_answers_video[ $index ] ) ) {
				if ( isset( $poll_video_suggestion[ $index ] ) && $poll_video_suggestion[ $index ] == 'yes' ) {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-video">';
					$poll_form_html .= '<iframe width="420" height="345" src="' . esc_url( $poll_answers_video[ $index ] ) . '"></iframe>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				} else {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-video">';
					$poll_form_html .= '<video src="' . esc_url( $poll_answers_video[ $index ] ) . '" controls="" poster="" preload="none"></video>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				}
			} elseif ( isset( $thumbnail_poll_answers_video[ $index ] ) && ! empty( $thumbnail_poll_answers_video[ $index ] ) && empty( $poll_answers_video[ $index ] ) ) {

				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-thumb-image poll-video-image">';
				$poll_form_html .= '<img src="' . $thumbnail_poll_answers_video[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';

			} elseif ( isset( $poll_answers_video[ $index ] ) && ! empty( $poll_answers_video[ $index ] ) && isset( $thumbnail_poll_answers_video[ $index ] ) && ! empty( $thumbnail_poll_answers_video[ $index ] ) ) {

				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-thumb-image poll-video-image poll-image-view"  data-id="' . $index . '">';
				$poll_form_html .= '<img src="' . $thumbnail_poll_answers_video[ $index ] . '">';
				$poll_form_html .= '</div>';

				if ( isset( $poll_video_suggestion[ $index ] ) && $poll_video_suggestion[ $index ] == 'yes' ) {
					$poll_form_html .= '<div class="wb-poll-lightbox poll-video-lightbox lightbox-' . $index . '" style="display:none;">';
					$poll_form_html .= '<div class="close" data-id="' . $index . '">';
					$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<div class="content-area">';
					$poll_form_html .= '<iframe width="420" height="345" src="' . $poll_answers_video[ $index ] . '"></iframe>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				} else {
					$poll_form_html .= '<div class="wb-poll-lightbox poll-video-lightbox lightbox-' . $index . '" style="display:none;">';
					$poll_form_html .= '<div class="close" data-id="' . $index . '">';
					$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<div class="content-area">';
					$poll_form_html .= '<video src="' . $poll_answers_video[ $index ] . '" controls="" poster="" preload="none"></video>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				}
			}

			// audio.
			if ( isset( $poll_answers_audio[ $index ] ) && ! empty( $poll_answers_audio[ $index ] ) && empty( $thumbnail_poll_answers_audio[ $index ] ) ) {

				if ( isset( $poll_audio_suggestion[ $index ] ) && $poll_audio_suggestion[ $index ] == 'yes' ) {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-audio">';
					$poll_form_html .= '<iframe width="420" height="345" src="' . $poll_answers_audio[ $index ] . '"></iframe>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';

				} else {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-audio">';
					$poll_form_html .= '<audio src="' . $poll_answers_audio[ $index ] . '" controls="" preload="none"></audio>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				}
			} elseif ( isset( $thumbnail_poll_answers_audio[ $index ] ) && ! empty( $thumbnail_poll_answers_audio[ $index ] ) && empty( $poll_answers_audio[ $index ] ) ) {

				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
				$poll_form_html .= '<div class="poll-thumb-image poll-video-image">';
				$poll_form_html .= '<img src="' . $thumbnail_poll_answers_audio[ $index ] . '">';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';

			} elseif ( isset( $poll_answers_audio[ $index ] ) && ! empty( $poll_answers_audio[ $index ] ) && isset( $thumbnail_poll_answers_audio[ $index ] ) && ! empty( $thumbnail_poll_answers_audio[ $index ] ) ) {

				if ( isset( $poll_audio_suggestion[ $index ] ) && $poll_audio_suggestion[ $index ] == 'yes' ) {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-thumb-image poll-audio-image poll-image-view" data-id="' . $index . '">';
					$poll_form_html .= '<img src="' . $thumbnail_poll_answers_audio[ $index ] . '">';
					$poll_form_html .= '</div>';

					$poll_form_html .= '<div class="wb-poll-lightbox poll-audio-lightbox lightbox-' . $index . '" style="display:none;">';
					$poll_form_html .= '<div class="close" data-id="' . $index . '">';
					$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<div class="content-area">';
					$poll_form_html .= '<iframe width="420" height="345" src="' . $poll_answers_audio[ $index ] . '"></iframe>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				} else {
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
					$poll_form_html .= '<div class="wbpoll-question-choices-item-content">';
					$poll_form_html .= '<div class="poll-thumb-image poll-audio-image poll-image-view" data-id="' . $index . '">';
					$poll_form_html .= '<img src="' . $thumbnail_poll_answers_audio[ $index ] . '">';
					$poll_form_html .= '</div>';

					$poll_form_html .= '<div class="wb-poll-lightbox poll-audio-lightbox lightbox-' . $index . '" style="display:none;">';
					$poll_form_html .= '<div class="close" data-id="' . $index . '">';
					$poll_form_html .= '<svg class="pswp__icn" aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M11.53 1.53A.75.75 0 0 0 10.47.47L6 4.94 1.53.47A.75.75 0 1 0 .47 1.53L4.94 6 .47 10.47a.75.75 0 1 0 1.06 1.06L6 7.06l4.47 4.47a.75.75 0 1 0 1.06-1.06L7.06 6l4.47-4.47Z"></path></svg>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<div class="content-area">';
					$poll_form_html .= '<audio src="' . $poll_answers_audio[ $index ] . '" controls="" preload="none" ></audio>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '</div>';
				}
			}

			if ( isset( $poll_answers_html[ $index ] ) && ! empty( $poll_answers_html[ $index ] ) ) {
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content-container">';
				$poll_form_html .= '<div class="wbpoll-question-choices-item-content poll-html-content">';
				$poll_form_html .= '<div class="poll-html">' . $poll_answers_html[ $index ] . '</div>';
				$poll_form_html .= '</div>';
				$poll_form_html .= '</div>';
			}

			$poll_form_html                                 .= '<div class="wbpoll-question-choices-item-label"><div class="wbpoll-question-choices-item-text">';
			$poll_form_html                                 .= '<span class="wbpoll_single_answer wbpoll_single_answer-text wbpoll_single_answer-text-' . $post_id . '"  data-post-id="' . $post_id . '" data-answer="' . $answer . ' ">' . apply_filters(
				'wbpoll_form_listitem_answer_title',
				esc_html__( $answer, 'buddypress-polls' ),
				$post_id,
				$index,
				$poll_answers_extra_single
			) . '</span>';
				$poll_form_html                             .= '</div></div></label></div></div>';
				$wbpoll_form_answer_listitem_inside_html_end = '';
				$poll_form_html                             .= apply_filters(
					'wbpoll_form_answer_listitem_inside_html_end',
					$wbpoll_form_answer_listitem_inside_html_end,
					$post_id,
					$index,
					$answer,
					$poll_answers_extra_single
				);

			$poll_form_html .= '</div>';
		}

		$poll_form_html = apply_filters( 'wbpoll_form_answer_end', $poll_form_html, $post_id );

		$poll_form_html .= '</div>';

		// hook.
		$poll_form_html = apply_filters( 'wbpoll_form_html_after_question', $poll_form_html, $post_id );
		$option_value   = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$wbpolls_user_add_extra_op = isset( $option_value['wbpolls_user_add_extra_op'] ) ? $option_value['wbpolls_user_add_extra_op'] : '';
		}
		$add_additional_fields = get_post_meta( $post_id, '_wbpoll_add_additional_fields', true );

		if ( 'yes' === $wbpolls_user_add_extra_op && ! empty( $add_additional_fields ) && '1' === $add_additional_fields ) {

			$poll_type = get_post_meta( $post_id, 'poll_type', true );
			// $poll_type_backend = get_post_meta( $post_id, '_wbpoll_answer_extra', true );
			// if ( isset( $poll_type_backend['answercount'] ) ) {
			// 	unset( $poll_type_backend['answercount'] );
			// }

			if (  isset( $poll_type ) && ! empty( $poll_type ) && 'default' === $poll_type ) {
				if ( $poll_type == 'default' || $add_additional_counts == true ) {
					$poll_form_html .= "<div class='btn btn-primary button wbpolls-add-option-button text_field' id='text_field'> " . esc_html__( 'Add Option', 'buddypress-polls' ) . '</div>';
					$poll_form_html .= '<div class="12121 wbpolls-answer-wrap"><div class="row wbpoll-list-item" id="type_text" style="display:none;">';
					$poll_form_html .= '<div class="ans-records text_records">';
					$poll_form_html .= '<input type="hidden" name="post_id" id="post_id" value="' . $post_id . '">';
					$poll_form_html .= '<div class="ans-records-wrap">';
					$poll_form_html .= '<label>' . esc_html__( 'Text Answer', 'buddypress-polls' ) . '</label>';
					$poll_form_html .= '<input name="_wbpoll_answer[]" id="wbpoll_answer" type="text" value="">';
					$poll_form_html .= '<input type="hidden" id="wbpoll_answer_extra_type" value="default" name="_wbpoll_answer_extra[][type]">';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<a class="add-field extra-fields-text" href="#">' . esc_html__( 'Add More', 'buddypress-polls' ) . '</a>';
					$poll_form_html .= '</div>';
					$poll_form_html .= '<div class="text_records_dynamic"></div></div>';
					$poll_form_html .= '<div class="btn btn-primary button wbpolls-remove-option-button post_text_field" id="post_text_field">' . esc_html__( 'Post Option', 'buddypress-polls' ) . '</div>';
					$poll_form_html .= '</div>';
				}
			}
		}
		$poll_pause = intval(
			get_post_meta(
				$post_id,
				'_wbpoll_pause_poll',
				true
			)
		);
		if ( $poll_pause != '1' ) {
			// show the poll button.
			$poll_form_html .= '<p class = "wbpoll_ajax_link"><button type="submit" class="btn btn-primary button wbpoll_vote_btn" data-reference = "' . $reference . '" data-charttype = "' . $result_chart_type . '" data-busy = "0" data-post-id="' . $post_id . '"  data-security="' . $nonce . '" >' . esc_html__(
				'Vote',
				'buddypress-polls'
			) . '<span class="wbvoteajaximage wbvoteajaximagecustom"></span></button></p>';
		} else {
			// Get the author ID of the post.
			$author_id = get_post_field( 'post_author', $post_id );
			// Get the author name.
			$author_name  = get_the_author_meta( 'display_name', $author_id );
			$poll_output .= '<p class="wbpoll-voted-info 55
			wbpoll-alert wbpoll-voted-info-' . $post_id . '"> ' . __(
				'This particular poll has been paused by the ' . $author_name . '. Please wait until it is live again',
				'buddypress-polls'
			) . '</p>';
		}
		$poll_form_html .= '<input type="hidden" name="action" value="wbpoll_user_vote">';
		$poll_form_html .= '<input type="hidden" name="reference" value="' . $reference . '">';
		$poll_form_html .= '<input type="hidden" name="chart_type" value="' . $result_chart_type . '">';
		$poll_form_html .= '<input type="hidden" name="nonce" value="' . $nonce . '">';
		$poll_form_html .= '<input type="hidden" name="poll_id" value="' . $post_id . '">';
		$poll_form_html .= '</div>';
		$poll_form_html .= '</form>';
		$poll_form_html .= '<div class="wbpoll_clearfix"></div>';
		$poll_form_html .= '</div>';
		$poll_form_html .= '<div class="wbpoll-qresponse wbpoll-qresponse-' . $post_id . '"></div>';
		$poll_form_html .= '<div class="wbpoll_clearfix"></div>';

		$poll_form_html = apply_filters( 'wbpoll_form_html_after', $poll_form_html, $post_id );

		$poll_output .= apply_filters( 'wbpoll_form_html', $poll_form_html, $post_id );

		return $poll_output;

	}

	public static function add_additional_allValuesSame( $arr ) {
		if ( count( $arr ) === 0 ) {
			return true; // Empty array is considered to have all values the same.
		}
		$firstValue = isset( $arr[0] ) ? $arr[0] : array();
		foreach ( $arr as $value ) {
			if ( $value !== $firstValue ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get result from a single poll for backend
	 *
	 * @param  int $post_id
	 *
	 * return string|mixed
	 */
	public static function show_backend_single_poll_result( $poll_id, $reference, $result_chart_type = 'text' ) {
		global $wpdb;

		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;

		$user_ip = self::get_ipaddress();

		if ( $user_id == 0 ) {
			// $user_session = $_COOKIE[ BPOLLS_COOKIE_NAME ]; //this is string
			$user_session = '';
		} elseif ( is_user_logged_in() ) {
			$user_session = 'user-' . $user_id; // this is string
		}

		$setting_api     = get_option( 'wbpoll_global_settings' );
		$poll_start_date = get_post_meta( $poll_id, '_wbpoll_start_date', true ); // poll start date.
		$poll_end_date   = get_post_meta( $poll_id, '_wbpoll_end_date', true ); // poll end date.
		$poll_user_roles = get_post_meta( $poll_id, '_wbpoll_user_roles', true ); // poll user roles.
		if ( ! is_array( $poll_user_roles ) ) {
			$poll_user_roles = array();
		}

		$poll_content                   = get_post_meta( $poll_id, '_wbpoll_content', true ); // poll content.
		$poll_never_expire              = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_never_expire',
				true
			)
		); // poll never epire.
		$poll_show_result_before_expire = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_show_result_before_expire',
				true
			)
		); // poll never epire.

		$poll_result_chart_type = get_post_meta( $poll_id, '_wbpoll_result_chart_type', true ); // chart type.

		$result_chart_type = self::chart_type_fallback( $result_chart_type );

		$poll_answers = get_post_meta( $poll_id, '_wbpoll_answer', true );
		$poll_answers = is_array( $poll_answers ) ? $poll_answers : array();

		$poll_colors = get_post_meta( $poll_id, '_wbpoll_answer_color', true );
		$poll_colors = is_array( $poll_colors ) ? $poll_colors : array();

		// image, video, audio, html.
		$poll_ans_image = get_post_meta( $poll_id, '_wbpoll_full_size_image_answer', true );
		$poll_ans_image = is_array( $poll_ans_image ) ? $poll_ans_image : array();

		$poll_answers_video = get_post_meta( $poll_id, '_wbpoll_video_answer_url', true );
		$poll_answers_video = is_array( $poll_answers_video ) ? $poll_answers_video : array();

		$poll_video_suggestion = get_post_meta( $poll_id, '_wbpoll_video_import_info', true );
		$poll_video_suggestion = is_array( $poll_video_suggestion ) ? $poll_video_suggestion : array();

		$poll_answers_audio = get_post_meta( $poll_id, '_wbpoll_audio_answer_url', true );
		$poll_answers_audio = is_array( $poll_answers_audio ) ? $poll_answers_audio : array();

		$poll_audio_suggestion = get_post_meta( $poll_id, '_wbpoll_audio_import_info', true );
		$poll_audio_suggestion = is_array( $poll_audio_suggestion ) ? $poll_audio_suggestion : array();

		$poll_answers_html = get_post_meta( $poll_id, '_wbpoll_html_answer', true );
		$poll_answers_html = is_array( $poll_answers_html ) ? $poll_answers_html : array();

		// thumbnails image, video, audio, html.
		$thumbnail_poll_ans_image = get_post_meta( $poll_id, '_wbpoll_full_thumbnail_image_answer', true );
		$thumbnail_poll_ans_image = is_array( $thumbnail_poll_ans_image ) ? $thumbnail_poll_ans_image : array();

		$thumbnail_poll_answers_video = get_post_meta( $poll_id, '_wbpoll_video_thumbnail_image_url', true );
		$thumbnail_poll_answers_video = is_array( $thumbnail_poll_answers_video ) ? $thumbnail_poll_answers_video : array();

		$thumbnail_poll_answers_audio = get_post_meta( $poll_id, '_wbpoll_audio_thumbnail_image_url', true );
		$thumbnail_poll_answers_audio = is_array( $thumbnail_poll_answers_audio ) ? $thumbnail_poll_answers_audio : array();

		$total_results = self::get_pollResult( $poll_id );

		$poll_result = array();

		$poll_result['reference'] = $reference;
		$poll_result['poll_id']   = $poll_id;
		$poll_result['total']     = self::count_pollResult( $poll_id );

		$poll_result['colors'] = $poll_colors;
		$poll_result['answer'] = $poll_answers;
		// $poll_result['results']           = json_encode($total_results);
		$poll_result['chart_type'] = $result_chart_type;
		$poll_result['text']       = '';

		$poll_result['image'] = $poll_ans_image;
		$poll_result['video'] = $poll_answers_video;
		$poll_result['audio'] = $poll_answers_audio;
		$poll_result['html']  = $poll_answers_html;

		$poll_result['thumb_image']     = $thumbnail_poll_ans_image;
		$poll_result['thumb_video_img'] = $thumbnail_poll_answers_video;
		$poll_result['thumb_audio_img'] = $thumbnail_poll_answers_audio;

		$poll_result['video_suggestion'] = $poll_video_suggestion;
		$poll_result['audio_suggestion'] = $poll_audio_suggestion;

		$poll_answers_weight = array();

		foreach ( $total_results as $result ) {
			$user_ans = maybe_unserialize( $result['user_answer'] );

			if ( is_array( $user_ans ) ) {

				foreach ( $user_ans as $u_ans ) {
					$old_val                       = isset( $poll_answers_weight[ $u_ans ] ) ? intval( $poll_answers_weight[ $u_ans ] ) : 0;
					$poll_answers_weight[ $u_ans ] = ( $old_val + 1 );
				}
			} else {
				$user_ans                         = intval( $user_ans );
				$old_val                          = isset( $poll_answers_weight[ $user_ans ] ) ? intval( $poll_answers_weight[ $user_ans ] ) : 0;
				$poll_answers_weight[ $user_ans ] = ( $old_val + 1 );
			}
		}

		$poll_result['answers_weight'] = $poll_answers_weight;

		// ready mix :).
		$poll_weighted_index  = array();
		$poll_weighted_labels = array();

		foreach ( $poll_answers as $index => $answer_title ) {
			// $poll_weighted_labels[ $answer ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_index[ $index ]         = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_labels[ $answer_title ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
		}

		$poll_result['weighted_index'] = $poll_weighted_index;
		$poll_result['weighted_label'] = $poll_weighted_labels;

		ob_start();

		do_action( 'wbpoll_answer_html_before', $poll_id, $reference, $poll_result );
		echo '<div class="wbpoll_result_wrap wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . ' wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap_' . esc_html( $poll_id, 'buddypress-polls' ) . ' wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . '_' . esc_html( $poll_id, 'buddypress-polls' ) . ' ">';

		do_action( 'wbpoll_answer_html_before_question', $poll_id, $reference, $poll_result );

		$poll_display_methods = self::wbpoll_display_options_backend();
		if ( ! empty( $poll_display_methods ) ) {
			$poll_display_method = $poll_display_methods[ $result_chart_type ];

			$method = $poll_display_method['method'];

			if ( $method != '' && is_callable( $method ) ) {
				call_user_func_array( $method, array( $poll_id, $reference, $poll_result ) );
			}
		}

		do_action( 'wbpoll_answer_html_after_question', $poll_id, $reference, $poll_result );

		echo '</div>';
		do_action( 'wbpoll_answer_html_after', $poll_id, $reference, $poll_result );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}


	/**
	 * Get result from a single poll for backend
	 *
	 * @param  int $post_id
	 *
	 * return string|mixed
	 */
	public static function show_backend_single_poll_widget_result( $poll_id, $reference, $result_chart_type = 'text' ) {
		global $wpdb;
		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;

		$poll_user_roles = get_post_meta( $poll_id, '_wbpoll_user_roles', true ); // poll user roles.
		if ( ! is_array( $poll_user_roles ) ) {
			$poll_user_roles = array();
		}

		$result_chart_type = self::chart_type_fallback( $result_chart_type );

		$poll_answers = get_post_meta( $poll_id, '_wbpoll_answer', true );
		$poll_answers = is_array( $poll_answers ) ? $poll_answers : array();

		$total_results = self::get_pollResult( $poll_id );

		$poll_result = array();

		$poll_result['reference'] = $reference;
		$poll_result['poll_id']   = $poll_id;
		$poll_result['total']     = self::count_pollResult( $poll_id );

		$poll_result['answer'] = $poll_answers;
		// $poll_result['results']           = json_encode($total_results);
		$poll_result['chart_type'] = $result_chart_type;
		$poll_result['text']       = '';

		$poll_answers_weight = array();

		foreach ( $total_results as $result ) {
			$user_ans = maybe_unserialize( $result['user_answer'] );

			if ( is_array( $user_ans ) ) {

				foreach ( $user_ans as $u_ans ) {
					$old_val                       = isset( $poll_answers_weight[ $u_ans ] ) ? intval( $poll_answers_weight[ $u_ans ] ) : 0;
					$poll_answers_weight[ $u_ans ] = ( $old_val + 1 );
				}
			} else {
				$user_ans                         = intval( $user_ans );
				$old_val                          = isset( $poll_answers_weight[ $user_ans ] ) ? intval( $poll_answers_weight[ $user_ans ] ) : 0;
				$poll_answers_weight[ $user_ans ] = ( $old_val + 1 );
			}
		}

		$poll_result['answers_weight'] = $poll_answers_weight;

		// ready mix :)
		$poll_weighted_index  = array();
		$poll_weighted_labels = array();

		foreach ( $poll_answers as $index => $answer_title ) {
			// $poll_weighted_labels[ $answer ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_index[ $index ]         = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_labels[ $answer_title ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
		}

		$poll_result['weighted_index'] = $poll_weighted_index;
		$poll_result['weighted_label'] = $poll_weighted_labels;

		ob_start();

		do_action( 'wbpoll_answer_html_before', $poll_id, $reference, $poll_result );
		echo '<div class="wbpoll_result_wrap wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . ' wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap_' . esc_html( $poll_id, 'buddypress-polls' ) . ' wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . '_' . esc_html( $poll_id, 'buddypress-polls' ) . ' ">';

		do_action( 'wbpoll_answer_html_before_question', $poll_id, $reference, $poll_result );

		$poll_display_methods = self::wbpoll_display_options_widget_result();
		$poll_display_method  = $poll_display_methods[ $result_chart_type ];

		$method = $poll_display_method['method'];

		if ( $method != '' && is_callable( $method ) ) {
			call_user_func_array( $method, array( $poll_id, $reference, $poll_result ) );
		}

		do_action( 'wbpoll_answer_html_after_question', $poll_id, $reference, $poll_result );

		echo '</div>';
		do_action( 'wbpoll_answer_html_after', $poll_id, $reference, $poll_result );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}


	public static function show_backend_single_poll_widget_result_all_voted() {
		global $wpdb;
		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;
		$user_ip      = self::get_ipaddress();

		ob_start();
		$output = '';

		if ( is_user_logged_in() ) {
			$votes_name = self::wb_poll_table_name();
			global $wpdb;
			$sql     = $wpdb->prepare( "SELECT DISTINCT poll_id, poll_title FROM {$votes_name} WHERE user_id = %d", $user_id );
			$results = $wpdb->get_results( $sql, ARRAY_A );
			if ( ! empty( $results ) ) {
				$output .= "<select id='poll_seletect'>";
				$output .= "<option value=''>" . esc_html__( 'Select poll', 'buddypress-polls' ) . '</option>';
				foreach ( $results as $res ) {
					$output .= "<option value='" . $res['poll_id'] . "'>" . $res['poll_title'] . '</option>';
				}

				$output .= '</select>';

			} else {
				$output .= '<p>' . esc_html__( 'Please submit your vote in the poll to access the poll results.', 'buddypress-polls' ) . '</p>';
			}
		} else {

			$votes_name = self::wb_poll_table_name();
			global $wpdb;
			$sql     = $wpdb->prepare( "SELECT DISTINCT poll_id, poll_title FROM {$votes_name} WHERE user_id = %d AND user_ip = %s", $user_id, $user_ip );
			$results = $wpdb->get_results( $sql, ARRAY_A );
			if ( ! empty( $results ) ) {
				$output .= "<select id='poll_seletect'>";
				$output .= "<option value=''>" . esc_html__( 'Select poll', 'buddypress-polls' ) . '</option>';
				foreach ( $results as $res ) {
					$output .= "<option value='" . $res['poll_id'] . "'>" . $res['poll_title'] . '</option>';
				}

				$output .= '</select>';

			} else {
				$output .= '<p>' . esc_html__( 'Please submit your vote in the poll to access the poll results.', 'buddypress-polls' ) . '</p>';
			}
		}

		$output .= "<div class='all_polll_result'>";
		$output .= '</div>';
		$output .= ob_get_contents();
		ob_end_clean();

		return $output;

	}

	/**
	 * Get result from a single poll
	 *
	 * @param  int $post_id
	 *
	 * return string|mixed
	 */
	public static function show_single_poll_result( $poll_id, $reference, $result_chart_type = 'text' ) {
		global $wpdb;

		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;

		$user_ip = self::get_ipaddress();

		if ( $user_id == 0 ) {
			// $user_session = $_COOKIE[ BPOLLS_COOKIE_NAME ]; //this is string
			$user_session = '';
		} elseif ( is_user_logged_in() ) {
			$user_session = 'user-' . $user_id; // this is string.
		}

		$setting_api     = get_option( 'wbpoll_global_settings' );
		$poll_start_date = get_post_meta( $poll_id, '_wbpoll_start_date', true ); // poll start date.
		$poll_end_date   = get_post_meta( $poll_id, '_wbpoll_end_date', true ); // poll end date.
		$poll_user_roles = get_post_meta( $poll_id, '_wbpoll_user_roles', true ); // poll user roles.
		if ( ! is_array( $poll_user_roles ) ) {
			$poll_user_roles = array();
		}

		$poll_content                   = get_post_meta( $poll_id, '_wbpoll_content', true ); // poll content.
		$poll_never_expire              = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_never_expire',
				true
			)
		); // poll never epire.
		$poll_show_result_before_expire = intval(
			get_post_meta(
				$poll_id,
				'_wbpoll_show_result_before_expire',
				true
			)
		); // poll never epire.

		$poll_result_chart_type = get_post_meta( $poll_id, '_wbpoll_result_chart_type', true ); // chart type.

		$result_chart_type = self::chart_type_fallback( $result_chart_type );

		$poll_answers = get_post_meta( $poll_id, '_wbpoll_answer', true );
		$poll_answers = is_array( $poll_answers ) ? $poll_answers : array();

		$poll_colors = get_post_meta( $poll_id, '_wbpoll_answer_color', true );
		$poll_colors = is_array( $poll_colors ) ? $poll_colors : array();

		// image, video, audio, html.
		$poll_ans_image = get_post_meta( $poll_id, '_wbpoll_full_size_image_answer', true );
		$poll_ans_image = is_array( $poll_ans_image ) ? $poll_ans_image : array();

		$poll_answers_video = get_post_meta( $poll_id, '_wbpoll_video_answer_url', true );
		$poll_answers_video = is_array( $poll_answers_video ) ? $poll_answers_video : array();

		$poll_answers_audio = get_post_meta( $poll_id, '_wbpoll_audio_answer_url', true );
		$poll_answers_audio = is_array( $poll_answers_audio ) ? $poll_answers_audio : array();

		$poll_answers_html = get_post_meta( $poll_id, '_wbpoll_html_answer', true );
		$poll_answers_html = is_array( $poll_answers_html ) ? $poll_answers_html : array();

		// thumbnails image, video, audio, html.
		$thumbnail_poll_ans_image = get_post_meta( $poll_id, '_wbpoll_full_thumbnail_image_answer', true );
		$thumbnail_poll_ans_image = is_array( $thumbnail_poll_ans_image ) ? $thumbnail_poll_ans_image : array();

		$thumbnail_poll_answers_video = get_post_meta( $poll_id, '_wbpoll_video_thumbnail_image_url', true );
		$thumbnail_poll_answers_video = is_array( $thumbnail_poll_answers_video ) ? $thumbnail_poll_answers_video : array();

		$thumbnail_poll_answers_audio = get_post_meta( $poll_id, '_wbpoll_audio_thumbnail_image_url', true );
		$thumbnail_poll_answers_audio = is_array( $thumbnail_poll_answers_audio ) ? $thumbnail_poll_answers_audio : array();

		$poll_video_suggestion = get_post_meta( $poll_id, '_wbpoll_video_import_info', true );
		$poll_video_suggestion = is_array( $poll_video_suggestion ) ? $poll_video_suggestion : array();

		$poll_audio_suggestion = get_post_meta( $poll_id, '_wbpoll_audio_import_info', true );
		$poll_audio_suggestion = is_array( $poll_audio_suggestion ) ? $poll_audio_suggestion : array();

		$total_results = self::get_pollResult( $poll_id );

		$poll_result = array();

		$poll_result['reference'] = $reference;
		$poll_result['poll_id']   = $poll_id;
		$poll_result['total']     = self::count_pollResult( $poll_id );

		$poll_result['colors'] = $poll_colors;
		$poll_result['answer'] = $poll_answers;
		// $poll_result['results']           = json_encode($total_results);
		$poll_result['chart_type'] = $result_chart_type;
		$poll_result['text']       = '';

		$poll_result['image'] = $poll_ans_image;
		$poll_result['video'] = $poll_answers_video;
		$poll_result['audio'] = $poll_answers_audio;
		$poll_result['html']  = $poll_answers_html;

		$poll_result['thumb_image']     = $thumbnail_poll_ans_image;
		$poll_result['thumb_video_img'] = $thumbnail_poll_answers_video;
		$poll_result['thumb_audio_img'] = $thumbnail_poll_answers_audio;

		$poll_result['video_suggestion'] = $poll_video_suggestion;
		$poll_result['audio_suggestion'] = $poll_audio_suggestion;

		$poll_answers_weight = array();

		foreach ( $total_results as $result ) {
			$user_ans = maybe_unserialize( $result['user_answer'] );

			if ( is_array( $user_ans ) ) {

				foreach ( $user_ans as $u_ans ) {
					$old_val                       = isset( $poll_answers_weight[ $u_ans ] ) ? intval( $poll_answers_weight[ $u_ans ] ) : 0;
					$poll_answers_weight[ $u_ans ] = ( $old_val + 1 );
				}
			} else {
				$user_ans                         = intval( $user_ans );
				$old_val                          = isset( $poll_answers_weight[ $user_ans ] ) ? intval( $poll_answers_weight[ $user_ans ] ) : 0;
				$poll_answers_weight[ $user_ans ] = ( $old_val + 1 );
			}
		}

		$poll_result['answers_weight'] = $poll_answers_weight;

		// ready mix :).
		$poll_weighted_index  = array();
		$poll_weighted_labels = array();

		foreach ( $poll_answers as $index => $answer_title ) {
			// $poll_weighted_labels[ $answer ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_index[ $index ]         = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
			$poll_weighted_labels[ $answer_title ] = isset( $poll_answers_weight[ $index ] ) ? $poll_answers_weight[ $index ] : 0;
		}

		$poll_result['weighted_index'] = $poll_weighted_index;
		$poll_result['weighted_label'] = $poll_weighted_labels;

		ob_start();

		do_action( 'wbpoll_answer_html_before', $poll_id, $reference, $poll_result );
		echo '<div class="wbpoll_result_wrap wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . ' wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap wbpoll_' . esc_html( $result_chart_type, 'buddypress-polls' ) . '_result_wrap_' . esc_html( $poll_id, 'buddypress-polls' ) . ' wbpoll_result_wrap_' . esc_html( $reference, 'buddypress-polls' ) . '_' . esc_html( $poll_id, 'buddypress-polls' ) . ' ">';

		do_action( 'wbpoll_answer_html_before_question', $poll_id, $reference, $poll_result );

		$poll_display_methods = self::wbpoll_display_options();
		$poll_display_method  = ( isset( $poll_display_methods[ $result_chart_type ] ) ) ? $poll_display_methods[ $result_chart_type ] : '';

		$method = ( isset( $poll_display_method['method'] ) ) ? $poll_display_method['method'] : '';

		if ( $method != '' && is_callable( $method ) ) {
			call_user_func_array( $method, array( $poll_id, $reference, $poll_result ) );
		}

		do_action( 'wbpoll_answer_html_after_question', $poll_id, $reference, $poll_result );

		echo '</div>';
		do_action( 'wbpoll_answer_html_after', $poll_id, $reference, $poll_result );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}


	/**
	 * Chart Type fallback
	 *
	 * @param $chart_type
	 *
	 * @return string
	 */
	public static function chart_type_fallback( $chart_type ) {
		$poll_display_methods = self::wbpoll_display_options();
		$chart_info           = ( isset( $poll_display_methods[ $chart_type ] ) ) ? $poll_display_methods[ $chart_type ] : '';

		if ( $chart_info != '' && is_callable( $chart_info['method'] ) ) {
			return $chart_type;
		}

		return 'text';
	}//end chart_type_fallback()

	/**
	 * Sanitizes a hex color.
	 *
	 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
	 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
	 *
	 * @param  string $color
	 *
	 * @return string|void
	 * @since 3.4.0
	 */
	public static function sanitize_hex_color( $color ) {

		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
	}//end sanitize_hex_color()

	/**
	 * wbpoll post type meta fields array
	 *
	 * @return array
	 *
	 * initialize with init
	 */
	public static function get_meta_fields() {

		$roles           = self::user_roles( false, true );
		$global_settings = get_option( 'wbpoll_global_settings' );

		$default_user_roles = isset( $global_settings['user_roles'] ) ? $global_settings['user_roles'] : self::user_roles(
			true,
			true
		);

		$default_never_expire              = isset( $global_settings['never_expire'] ) ? intval( $global_settings['never_expire'] ) : 0;
		$default_content                   = isset( $global_settings['content'] ) ? $global_settings['content'] : 1;
		$default_result_chart              = isset( $global_settings['result_chart_type'] ) ? $global_settings['result_chart_type'] : 'text';
		$default_poll_multivote            = isset( $global_settings['poll_multivote'] ) ? intval( $global_settings['poll_multivote'] ) : 0;
		$default_show_result_before_expire = isset( $global_settings['show_result_before_expire'] ) ? intval( $global_settings['show_result_before_expire'] ) : 0;

		// Field Array.
		$prefix = '_wbpoll_';

		$poll_display_methods = self::wbpoll_display_options();
		$poll_display_methods = self::wbpoll_display_options_linear( $poll_display_methods );

		$start_date = date_i18n( 'Y-m-d H:i:s' );
		$timestamp  = time() - 86400;
		$end_date   = strtotime( '+7 day', $timestamp );

		$post_meta_fields = array(
			'_wbpoll_never_expire'              => array(
				'label'   => esc_html__( 'Never Expire', 'buddypress-polls' ),
				'desc'    => esc_html__( 'Select if you want your poll to never expire.', 'buddypress-polls' ),
				'id'      => '_wbpoll_never_expire',
				'type'    => 'radio',
				'default' => $default_never_expire,
				'options' => array(
					'1' => esc_html__( 'Yes', 'buddypress-polls' ),
					'0' => esc_html__( 'No', 'buddypress-polls' ),
				),
			),
			'_wbpoll_start_date'                => array(
				'label'   => esc_html__( 'Start Date', 'buddypress-polls' ),
				'desc'    => __(
					'Poll Start Date. [<strong> Note:</strong> Field required. Default is today]',
					'buddypress-polls'
				),
				'id'      => '_wbpoll_start_date',
				'type'    => 'date',
				'default' => $start_date,
			),
			'_wbpoll_end_date'                  => array(
				'label'   => esc_html__( 'End Date', 'buddypress-polls' ),
				'desc'    => __(
					'Poll End Date.  [<strong> Note:</strong> Field required. Default is next seven days. ]',
					'buddypress-polls'
				),
				'id'      => '_wbpoll_end_date',
				'type'    => 'date',
				'default' => date_i18n( 'Y-m-d H:i:s', $end_date ),
			),
			'_wbpoll_user_roles'                => array(
				'label'    => esc_html__( 'Who Can Vote', 'buddypress-polls' ),
				'desc'     => esc_html__( 'Which user role will have vote capability', 'buddypress-polls' ),
				'id'       => '_wbpoll_user_roles',
				'type'     => 'multiselect',
				'options'  => $roles,
				'optgroup' => 1,
				'default'  => array(),
			),
			'_wbpoll_content'                   => array(
				'label'   => esc_html__( 'Show Poll Description in Shortcode', 'buddypress-polls' ),
				'desc'    => esc_html__( 'Select if you want to show content.', 'buddypress-polls' ),
				'id'      => '_wbpoll_content',
				'type'    => 'radio',
				'default' => $default_content,
				'options' => array(
					'1' => esc_html__( 'Yes', 'buddypress-polls' ),
					'0' => esc_html__( 'No', 'buddypress-polls' ),
				),

			),

			'_wbpoll_show_result_before_expire' => array(
				'label'   => esc_html__( 'Show Result After Expires', 'buddypress-polls' ),
				'desc'    => esc_html__(
					'Select if you want poll to show result After expires. After expires the result will be shown always.',
					'buddypress-polls'
				),
				'id'      => '_wbpoll_show_result_before_expire',
				'type'    => 'radio',
				'default' => $default_show_result_before_expire,
				'options' => array(
					'1' => esc_html__( 'Yes', 'buddypress-polls' ),
					'0' => esc_html__( 'No', 'buddypress-polls' ),
				),
			),
			'_wbpoll_multivote'                 => array(
				'label'   => esc_html__( 'Enable Multi Choice', 'buddypress-polls' ),
				'desc'    => esc_html__( 'Can user vote multiple option', 'buddypress-polls' ),
				'id'      => '_wbpoll_multivote',
				'type'    => 'radio',
				'default' => $default_poll_multivote,
				'options' => array(
					'1' => esc_html__( 'Yes', 'buddypress-polls' ),
					'0' => esc_html__( 'No', 'buddypress-polls' ),
				),
			),
			'_wbpoll_vote_per_session'          => array(
				'label'   => esc_html__( 'Votes Per User', 'buddypress-polls' ),
				'desc'    => esc_html__( 'Set the number of times a user can vote.', 'buddypress-polls' ),
				'id'      => '_wbpoll_vote_per_session',
				'type'    => 'number',
				'default' => 1,
			),

		);
		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$wbpolls_user_add_extra_op = isset( $option_value['wbpolls_user_add_extra_op'] ) ? $option_value['wbpolls_user_add_extra_op'] : '';

			if ( $wbpolls_user_add_extra_op == 'yes' ) {
				$post_meta_fields['_wbpoll_add_additional_fields'] = array(
					'label'   => esc_html__( 'Add Additional poll option', 'buddypress-polls' ),
					'desc'    => esc_html__( 'Add Additional poll option only for text poll.', 'buddypress-polls' ),
					'id'      => '_wbpoll_add_additional_fields',
					'type'    => 'radio',
					'default' => $default_content,
					'options' => array(
						'1' => esc_html__( 'Yes', 'buddypress-polls' ),
						'0' => esc_html__( 'No', 'buddypress-polls' ),
					),
				);
			}
		}

		$post_meta_fields_buddypress = array(
			'_wbpoll_show_poll_under_the_activity' => array(
				'label'   => esc_html__( 'Show poll under the BuddyPress activity', 'buddypress-polls' ),
				'desc'    => esc_html__(
					'Select if you want poll to Show poll under the BuddyPress activity.',
					'buddypress-polls'
				),
				'id'      => '_wbpoll_show_poll_under_the_activity',
				'type'    => 'radio',
				'default' => 0,
				'options' => array(
					'1' => esc_html__( 'Yes', 'buddypress-polls' ),
					'0' => esc_html__( 'No', 'buddypress-polls' ),
				),
			),
		);

		if ( class_exists( 'Buddypress' ) ) {
			$return_post = apply_filters( 'wbpoll_fields', $post_meta_fields_buddypress );
		}
		$return_post = apply_filters( 'wbpoll_fields', $post_meta_fields );
		return $return_post;

	}//end get_meta_fields()


	/**
	 * Single answer field template
	 *
	 * @param  int           $index
	 * @param  string        $answers_title
	 * @param  string        $answers_color
	 * @param  int           $is_voted
	 * @param        $answers_extra
	 * @param        $poll_postid
	 *
	 * @return string
	 */
	public static function wbpoll_answer_field_template(
		$index = 0,
		$answers_title = '',
		$answers_color = '',
		$is_voted = 0,
		$answers_extra = '',
		$poll_postid = '',
		$full_size_image = '',
		$thumbnail_size_image = '',
		$video_url = '',
		$video_thumbnail_image = '',
		$html_code = '',
		$audio_url = '',
		$audio_thumbnail_image = '',
		$number = '',
		$iframe_video_url = '',
		$iframe_audio_url = ''

	) {

		$input_type  = 'text';
		$color_class = 'wbpoll_answer_color';
		ob_start();
		if ( isset( $answers_extra['type'] ) && $answers_extra['type'] == 'default' ) {
			$answer_type = isset( $answers_extra['type'] ) ? $answers_extra['type'] : 'default';
			?>
			<li class="wb_poll_items" id="wb-poll-answer-<?php echo esc_attr( $index ); ?>" data-type="default">
				<div class="wbpoll-containable-list-item-toolbar toolbar-<?php echo esc_attr( $index ); ?>"  data-id="<?php echo esc_attr( $index ); ?>">
					<div class="wb_pollmove"><i title="<?php esc_html_e( 'Drag and Drop to reorder poll answers', 'buddypress-polls' ); ?>" class="cbpollmoveicon"><?php esc_html_e( 'Move', 'buddypress-polls' ); ?></i></div>

					<div class="wbpoll-containable-list-item-toolbar-collapse-text"><span><?php echo esc_attr( $number ); ?></span></div>
					<div class="wbpoll-containable-list-item-toolbar-preview-text"><span><?php echo esc_html( $answers_title ); ?></span></div>
					<?php if ( $answers_extra['type'] == 'default' ) { ?>
						<div class="wbpoll-containable-list-item-toolbar-preview-type"><span><?php esc_html_e( 'Text', 'buddypress-polls' ); ?></span></div>
					<?php } ?>

					<div class="wb_pollremove dashicons dashicons-trash" title="<?php esc_html_e( 'Remove', 'buddypress-polls' ); ?>"></div>
					<div class="wbpoll-toolbar-toggle dashicons dashicons-arrow-down-alt2" title="<?php esc_html_e( 'Toggle', 'buddypress-polls' ); ?>"></div>
				</div>

				<div class="wbpoll-containable-list-item-editor wbpoll-containable-list-item-editor-text hidetab wb-hide-<?php echo esc_attr( $index ); ?>">
					<div class="wbpoll-options-input-container">
						<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_answer[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $answers_title ); ?>"   id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" class="wbpoll_answer"/>
						<input type="hidden" id="wbpoll_answer_extra_type_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $answer_type ); ?>" name="_wbpoll_answer_extra[<?php echo esc_attr( $index ); ?>][type]" />

						<?php do_action( 'wbpoll_answer_extra_fields', $index, $answers_extra, $is_voted, $poll_postid ); ?>

					</div>
				</div>

				<div class="clear clearfix"></div>
			</li>
			<?php
		} elseif ( isset( $answers_extra['type'] ) && $answers_extra['type'] == 'image' ) {

			$answer_type = isset( $answers_extra['type'] ) ? $answers_extra['type'] : 'image';

			?>
			<li class="wb_poll_items" id="wb-poll-answer-<?php echo esc_attr( $index ); ?>" data-type="image">

				<div class="wbpoll-containable-list-item-toolbar toolbar-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>">
					<div class="wb_pollmove"><i title="<?php esc_html_e( 'Drag and Drop to reorder poll answers', 'buddypress-polls' ); ?>" class="cbpollmoveicon"><?php esc_html_e( 'Move', 'buddypress-polls' ); ?></i></div>
					<div class="wbpoll-containable-list-item-toolbar-collapse-text"><span><?php echo esc_html( $number ); ?></span></div>
					<div class="wbpoll-containable-list-item-toolbar-preview-text"><span><?php echo esc_html( $answers_title ); ?></span></div>
					<?php if ( $answers_extra['type'] == 'image' ) { ?>
						<div class="wbpoll-containable-list-item-toolbar-preview-type"><span><?php esc_html_e( 'Image', 'buddypress-polls' ); ?></span></div>
					<?php } ?>
					<div class="wb_pollremove dashicons dashicons-trash" title="<?php esc_html_e( 'Remove', 'buddypress-polls' ); ?>"></div>
					<div class="wbpoll-toolbar-toggle dashicons dashicons-arrow-down-alt2" title="<?php esc_html_e( 'Toggle', 'buddypress-polls' ); ?>"></div>

				</div><?php // close - .wbpoll-containable-list-item-toolbar. ?>

				<div class="wbpoll-containable-list-item-editor hidetab wb-hide-<?php echo esc_attr( $index ); ?>">
					<div class="wbpoll-options-input-container">
						<div class="left image-section wbpoll-image-input-preview">

						<?php if ( isset( $full_size_image ) && ! empty( $full_size_image ) ) { ?>
								<div class="wbpoll-image-input-preview-thumbnail image_wbpoll_full_size_image_answer-<?php echo esc_attr( $index ); ?>" ><img src="<?php echo esc_url( $full_size_image ); ?>" controls="" preload="none"></div>
						<?php } else { ?>
								<div class="wbpoll-image-input-preview-thumbnail image_wbpoll_full_size_image_answer-<?php echo esc_attr( $index ); ?>" ></div>
						<?php } ?>
						</div>

						<div class="right wbpoll-image-input-details">
							<div class="wbpoll-input-group">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Label', 'buddypress-polls' ); ?></label>
								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_answer[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $answers_title ); ?>"  placeholder="Label" id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" class="wbpoll_answer"/>
							</div>

							<div class="wbpoll-input-group with-button">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Image URL', 'buddypress-polls' ); ?></label>
								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_full_size_image_answer[<?php echo esc_attr( $index ); ?>]"  placeholder="Full Size image url"  id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $full_size_image ); ?>" class="image_url wbpoll_answer wbpoll_full_size_image_answer-<?php echo esc_attr( $index ); ?>"  data-text="wbpoll_full_size_image_answer-<?php echo esc_attr( $index ); ?>"/>
								<input type="button" class="button" value="Upload" id="upload-btn" data-text="wbpoll_full_size_image_answer-<?php echo esc_attr( $index ); ?>"/>
							</div>

						</div>

						<input type="hidden" id="wbpoll_answer_extra_type_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $answer_type ); ?>" name="_wbpoll_answer_extra[<?php echo esc_attr( $index ); ?>][type]" />

						<?php do_action( 'wbpoll_answer_extra_fields', $index, $answers_extra, $is_voted, $poll_postid ); ?>

					</div><?php // close - .wbpoll-options-input-container. ?>
				</div><?php // close - .wbpoll-containable-list-item-editor. ?>

				<div class="clear clearfix"></div>
			</li>
			<?php
		} elseif ( isset( $answers_extra['type'] ) && $answers_extra['type'] == 'video' ) {

			$answer_type = isset( $answers_extra['type'] ) ? $answers_extra['type'] : 'video';
			?>
			<li class="wb_poll_items" id="wb-poll-answer-<?php echo esc_attr( $index ); ?>" data-type="video">

				<div class="wbpoll-containable-list-item-toolbar toolbar-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>">
					<div class="wb_pollmove"><i title="<?php esc_html_e( 'Drag and Drop to reorder poll answers', 'buddypress-polls' ); ?>" class="cbpollmoveicon"><?php esc_html_e( 'Move', 'buddypress-polls' ); ?></i></div>
					<div class="wbpoll-containable-list-item-toolbar-collapse-text"><span><?php echo esc_html( $number ); ?></span></div>
					<div class="wbpoll-containable-list-item-toolbar-preview-text"><span><?php echo esc_html( $answers_title ); ?></span></div>
					<?php if ( $answers_extra['type'] == 'video' ) { ?>
						<div class="wbpoll-containable-list-item-toolbar-preview-type"><span><?php esc_html_e( 'Video', 'buddypress-polls' ); ?></span></div>
					<?php } ?>
					<div class="wb_pollremove dashicons dashicons-trash" title="<?php esc_html_e( 'Remove', 'buddypress-polls' ); ?>"></div>
					<div class="wbpoll-toolbar-toggle dashicons dashicons-arrow-down-alt2" title="<?php esc_html_e( 'Toggle', 'buddypress-polls' ); ?>"></div>
				</div><?php // close - .wbpoll-containable-list-item-toolbar. ?>

				<div class="wbpoll-containable-list-item-editor hidetab wb-hide-<?php echo esc_attr( $index ); ?>">
					<div class="wbpoll-options-input-container">
						<div class="left video-section wbpoll-image-input-preview">
							<?php
							if ( isset( $video_url ) && ! empty( $video_url ) ) {

								if ( isset( $iframe_video_url ) && $iframe_video_url == 'yes' ) {
									?>
									<div class="wbpoll-image-input-preview-thumbnail video_wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"><iframe width="420" height="345" src="<?php echo esc_url( $video_url ); ?>"></iframe></div>
								} else {
									<div class="wbpoll-image-input-preview-thumbnail video_wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"><video src="<?php echo esc_url( $video_url ); ?>" controls="" poster="" preload="none"></video></div>
									<?php
								}
							} else {
								?>
								<div class="wbpoll-image-input-preview-thumbnail video_wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"></div>
							<?php } ?>

						</div>

						<div class="right wbpoll-image-input-details">

							<div class="wbpoll-input-group">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Label', 'buddypress-polls' ); ?></label>
								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_answer[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $answers_title ); ?>"  placeholder="Label" id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" class="wbpoll_answer"/>
							</div>

							<div class="wbpoll-input-group with-button">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Video URL', 'buddypress-polls' ); ?></label>
								<input type="hidden" name="_wbpoll_video_answer_url[<?php echo esc_attr( $index ); ?>]"  id="wbpoll_answer-url-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_url( $video_url ); ?>" class="wbpoll_answer wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"/>

								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" placeholder="Full Size Video URL"  id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $video_url ); ?>" class="video_url wbpoll_answer wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"/>

								<input type="button" class="button" value="Upload" id="upload-btn-video" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_video_answer_url-<?php echo esc_attr( $index ); ?>"/>
								<div class="error-<?php echo esc_attr( $index ); ?>"></div>
								<?php if ( isset( $iframe_video_url ) && $iframe_video_url == 'yes' ) { ?>
									<div class="wbpoll-input-group-suggestions hide_suggestion-<?php echo esc_attr( $index ); ?>" style="display:none;">
										<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
										<input type="radio" class="yes_video" name="_wbpoll_video_import_info[<?php echo esc_attr( $index ); ?>]" value="yes" data-id="<?php echo esc_attr( $index ); ?>" checked><label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
										<input type="radio" id="no" name="_wbpoll_video_import_info[<?php echo esc_attr( $index ); ?>]" value="no" data-id="<?php echo esc_attr( $index ); ?>" ><label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
										<br>
									</div>
								<?php } else { ?>
									<div class="wbpoll-input-group-suggestions hide_suggestion-<?php echo esc_attr( $index ); ?>" style="display:none;">
										<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
										<input type="radio" class="yes_video" name="_wbpoll_video_import_info[<?php echo esc_attr( $index ); ?>]" value="yes" data-id="<?php echo esc_attr( $index ); ?>"><label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
										<input type="radio" id="no" name="_wbpoll_video_import_info[<?php echo esc_attr( $index ); ?>]" value="no" data-id="<?php echo esc_attr( $index ); ?>" checked><label for="no"><?php esc_html__( 'No', 'buddypress-polls' ); ?></label>
										<br>
									</div>
								<?php } ?>

							</div>

						</div>

						<input type="hidden" id="wbpoll_answer_extra_type_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $answer_type ); ?>" name="_wbpoll_answer_extra[<?php echo esc_attr( $index ); ?>][type]" />

						<?php do_action( 'wbpoll_answer_extra_fields', $index, $answers_extra, $is_voted, $poll_postid ); ?>

					</div><?php // close - .wbpoll-options-input-container. ?>
				</div><?php // close - .wbpoll-containable-list-item-editor. ?>

				<div class="clear clearfix"></div>
			</li>
			<?php

		} elseif ( isset( $answers_extra['type'] ) && $answers_extra['type'] == 'audio' ) {

			$answer_type = isset( $answers_extra['type'] ) ? $answers_extra['type'] : 'audio';
			?>
			<li class="wb_poll_items" id="wb-poll-answer-<?php echo esc_attr( $index ); ?>" data-type="audio">

				<div class="wbpoll-containable-list-item-toolbar toolbar-<?php echo esc_attr( $index ); ?>"  data-id="<?php echo esc_attr( $index ); ?>">
					<div class="wb_pollmove"><i title="<?php esc_html_e( 'Drag and Drop to reorder poll answers', 'buddypress-polls' ); ?>" class="cbpollmoveicon"><?php esc_html_e( 'Move', 'buddypress-polls' ); ?></i></div>
					<div class="wbpoll-containable-list-item-toolbar-collapse-text"><span><?php echo esc_html( $number ); ?></span></div>
					<div class="wbpoll-containable-list-item-toolbar-preview-text"><span><?php echo esc_html( $answers_title ); ?></span></div>
					<?php if ( $answers_extra['type'] == 'audio' ) { ?>
						<div class="wbpoll-containable-list-item-toolbar-preview-type"><span><?php esc_html_e( 'Audio', 'buddypress-polls' ); ?></span></div>
					<?php } ?>
					<div class="wb_pollremove dashicons dashicons-trash" title="<?php esc_html_e( 'Remove', 'buddypress-polls' ); ?>"></div>
					<div class="wbpoll-toolbar-toggle dashicons dashicons-arrow-down-alt2" title="<?php esc_html_e( 'Toggle', 'buddypress-polls' ); ?>"></div>
				</div><?php // close - .wbpoll-containable-list-item-toolbar. ?>

				<div class="wbpoll-containable-list-item-editor hidetab wb-hide-<?php echo esc_attr( $index ); ?>">
					<div class="wbpoll-options-input-container">
						<div class="left audio-section wbpoll-image-input-preview">

							<?php
							if ( isset( $audio_url ) && ! empty( $audio_url ) ) {
								if ( isset( $iframe_audio_url ) && $iframe_audio_url == 'yes' ) {
									?>
									<div class="wbpoll-image-input-preview-thumbnail audio_wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>" ><iframe width="420" height="345" src="<?php echo esc_url( $audio_url ); ?>"></iframe></div>
								<?php } else { ?>
									<div class="wbpoll-image-input-preview-thumbnail audio_wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>" ><audio src="<?php echo esc_url( $audio_url ); ?>" controls="" preload="none"></audio></div>
									<?php
								}
							} else {
								?>
								<div class="wbpoll-image-input-preview-thumbnail audio_wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>" ></div>
							<?php } ?>

						</div>

						<div class="right wbpoll-image-input-details">

							<div class="wbpoll-input-group">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Label', 'buddypress-polls' ); ?></label>
								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_answer[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $answers_title ); ?>"  placeholder="Label" id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" class="wbpoll_answer"/>
							</div>

							<div class="wbpoll-input-group with-button">
								<label for="wbpoll_answer-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Audio URL', 'buddypress-polls' ); ?></label>
								<input type="hidden" style="width:330px;" name="_wbpoll_audio_answer_url[<?php echo esc_attr( $index ); ?>]"  placeholder="Full Size Audio URL"  id="wbpoll_answer-url-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_url( $audio_url ); ?>" class="wbpoll_answer wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>"/>
								<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;"  id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" value="<?php echo esc_url( $audio_url ); ?>" class="audio_url wbpoll_answer wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>"/>

								<input type="button" class="button upload-btn-audio" value="Upload" id="upload-audio-btn" data-id="<?php echo esc_attr( $index ); ?>" data-text="wbpoll_audio_answer_url-<?php echo esc_attr( $index ); ?>"/>
								<div class="error-<?php echo esc_attr( $index ); ?>"></div>

								<?php if ( isset( $iframe_audio_url ) && $iframe_audio_url == 'yes' ) { ?>
									<div class="wbpoll-input-group-suggestions hide_suggestion-<?php echo esc_attr( $index ); ?>" style="display:none;">
										<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
										<input type="radio" class="yes_audio" name="_wbpoll_audio_import_info[<?php echo esc_attr( $index ); ?>]" value="yes" data-id="<?php echo esc_attr( $index ); ?>" checked><label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
										<input type="radio" id="no" name="_wbpoll_audio_import_info[<?php echo esc_attr( $index ); ?>]" value="no" data-id="<?php echo esc_attr( $index ); ?>"><label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
										<br>
									</div>
								<?php } else { ?>
									<div class="wbpoll-input-group-suggestions hide_suggestion-<?php echo esc_attr( $index ); ?>" style="display:none;">
										<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
										<input type="radio" class="yes_audio" name="_wbpoll_audio_import_info[<?php echo esc_attr( $index ); ?>]" value="yes" data-id="<?php echo esc_attr( $index ); ?>"><label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
										<input type="radio" id="no" name="_wbpoll_audio_import_info[<?php echo esc_attr( $index ); ?>]" value="no" data-id="<?php echo esc_attr( $index ); ?>" checked><label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
										<br>
									</div>
								<?php } ?>
							</div>

						</div>

						<input type="hidden" id="wbpoll_answer_extra_type_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $answer_type ); ?>" name="_wbpoll_answer_extra[<?php echo esc_attr( $index ); ?>][type]" />

						<?php do_action( 'wbpoll_answer_extra_fields', $index, $answers_extra, $is_voted, $poll_postid ); ?>

					</div><?php // close - .wbpoll-options-input-container. ?>
				</div><?php // close - .wbpoll-containable-list-item-editor. ?>
				<div class="clear clearfix"></div>
			</li>
			<?php
		} elseif ( isset( $answers_extra['type'] ) && $answers_extra['type'] == 'html' ) {

			$answer_type = isset( $answers_extra['type'] ) ? $answers_extra['type'] : 'html';
			?>
			<li class="wb_poll_items" id="wb-poll-answer-<?php echo esc_attr( $index ); ?>" data-type="html">

				<div class="wbpoll-containable-list-item-toolbar toolbar-<?php echo esc_attr( $index ); ?>"  data-id="<?php echo esc_attr( $index ); ?>">
					<div class="wb_pollmove">
						<i title="<?php esc_html_e( 'Drag and Drop to reorder poll answers', 'buddypress-polls' ); ?>" class="cbpollmoveicon"><?php esc_html_e( 'Move', 'buddypress-polls' ); ?></i>
					</div>
					<div class="wbpoll-containable-list-item-toolbar-collapse-text"><span><?php echo esc_html( $number ); ?></span></div>
					<div class="wbpoll-containable-list-item-toolbar-preview-text"><span><?php echo esc_html( $answers_title ); ?></span></div>

					<?php if ( $answers_extra['type'] == 'html' ) { ?>
						<div class="wbpoll-containable-list-item-toolbar-preview-type"><span><?php esc_html_e( 'HTML', 'buddypress-polls' ); ?></span></div>
					<?php } ?>

					<div class="wb_pollremove dashicons dashicons-trash" title="<?php esc_html_e( 'Remove', 'buddypress-polls' ); ?>"></div>
					<div class="wbpoll-toolbar-toggle dashicons dashicons-arrow-down-alt2" title="<?php esc_html_e( 'Toggle', 'buddypress-polls' ); ?>"></div>
				</div><?php // close - .wbpoll-containable-list-item-toolbar. ?>

				<div class="wbpoll-containable-list-item-editor wbpoll-containable-list-item-editor-text hidetab wb-hide-<?php echo esc_attr( $index ); ?>">
					<div class="wbpoll-options-input-container">
						<h4><?php esc_html_e( 'Add HTML Answer', 'buddypress-polls' ); ?></h4>

						<input type="<?php echo esc_attr( $input_type ); ?>" style="width:330px;" name="_wbpoll_answer[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $answers_title ); ?>"  placeholder="Label" id="wbpoll_answer-<?php echo esc_attr( $index ); ?>" class="wbpoll_answer"/>
						<br>
						<textarea name="_wbpoll_html_answer[<?php echo esc_attr( $index ); ?>]"  placeholder="Full HTML Data" class="tiny wbpoll_answer"/><?php echo esc_html( $html_code ); ?></textarea>

						<input type="hidden" id="wbpoll_answer_extra_type_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $answer_type ); ?>" name="_wbpoll_answer_extra[<?php echo esc_attr( $index ); ?>][type]" />

						<?php do_action( 'wbpoll_answer_extra_fields', $index, $answers_extra, $is_voted, $poll_postid ); ?>

					</div><?php // close - .wbpoll-options-input-container. ?>
				</div><?php // close - .wbpoll-containable-list-item-editor. ?>

				<div class="clear clearfix"></div>
			</li>
			<?php
		}
		return ob_get_clean();
	}//end wbpoll_answer_field_template()

	/**
	 * Get all votes of a user by various criteria
	 *
	 * @param  int    $user_id
	 * @param  string $orderby
	 * @param  string $order
	 * @param  int    $perpage
	 * @param  int    $page
	 * @param  string $status
	 *
	 * @return array|null|object
	 */
	public static function getAllVotesByUser(
		$user_id = 0,
		$orderby = 'id',
		$order = 'desc',
		$perpage = 20,
		$page = 1,
		$status = 'all'
	) {

		$user_id = intval( $user_id );
		$data    = array();
		if ( intval( $user_id ) == 0 ) {
			return $data;
		}

		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql_select = "SELECT * FROM $votes_name";

		$where_sql = '';

		if ( is_numeric( $status ) ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'published=%d', intval( $status ) );
		}

		if ( intval( $user_id ) > 0 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'user_id=%d', intval( $user_id ) );
		}

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}

		$limit_sql = '';

		if ( $perpage != -1 ) {
			$perpage     = intval( $perpage );
			$start_point = ( $page * $perpage ) - $perpage;
			$limit_sql  .= 'LIMIT';
			$limit_sql  .= ' ' . $start_point . ',';
			$limit_sql  .= ' ' . $perpage;
		}

		$sortingOrder = " ORDER BY $orderby $order ";

		$data = $wpdb->get_results( "$sql_select  WHERE  $where_sql $sortingOrder  $limit_sql", 'ARRAY_A' );

		return $data;
	}//end getAllVotesByUser()

	/**
	 * Get all votes by different criteria
	 *
	 * @param  string $orderby
	 * @param  string $order
	 * @param  int    $perpage
	 * @param  int    $page
	 * @param  int    $poll_id
	 * @param  string $status
	 * @param  int    $vote_id
	 *
	 * @return array|null|object
	 */
	public static function getAllVotes(
		$orderby = 'id',
		$order = 'desc',
		$perpage = 20,
		$page = 1,
		$poll_id = 0,
		$status = 'all',
		$vote_id = 0
	) {
		$poll_id = intval( $poll_id );
		$vote_id = intval( $vote_id );

		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql_select = "SELECT * FROM $votes_name";

		$where_sql = '';
		if ( $poll_id != 0 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'poll_id=%d', $poll_id );
		}

		if ( $vote_id > 0 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'id=%d', $vote_id );
		}

		if ( is_numeric( $status ) ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'published=%d', intval( $status ) );
		}

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}

		$limit_sql = '';

		if ( $perpage != -1 ) {
			$perpage     = intval( $perpage );
			$start_point = ( $page * $perpage ) - $perpage;
			$limit_sql  .= 'LIMIT';
			$limit_sql  .= ' ' . $start_point . ',';
			$limit_sql  .= ' ' . $perpage;
		}

		$sortingOrder = " ORDER BY $orderby $order ";

		$data = $wpdb->get_results( "$sql_select  WHERE  $where_sql $sortingOrder  $limit_sql", 'ARRAY_A' );

		return $data;
	}//end getAllVotes()


	/**
	 * Get total vote count based on multiple criteria
	 *
	 * @param  int    $poll_id
	 * @param  string $status
	 * @param  int    $vote_id
	 *
	 * @return null|string
	 */
	public static function getVoteCount( $poll_id = 0, $status = 'all', $vote_id = 0 ) {

		$poll_id = intval( $poll_id );
		$vote_id = intval( $vote_id );

		global $wpdb;
		$votes_name = self::wb_poll_table_name();

		$sql_select = "SELECT COUNT(*) FROM $votes_name";

		$where_sql = '';
		if ( $poll_id != 0 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'poll_id=%d', $poll_id );
		}

		if ( $vote_id > 0 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'id=%d', $vote_id );
		}

		if ( is_numeric( $status ) ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}
			$where_sql .= $wpdb->prepare( 'published=%d', intval( $status ) );
		}

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}

		$count = $wpdb->get_var( "$sql_select  WHERE  $where_sql" );

		return $count;
	}//end getVoteCount()

	/**
	 * Get single vote information usign vote id
	 *
	 * @param $vote_id
	 *
	 * @return array|null|object|void
	 */
	public static function getVoteInfo( $vote_id ) {
		global $wpdb;

		$votes_name = self::wb_poll_table_name();
		$sql        = $wpdb->prepare( "SELECT * FROM $votes_name WHERE id=%d ", intval( $vote_id ) );
		$log_info   = $wpdb->get_row( $sql, ARRAY_A );

		return $log_info;
	}//end getVoteInfo()

	/**
	 * Add utm params to any url
	 *
	 * @param  string $url
	 *
	 * @return string
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		$url = add_query_arg(
			array(
				'utm_source'   => 'plgsidebarinfo',
				'utm_medium'   => 'plgsidebar',
				'utm_campaign' => 'wpfreemium',
			),
			$url
		);

		return $url;
	}//end url_utmy()

	/**
	 * Random color
	 *
	 * https://thisinterestsme.com/random-rgb-hex-color-php/
	 *
	 * @return string[]
	 */
	public static function randomColor() {
		$result = array(
			'rgb' => '',
			'hex' => '',
		);
		foreach ( array( 'r', 'b', 'g' ) as $col ) {
			$rand = mt_rand( 0, 255 );
			// $result['rgb'][$col] = $rand;
			$dechex = dechex( $rand );
			if ( strlen( $dechex ) < 2 ) {
				$dechex = '0' . $dechex;
			}
			$result['hex'] .= $dechex;
		}

		return $result;
	}//end randomColor()

	/**
	 * Bookmark login form
	 *
	 * @since 1.2.4
	 *
	 * @return array
	 */
	public static function guest_login_forms() {
		$forms = array();

		$forms['wordpress'] = esc_html__( 'WordPress Core Login Form', 'buddypress-polls' );

		return apply_filters( 'wbpoll_guest_login_forms', $forms );
	}//end guest_login_forms()

}//end class
