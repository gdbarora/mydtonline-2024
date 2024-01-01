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

class Pollrestapi {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'registerRoutes' ) );
	}

	/**
	 * Register routes.
	 */
	public function registerRoutes() {

		register_rest_route(
			'wbpoll/v1', '/postpoll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_wbpoll' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll listall poll
		register_rest_route(
			'wbpoll/v1', '/listpoll', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'list_all_poll' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll by poll-id
		register_rest_route(
			'wbpoll/v1', '/listpoll/id', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_by_id' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll by user
		register_rest_route(
			'wbpoll/v1', '/listpoll/user/id', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll pause by user
		register_rest_route(
			'wbpoll/v1', '/listpoll/pause/poll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_pause_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll delete by user
		register_rest_route(
			'wbpoll/v1', '/listpoll/delete/poll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_delete_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll unpublish by user
		register_rest_route(
			'wbpoll/v1', '/listpoll/unpublish/poll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_unpublish_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

		// wbpoll list poll unpublish by user
		register_rest_route(
			'wbpoll/v1', '/listpoll/publish/poll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_publish_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

		 // wbpoll list poll alll result by user id
		register_rest_route(
			'wbpoll/v1', '/listpoll/result/poll', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'listpoll_result_by_user' ),
				'permission_callback' => '__return_true',
			)
		);

	}

	// Callback function
	public function create_wbpoll( $request ) {

		$parameters = $request->get_params();
		$prefix     = '_wbpoll_';
		// Retrieve the post data from the request body
		$post_title   = sanitize_text_field( $parameters['title'] );
		$post_content = wp_kses_post( $parameters['content'] );
		$post_author  = $parameters['author_id'];

		$option_value = get_option( 'wbpolls_settings' );
		if ( ! empty( $option_value ) ) {
			$wbpolls_submit_status = $option_value['wbpolls_submit_status'];
		}
		// Create a new post with the retrieved data
		$updatepost_id = isset( $parameters['poll_id'] ) ? $parameters['poll_id'] : '';
		$post          = get_post( $updatepost_id );
		// Update the post data
		if ( $post ) {
			$updated_post = array(
				'ID'           => $updatepost_id,
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_author'  => $post_author,
			);
			$post_id      = wp_update_post( $updated_post );
		} else {
			$new_post = array(
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_status'  => $wbpolls_submit_status,
				'post_type'    => 'wbpoll',
				'post_author'  => $post_author,
			);
			$post_id  = wp_insert_post( $new_post );
		}

		 // option type (default, image, video, audio, html)
		if ( isset( $parameters[ $prefix . 'answer_extra' ] ) ) {

				$extra = [];
			foreach ( $parameters[ $prefix . 'answer_extra' ] as $key => $extra_type ) {
				if ( $extra_type == $parameters['poll_type'] ) {
					$extra[]['type'] = $extra_type;
				}
			}
				 update_post_meta( $post_id, $prefix . 'answer_extra', $extra );

		} else {
			delete_post_meta( $post_id, $prefix . 'answer_extra' );
		}

		// option lable
		if ( isset( $parameters[ $prefix . 'answer' ] ) ) {

			$titles = [];
			foreach ( $parameters[ $prefix . 'answer' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$titles[] = $extra_type;
				}
			}
			foreach ( $titles as $index => $title ) {
				$titles[ $index ] = $title;
			}
			update_post_meta( $post_id, $prefix . 'answer', $titles );

		} else {
			delete_post_meta( $post_id, $prefix . 'answer' );
		}

		// Full size image answer
		if ( isset( $parameters[ $prefix . 'full_size_image_answer' ] ) ) {

			$images = [];
			foreach ( $parameters[ $prefix . 'full_size_image_answer' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$images[] = $extra_type;
				}
			}

			foreach ( $images as $index => $url ) {
				$images[ $index ] = sanitize_text_field( $url );
			}

			update_post_meta( $post_id, $prefix . 'full_size_image_answer', $images );

		} else {
			delete_post_meta( $post_id, $prefix . 'full_size_image_answer' );
		}

		// video url
		if ( isset( $parameters[ $prefix . 'video_answer_url' ] ) ) {

			$videos = [];
			foreach ( $parameters[ $prefix . 'video_answer_url' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$videos[] = $extra_type;
				}
			}

			foreach ( $videos as $index => $url ) {
				$videos[ $index ] = $url;
			}

			update_post_meta( $post_id, $prefix . 'video_answer_url', $videos );

		} else {
			delete_post_meta( $post_id, $prefix . 'video_answer_url' );
		}

		// video suggestion
		if ( isset( $parameters[ $prefix . 'video_import_info' ] ) ) {

			$suggestion = [];
			foreach ( $parameters[ $prefix . 'video_import_info' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$suggestion[] = $extra_type;
				}
			}
			foreach ( $suggestion as $index => $text ) {
				$suggestion[ $index ] = $text;
			}

			update_post_meta( $post_id, $prefix . 'video_import_info', $suggestion );

		} else {
			delete_post_meta( $post_id, $prefix . 'video_import_info' );
		}

		// Audio url
		if ( isset( $parameters[ $prefix . 'audio_answer_url' ] ) ) {

			$audios = [];
			foreach ( $parameters[ $prefix . 'audio_answer_url' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$audios[] = $extra_type;
				}
			}

			foreach ( $audios as $index => $url ) {
				$audios[ $index ] = $url;
			}

			update_post_meta( $post_id, $prefix . 'audio_answer_url', $audios );

		} else {
			delete_post_meta( $post_id, $prefix . 'audio_answer_url' );
		}

		// audio suggestion
		if ( isset( $parameters[ $prefix . 'audio_import_info' ] ) ) {
			$suggestion = [];
			foreach ( $parameters[ $prefix . 'audio_import_info' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$suggestion[] = $extra_type;
				}
			}
			foreach ( $suggestion as $index => $text ) {
				$suggestion[ $index ] = $text;
			}

			update_post_meta( $post_id, $prefix . 'audio_import_info', $suggestion );

		} else {
			delete_post_meta( $post_id, $prefix . 'audio_import_info' );
		}

		// html content
		if ( isset( $parameters[ $prefix . 'html_answer' ] ) ) {
			$htmls = [];
			foreach ( $parameters[ $prefix . 'html_answer' ] as $extra_type ) {
				if ( ! empty( $extra_type ) ) {
					$htmls[] = $extra_type;
				}
			}

			foreach ( $htmls as $index => $html ) {
				$htmls[ $index ] = $html;
			}

			update_post_meta( $post_id, $prefix . 'html_answer', $htmls );

		} else {
			delete_post_meta( $post_id, $prefix . 'html_answer' );
		}

		// Start date meta
		if ( isset( $parameters[ $prefix . 'start_date' ] ) ) {
			$start_date = $parameters[ $prefix . 'start_date' ];
			update_post_meta( $post_id, $prefix . 'start_date', $start_date );
		} else {
			delete_post_meta( $post_id, $prefix . 'start_date' );
		}

		// poll type
		if ( isset( $parameters['poll_type'] ) ) {
			$poll_type = $parameters['poll_type'];
			update_post_meta( $post_id, 'poll_type', $poll_type );
		} else {
			delete_post_meta( $post_id, 'poll_type' );
		}
		// End date meta
		if ( isset( $parameters[ $prefix . 'end_date' ] ) ) {
			$end_date = $parameters[ $prefix . 'end_date' ];
			update_post_meta( $post_id, $prefix . 'end_date', $end_date );
		} else {
			delete_post_meta( $post_id, $prefix . 'end_date' );
		}

		// Who can vote meta
		if ( isset( $parameters[ $prefix . 'user_roles' ] ) ) {
			$user_roles = $parameters[ $prefix . 'user_roles' ];
			update_post_meta( $post_id, $prefix . 'user_roles', $user_roles );
		} else {
			delete_post_meta( $post_id, $prefix . 'user_roles' );
		}

		// show description meta
		$description = 1;
		if ( isset( $description ) ) {
			$content = $description;
			update_post_meta( $post_id, $prefix . 'content', $content );
		} else {
			delete_post_meta( $post_id, $prefix . 'content' );
		}

		// never expire meta
		if ( isset( $parameters[ $prefix . 'never_expire' ] ) ) {
			$never_expire = $parameters[ $prefix . 'never_expire' ];
			update_post_meta( $post_id, $prefix . 'never_expire', $never_expire );
		} else {
			delete_post_meta( $post_id, $prefix . 'never_expire' );
		}

		// show result after Expire meta
		if ( isset( $parameters[ $prefix . 'show_result_before_expire' ] ) ) {
			$show_result_before_expire = $parameters[ $prefix . 'show_result_before_expire' ];
			update_post_meta( $post_id, $prefix . 'show_result_before_expire', $show_result_before_expire );
		} else {
			delete_post_meta( $post_id, $prefix . 'show_result_before_expire' );
		}

		// multivote meta
		if ( isset( $parameters[ $prefix . 'multivote' ] ) ) {
			$multivote = $parameters[ $prefix . 'multivote' ];
			update_post_meta( $post_id, $prefix . 'multivote', $multivote );
		} else {
			delete_post_meta( $post_id, $prefix . 'multivote' );
		}

		 // add additional fields meta
		if ( isset( $parameters[ $prefix . 'add_additional_fields' ] ) ) {
			$multivote = $parameters[ $prefix . 'add_additional_fields' ];
			update_post_meta( $post_id, $prefix . 'add_additional_fields', $multivote );
		} else {
			delete_post_meta( $post_id, $prefix . 'add_additional_fields' );
		}
		// vote per session meta
		if ( isset( $parameters[ $prefix . 'vote_per_session' ] ) ) {
			$vote_per_session = $parameters[ $prefix . 'vote_per_session' ];
			update_post_meta( $post_id, $prefix . 'vote_per_session', $vote_per_session );
		} else {
			delete_post_meta( $post_id, $prefix . 'vote_per_session' );
		}

		if ( empty( trim( $updatepost_id ) ) || trim( $updatepost_id ) == '' ) {

			$type = $wbpolls_submit_status;

			// Return the response data
			if ( $type == 'publish' ) {
				$page = get_post( $post_id );
				if ( $page ) {
					$page_slug = $page->post_name;
				}
				$data = array(
					'success' => true,
					'message' => esc_html__( 'Your poll is published.', 'buddypress-polls' ),
					'post_id' => $post_id,
					'url'     => site_url() . '/poll/' . $page_slug,
				);
			} else {

				$notification = get_option( 'wbpolls_notification_settings' );

				if ( isset( $notification['wppolls_enable_notification'] ) && $notification['wppolls_enable_notification'] == 'yes' && isset( $notification['wppolls_admin_notification'] ) && $notification['wppolls_admin_notification'] == 'yes' ) {
					$send_admin_notification = self::send_admin_notifications( $post_id );
				}

				$option_value        = get_option( 'wbpolls_settings' );
				$poll_dashboard_page = isset( $option_value['poll_dashboard_page'] ) ? $option_value['poll_dashboard_page'] : '';

				$page = get_post( $poll_dashboard_page );
				if ( $page ) {
					$page_slug = $page->post_name;
				}
				$data = array(
					'success' => true,
					'message' => esc_html__( 'Your poll is in ' . $type . '. It will be published after admin review', 'buddypress-polls' ),
					'post_id' => $post_id,
					'url'     => site_url() . '/' . $page_slug,
				);
			}
		} else {

			$option_value        = get_option( 'wbpolls_settings' );
			$poll_dashboard_page = isset( $option_value['poll_dashboard_page'] ) ? $option_value['poll_dashboard_page'] : '';

			$page = get_post( $poll_dashboard_page );
			if ( $page ) {
				$page_slug = $page->post_name;
			}
			$data = array(
				'success' => true,
				'message' => esc_html__( 'Your poll update successfully', 'buddypress-polls' ),
				'post_id' => $post_id,
				'url'     => site_url() . '/' . $page_slug,
			);

		}

		update_option( 'permalink_structure', '/%postname%/' );
		return rest_ensure_response( $data );
	}


	public function send_admin_notifications( $post_id ) {

		$option_value  = get_option( 'wbpolls_notification_setting_options' );
		$option_admins = get_option( 'wbpolls_notification_settings' );
		$author_id     = $post->post_author;
		$admin_users   = $option_admins['wppolls_admin_user'];
		$subject       = isset( $option_value['admin']['notification_subject'] ) ? self::bpmbp_get_admin_notification_subject( $option_value['admin']['notification_subject'], $post_id, $author_id ) : '';
		$headers[]     = 'Content-Type: text/html; charset=UTF-8';
		$to            = array();

		if ( ! empty( $admin_users ) ) {
			// Add BuddyPress Notification First
			foreach ( $admin_users as $admin_user ) {
				$admin_user_info = get_userdata( $admin_user );
				$content         = isset( $option_value['admin']['notification_content'] ) ? self::bpmbp_get_notification_admin_content( $option_value['admin']['notification_content'], $post_id, $admin_user ) : '';
				wp_mail( $admin_user_info->user_email, $subject, $content, $headers );
			}
		}

	}


	public static function bpmbp_get_admin_notification_subject( $notification_subject, $post_id, $user_id = null ) {

		$subject = '';
		if ( isset( $notification_subject ) && ! empty( $notification_subject ) ) {
			$subject = $notification_subject;

			if ( strpos( $subject, '{site_name}' ) !== false ) {
				$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
			}
		}

		return apply_filters( 'bpmbp_notification_subject', $subject, $notification_subject, $poll_post );
	}


	public static function bpmbp_get_notification_admin_content( $notification_content, $post_id, $user_id = null ) {

		$content = '';
		if ( isset( $notification_content ) && ! empty( $notification_content ) ) {
			$content = $notification_content;

			if ( strpos( $content, '{site_name}' ) !== false ) {
				$content = str_replace( '{site_name}', get_bloginfo( 'name' ), $content );
			}

			if ( strpos( $content, '{poll_name}' ) !== false ) {
				$poll_post  = get_post( $post_id );
				$poll_title = '<a href="' . get_the_permalink( $poll_post ) . '">' . $poll_post->post_title . '</a>';
				$content    = str_replace( '{poll_name}', $poll_title, $content );
			}

			if ( strpos( $content, '{publisher_name}' ) !== false ) {
				$user    = get_userdata( $poll_post->post_author );
				$content = str_replace( '{publisher_name}', $user->display_name, $content );
			}

			if ( strpos( $content, '{site_admin}' ) !== false ) {
				if ( ! empty( $user_id ) && null !== $user_id ) {
					$user    = get_userdata( $user_id );
					$content = str_replace( '{site_admin}', $user->display_name, $content );
				}
			}
		}
		return $content;
	}



	// Callback function
	public function list_all_poll( $request ) {

		$args  = array(
			'post_type'      => 'wbpoll',
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		// Format the response data
		$data = array();
		foreach ( $posts as $post ) {

			$post_id = $post->ID;

			$meta_value_ans = get_post_meta( $post_id, '_wbpoll_answer', true );

			$image_answer_url = get_post_meta( $post_id, '_wbpoll_full_size_image_answer', true );
			$image_answer_url = isset( $image_answer_url ) ? $image_answer_url : array();

			$video_answer_url = get_post_meta( $post_id, '_wbpoll_video_answer_url', true );
			$video_answer_url = isset( $video_answer_url ) ? $video_answer_url : array();

			$audio_answer_url = get_post_meta( $post_id, '_wbpoll_audio_answer_url', true );
			$audio_answer_url = isset( $audio_answer_url ) ? $audio_answer_url : array();

			$html_content = get_post_meta( $post_id, '_wbpoll_html_answer', true );
			$html_content = isset( $html_content ) ? $html_content : array();

			$options_data = [];
			foreach ( $meta_value_ans as $key => $meta_value ) {

				$options_data[ $key ]['lable'] = $meta_value;

				if ( isset( $image_answer_url[ $key ] ) && ! empty( $image_answer_url[ $key ] ) ) {
					$options_data[ $key ]['image'] = $image_answer_url[ $key ];
				}

				if ( isset( $video_answer_url[ $key ] ) && ! empty( $video_answer_url[ $key ] ) ) {
					$options_data[ $key ]['video'] = $video_answer_url[ $key ];
				}

				if ( isset( $audio_answer_url[ $key ] ) && ! empty( $audio_answer_url[ $key ] ) ) {
					$options_data[ $key ]['audio'] = $audio_answer_url[ $key ];
				}

				if ( isset( $html_content[ $key ] ) && ! empty( $html_content[ $key ] ) ) {
					$options_data[ $key ]['html'] = $html_content[ $key ];
				}
			}
			$data[] = array(

				'id'                       => $post->ID,
				'title'                    => $post->post_title,
				'content'                  => $post->post_content,
				'date'                     => $post->post_date,
				'options'                  => $options_data,
				'start_time'               => get_post_meta( $post_id, '_wbpoll_start_date', true ),
				'end_date'                 => get_post_meta( $post_id, '_wbpoll_end_date', true ),
				'user_role'                => get_post_meta( $post_id, '_wbpoll_user_roles', true ),
				'show_description'         => get_post_meta( $post_id, '_wbpoll_content', true ),
				'never_expire'             => get_post_meta( $post_id, '_wbpoll_never_expire', true ),
				'show_result_after_expire' => get_post_meta( $post_id, '_wbpoll_show_result_before_expire', true ),
				'multivote'                => get_post_meta( $post_id, '_wbpoll_multivote', true ),
				'vote_per_session'         => get_post_meta( $post_id, '_wbpoll_vote_per_session', true ),
				'result'                   => WBPollHelper::show_backend_single_poll_result( $post_id, 'shortcode', 'text' ),
			);
		}

		// Return the response data
		return rest_ensure_response( $data );
	}

	public function listpoll_by_id( $request ) {

		$post_id = $request['id'];
		$post    = get_post( $post_id );

		// If post not found, return a 404 error
		if ( empty( $post ) || is_wp_error( $post ) ) {
			return new WP_Error( '404', 'Post not found', array( 'status' => 404 ) );
		}

		// Format the response data
		$data = array(
			'id'             => $post->ID,
			'title'          => $post->post_title,
			'content'        => $post->post_content,
			'date'           => $post->post_date,
			'featured_image' => get_the_post_thumbnail( $post_id, 'large' ),
		);

		$meta_value_ans = get_post_meta( $post_id, '_wbpoll_answer', true );

		$image_answer_url = get_post_meta( $post_id, '_wbpoll_full_size_image_answer', true );
		$image_answer_url = isset( $image_answer_url ) ? $image_answer_url : array();

		$video_answer_url = get_post_meta( $post_id, '_wbpoll_video_answer_url', true );
		$video_answer_url = isset( $video_answer_url ) ? $video_answer_url : array();

		$audio_answer_url = get_post_meta( $post_id, '_wbpoll_audio_answer_url', true );
		$audio_answer_url = isset( $audio_answer_url ) ? $audio_answer_url : array();

		$html_content = get_post_meta( $post_id, '_wbpoll_html_answer', true );
		$html_content = isset( $html_content ) ? $html_content : array();

		$options_data = [];
		foreach ( $meta_value_ans as $key => $meta_value ) {

			$options_data[ $key ]['lable'] = $meta_value;
			if ( isset( $image_answer_url[ $key ] ) && ! empty( $image_answer_url[ $key ] ) ) {
				$options_data[ $key ]['image'] = $image_answer_url[ $key ];
			}

			if ( isset( $video_answer_url[ $key ] ) && ! empty( $video_answer_url[ $key ] ) ) {
				$options_data[ $key ]['video'] = $video_answer_url[ $key ];
			}

			if ( isset( $audio_answer_url[ $key ] ) && ! empty( $audio_answer_url[ $key ] ) ) {
				$options_data[ $key ]['audio'] = $audio_answer_url[ $key ];
			}

			if ( isset( $html_content[ $key ] ) && ! empty( $html_content[ $key ] ) ) {
				$options_data[ $key ]['html'] = $html_content[ $key ];
			}
		}
		$data['options']                  = $options_data;
		$data['start_time']               = get_post_meta( $post_id, '_wbpoll_start_date', true );
		$data['end_date']                 = get_post_meta( $post_id, '_wbpoll_end_date', true );
		$data['user_role']                = get_post_meta( $post_id, '_wbpoll_user_roles', true );
		$data['show_description']         = get_post_meta( $post_id, '_wbpoll_content', true );
		$data['never_expire']             = get_post_meta( $post_id, '_wbpoll_never_expire', true );
		$data['show_result_after_expire'] = get_post_meta( $post_id, '_wbpoll_show_result_before_expire', true );
		$data['multivote']                = get_post_meta( $post_id, '_wbpoll_multivote', true );
		$data['vote_per_session']         = get_post_meta( $post_id, '_wbpoll_vote_per_session', true );
		$data['result']                   = WBPollHelper::show_backend_single_poll_result( $post_id, 'shortcode', 'text' );

		// Return the response data
		return rest_ensure_response( $data );
	}

	// Callback function
	public function listpoll_by_user( $request ) {

		$author_id = $request['id'];
		$status    = $request['status'];
		$args      = array(
			'author'         => $author_id,
			'post_type'      => 'wbpoll',
			'posts_per_page' => -1,
			'post_status'    => $status,
		);

		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		// If no posts found, return a 404 error
		if ( empty( $posts ) ) {
			return new WP_Error( '404', 'No posts found', array( 'status' => 404 ) );
		}

		// Format the response data
		$data = array();
		foreach ( $posts as $post ) {
			$post_id = $post->ID;

			$data[] = array(
				'id'           => $post->ID,
				'title'        => $post->post_title,
				'slug'         => $post->post_name,
				'content'      => $post->post_content,
				'date'         => $post->post_date,
				'status'       => $post->post_status,
				'start_time'   => get_post_meta( $post_id, '_wbpoll_start_date', true ),
				'end_date'     => get_post_meta( $post_id, '_wbpoll_end_date', true ),
				'never_expire' => get_post_meta( $post_id, '_wbpoll_never_expire', true ),
				'totalvote'    => WBPollHelper::getVoteCount( $post_id ),
				'pausetype'    => get_post_meta( $post_id, '_wbpoll_pause_poll', true ),
			);

		}

		// Return the response data
		return rest_ensure_response( $data );

	}

	public function listpoll_pause_by_user( $request ) {
		$parameters = $request->get_params();
		$prefix     = '_wbpoll_';
		// Retrieve the post data from the request body
		$pollid = sanitize_text_field( $parameters['pollid'] );

		// Who can vote meta
		if ( isset( $parameters[ $prefix . 'pause_poll' ] ) ) {
			$pause_poll = $parameters[ $prefix . 'pause_poll' ];
			update_post_meta( $pollid, $prefix . 'pause_poll', $pause_poll );
		} else {
			delete_post_meta( $pollid, $prefix . 'pause_poll' );
		}

		$data = array(
			'success' => esc_html__( 'Poll pause successfully.', 'buddypress-polls' ),
			'post_id' => $pollid,
		);
		return rest_ensure_response( $data );
	}


	public function listpoll_delete_by_user( $request ) {
		$parameters = $request->get_params();
		$prefix     = '_wbpoll_';
		// Retrieve the post data from the request body
		$pollid = sanitize_text_field( $parameters['pollid'] );

		// Delete the post
		$result = wp_delete_post( $pollid, true );

		$data = array(
			'success' => esc_html__( 'Poll deleted successfully!', 'buddypress-polls' ),
			'post_id' => $pollid,
		);
		return rest_ensure_response( $data );
	}

	public function listpoll_unpublish_by_user( $request ) {
		$parameters = $request->get_params();
		$prefix     = '_wbpoll_';
		// Retrieve the post data from the request body
		$pollid = sanitize_text_field( $parameters['pollid'] );

		// Get the current post object
			$post = get_post( $pollid );

			// Check if the post is a poll and its status is 'publish'
		if ( $post && $post->post_type === 'wbpoll' && $post->post_status === 'publish' ) {
			// Set the new post status to 'draft'
			$updated_post = array(
				'ID'          => $pollid,
				'post_status' => 'draft',
			);

			// Update the post status
			wp_update_post( $updated_post );
		}

		$data = array(
			'success' => esc_html__( 'Poll unpublish successfully!', 'buddypress-polls' ),
			'post_id' => $pollid,
		);
		return rest_ensure_response( $data );
	}

	public function listpoll_publish_by_user( $request ) {
		$parameters = $request->get_params();
		$prefix     = '_wbpoll_';
		// Retrieve the post data from the request body
		$pollid = sanitize_text_field( $parameters['pollid'] );

		// Get the current post object
			$post = get_post( $pollid );

			// Check if the post is a poll and its status is 'publish'
		if ( $post && $post->post_type === 'wbpoll' && $post->post_status === 'draft' ) {
			// Set the new post status to 'draft'
			$updated_post = array(
				'ID'          => $pollid,
				'post_status' => 'publish',
			);

			// Update the post status
			wp_update_post( $updated_post );
		}

		$data = array(
			'success' => esc_html__( 'Poll publish successfully!', 'buddypress-polls' ),
			'post_id' => $pollid,
		);
		return rest_ensure_response( $data );
	}

	public function listpoll_result_by_user( $request ) {
		$parameters = $request->get_params();
		$pollid     = sanitize_text_field( $parameters['pollid'] );
		$result     = WBPollHelper::show_backend_single_poll_widget_result( esc_html( $pollid ), 'shortcode', 'text' );
		$data       = array(
			'success' => esc_html__( 'Poll report', 'buddypress-polls' ),
			'result'  => $result,
		);
		return rest_ensure_response( $data );
	}

}
$custom_endpoint = new Pollrestapi();

