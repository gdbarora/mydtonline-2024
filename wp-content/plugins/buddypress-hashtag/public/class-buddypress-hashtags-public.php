<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Hashtags_Public {

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
		 * defined in Buddypress_Hashtags_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Hashtags_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-hashtags-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-tagify', plugin_dir_url( __FILE__ ) . 'css/tagify.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$bpht_general_settings = get_option( 'bpht_general_settings' );
		$minlen                = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : 3;
		$maxlen                = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : 16;

		wp_enqueue_script( $this->plugin_name . '-tagify', plugin_dir_url( __FILE__ ) . 'js/vendor/tagify/tagify.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-hashtags-public.js', array( 'jquery', $this->plugin_name . '-tagify' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'bpht_ajax_object',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'bpht_ajax_security' ),
				'minlen'     => $minlen,
				'maxlen'     => $maxlen,
			)
		);
	}

	public function bpht_activity_comment_hashtags_filter( $content, $type ) {
		global $bp;
		$bpht_general_settings = get_option( 'bpht_general_settings' );
		$minlen                = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : 3;
		$maxlen                = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : 16;

		$pattern = '/[#]([\p{L}_0-9a-zA-Z-]{' . $minlen . ',' . $maxlen . '})/iu';

		$an_enabled = bpht_alpha_numeric_hashtags_enabled();

		if ( $an_enabled ) {
			// $pattern = " /#(\S{1,})/u";
			$pattern = ' /(?<!\S)#(\S{1,})/u';
			$content = str_replace( array( '<p>', '</p>' ), array( '<p> ', ' </p>' ), $content );
		}

		$hashtags_option = get_option( 'bpht_hashtags' );

		$old_activity_url = trailingslashit( get_bloginfo( 'url' ) ) . bp_get_activity_slug();
		$activity_url     = site_url( bp_get_activity_root_slug() );
		$hashtags         = array();
		preg_match_all( $pattern, $content, $hashtags );

		if ( $hashtags ) {
			if ( ! $hashtags = array_unique( $hashtags[1] ) ) {
				return $content;
			}

			add_filter( 'bp_bypass_check_for_moderation', '__return_true' );
			foreach ( (array) $hashtags as $hashtag ) {

				$pattern = "/(^|\s|\b)#" . $hashtag . "($|\b)/";

				if ( $an_enabled ) {
					$pattern = '/#' . $hashtag . '/u';
				}

				$content = preg_replace( $pattern, ' <a href="' . $activity_url . '/?activity_search=%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#' . htmlspecialchars( $hashtag ) . '</a>', str_replace( '<p>', '<p> ', $content ) );

				$old_url = $old_activity_url . '/?s=%23' . htmlspecialchars( $hashtag );
				$new_url = $activity_url . '/?activity_search=%23' . htmlspecialchars( $hashtag );
				$content = str_replace( array( $old_url, '?s=' ), array( $new_url, '?s=' ), $content );

				if ( $type == 'new' && current_action() == 'bp_activity_comment_content' ) {

					bpht_db_buddypress_hashtag_entry( $hashtag, 'buddypress' );
				}
			}
		}
		return $content;
	}

	public function bpht_activity_hashtags_filter( $content ) {
		global $bp;
		$bpht_general_settings = get_option( 'bpht_general_settings' );
		$minlen                = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : 3;
		$maxlen                = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : 16;

		$pattern = '/[#]([\p{L}_0-9a-zA-Z-]{' . $minlen . ',' . $maxlen . '})/iu';

		$an_enabled = bpht_alpha_numeric_hashtags_enabled();

		if ( $an_enabled ) {
			// $pattern = " /#(\S{1,})/u";
			$pattern = ' /(?<!\S)#(\S{1,})/u';
			$content = str_replace( array( '<p>', '</p>' ), array( '<p> ', ' </p>' ), $content );
		}

		$hashtags_option = get_option( 'bpht_hashtags' );
		$old_activity_url = trailingslashit( get_bloginfo( 'url' ) ) . bp_get_activity_slug();
		$activity_url     = site_url( bp_get_activity_root_slug() );
		$hashtags         = array();
		preg_match_all( $pattern, $content, $hashtags );

		if ( $hashtags ) {
			if ( ! $hashtags = array_unique( $hashtags[1] ) ) {
				return $content;
			}

			add_filter( 'bp_bypass_check_for_moderation', '__return_true' );
			foreach ( (array) $hashtags as $hashtag ) {

				$pattern = "/(^|\s|\b)#" . $hashtag . "($|\b)/";

				if ( $an_enabled ) {
					$pattern = '/#' . $hashtag . '/u';
				}

				if ( strpos( $content, '=%23' . htmlspecialchars( $hashtag ) ) != '' ) {
					continue;
				}

				$content = preg_replace( $pattern, ' <a href="' . $activity_url . '/?activity_search=%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#' . htmlspecialchars( $hashtag ) . '</a>', str_replace( '<p>', '<p> ', $content ) );

				$old_url = $old_activity_url . '/?s=%23' . htmlspecialchars( $hashtag );
				$new_url = $activity_url . '/?activity_search=%23' . htmlspecialchars( $hashtag );
				$content = str_replace( array( $old_url, '?s=' ), array( $new_url, '?s=' ), $content );

				if ( current_action() == 'bp_activity_new_update_content' || current_action() == 'groups_activity_new_update_content' ) {

					bpht_db_buddypress_hashtag_entry( $hashtag, 'buddypress' );
				}
			}
		}
		
		return $content;
	}

	public function bpht_bbpress_hashtags_filter( $content ) {
		global $bp, $wpdb;

		$get_id  = $wpdb->get_row( "SHOW TABLE STATUS LIKE '{$wpdb->prefix}posts'" );
		$post_id = $get_id->Auto_increment;

		$bpht_general_settings = get_option( 'bpht_general_settings' );
		$minlen                = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : 3;
		$maxlen                = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : 16;

		$forum_slug  = get_option( '_bbp_root_slug' );
		$search_slug = get_option( '_bbp_search_slug' );
		$search_slug = ( $search_slug == '' ) ? 'search' : $search_slug;

		$pattern    = '/[#]([\p{L}_0-9a-zA-Z-]{' . $minlen . ',' . $maxlen . '})/iu';
		$an_enabled = bpht_alpha_numeric_hashtags_enabled();

		if ( $an_enabled ) {
			// $pattern = " /#(\S{1,})/u";
			$pattern = ' /(?<!\S)#(\S{1,})/u';
			$content = str_replace( array( '<p>', '</p>' ), array( '<p> ', ' </p>' ), $content );
		}

		$hashtags_option = get_option( 'bpht_bbpress_hashtags' );

		$site_url = trailingslashit( get_bloginfo( 'url' ) );
		$hashtags = array();
		preg_match_all( $pattern, $content, $hashtags );
		if ( $hashtags ) {
			if ( ! $hashtags = array_unique( $hashtags[1] ) ) {
				return $content;
			}

			foreach ( (array) $hashtags as $hashtag ) {
				$pattern = "/(^|\s|\b)#" . $hashtag . "($|\b)/";
				if ( $an_enabled ) {
					$pattern = '/#' . $hashtag . '/u';
				}
				if ( strpos( $content, '/%23' . htmlspecialchars( $hashtag ) ) != '' ) {
					continue;
				}
				$content = preg_replace( $pattern, ' <a href="' . $site_url . $forum_slug . '/' . $search_slug . '/%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#' . htmlspecialchars( $hashtag ) . '</a>', str_replace( '<p>', '<p> ', $content ) );

				bpht_db_buddypress_hashtag_entry( $hashtag, 'bbpress', $post_id );
			}
		}

		return $content;
	}

	public function bpht_bbpress_edit_hashtags_filter( $content, $post_id ) {
		global $bp;

		$bpht_general_settings = get_option( 'bpht_general_settings' );
		$minlen                = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : 3;
		$maxlen                = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : 16;

		$forum_slug  = get_option( '_bbp_root_slug' );
		$search_slug = get_option( '_bbp_search_slug' );
		$search_slug = ( $search_slug == '' ) ? 'search' : $search_slug;

		$pattern    = '/[#]([\p{L}_0-9a-zA-Z-]{' . $minlen . ',' . $maxlen . '})/iu';
		$an_enabled = bpht_alpha_numeric_hashtags_enabled();

		if ( $an_enabled ) {
			// $pattern = " /#(\S{1,})/u";
			$pattern = ' /(?<!\S)#(\S{1,})/u';
			$content = str_replace( array( '<p>', '</p>' ), array( '<p> ', ' </p>' ), $content );
		}

		$hashtags_option = get_option( 'bpht_bbpress_hashtags' );

		$site_url = trailingslashit( get_bloginfo( 'url' ) );
		$hashtags = array();
		preg_match_all( $pattern, $content, $hashtags );
		if ( $hashtags ) {
			if ( ! $hashtags = array_unique( $hashtags[1] ) ) {
				return $content;
			}

			foreach ( (array) $hashtags as $hashtag ) {
				$pattern = "/(^|\s|\b)#" . $hashtag . "($|\b)/";
				if ( $an_enabled ) {
					$pattern = '/#' . $hashtag . '/u';
				}
				if ( strpos( $content, '/%23' . htmlspecialchars( $hashtag ) ) != '' ) {
					continue;
				}
				$content = preg_replace( $pattern, ' <a href="' . $site_url . $forum_slug . '/' . $search_slug . '/%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#' . htmlspecialchars( $hashtag ) . '</a>', str_replace( '<p>', '<p> ', $content ) );

				bpht_db_buddypress_hashtag_entry( $hashtag, 'bbpress', $post_id );
			}
		}

		return $content;
	}

	public function bpht_bbp_get_hashtags_filter_content( $content ) {
		$content = str_replace( '/?bbp_search=', 'search/', $content );
		return $content;
	}

	public function bpht_activity_hashtags_querystring( $qs, $object ) {
		global $bp;

		if ( isset( $_POST['search_terms'] ) && $_POST['search_terms'] != '' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$qs .= '&display_comments=stream';
		}

		return $qs;
	}


	public function bpht_register_hashtag_widget() {
		register_widget( 'BPHT_Hashtag_Widget' );
		if ( class_exists( 'bbPress' ) ) {
			register_widget( 'BPHT_Bbpress_Hashtag_Widget' );
		}
		register_widget( 'BPHT_Hashtag_Post_Widget' );
		register_widget( 'BPHT_Hashtag_Page_Widget' );
	}

	public function bpht_render_buddypress_hashtags( $atts ) {

		// 0 - cloud    name   asc
		// 1 - list     size   desc

		$atts = shortcode_atts(
			array(
				'displaystyle' => 'cloud',
				'sortby'       => 'name',
				'sortorder'    => 'asc',
				'limit'        => '12',
			),
			$atts,
			'bpht_bp_hashtags'
		);

		if ( ! isset( $atts['sortby'] ) ) {
			$atts['sortby'] = 'asc';
		}

		if ( ! isset( $atts['sortorder'] ) ) {
			$atts['sortorder'] = 'name';
		}

		if ( ! isset( $atts['displaystyle'] ) ) {
			$atts['displaystyle'] = 'cloud';
		}
		ob_start();
		?>

		<div>
			<?php

			if ( ! isset( $atts['limit'] ) ) {
				$atts['limit'] = 12;
			}

			$limit = $atts['limit'];

			global $wpdb;
			$table_name = $wpdb->prefix . 'bpht_hashtags';
			$hashtags   = array();
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {
				$sql = "SELECT * FROM $table_name WHERE ht_type IN ('buddypress') ORDER BY ht_count DESC LIMIT $limit";

				/**
				 * Filters the query of the widget hashtag items.
				 *
				 * @param string $sql The SQL query
				 * @param string $type The type of the requested hashtags
				 * @param int    $limit How many rows to fetch
				 */
				$query     = apply_filters( 'wbcom_hashtag_widget_query', $sql, 'buddypress', $limit );
				$_hashtags = $wpdb->get_results( $query );
				if ( $_hashtags ) {
					foreach ( $_hashtags as $key => $ht_data ) {
						$hashtags[ $ht_data->ht_name ] = array(
							'ht_count'      => $ht_data->ht_count,
							'ht_last_count' => $ht_data->ht_last_count,
							'ht_type'       => $ht_data->ht_type,
						);
					}
				}
			}

			$max = 0;

			if ( count( $hashtags ) && is_array( $hashtags ) ) {
				$result = array();

				if ( 'name' == $atts['sortby'] ) {
					if ( 'asc' == $atts['sortorder'] ) {
						ksort( $hashtags );
					}

					if ( 'desc' == $atts['sortorder'] ) {
						krsort( $hashtags );
					}
				} elseif ( 'size' == $atts['sortby'] ) {
					if ( 'asc' == $atts['sortorder'] ) {
						asort( $hashtags );
					}

					if ( 'desc' == $atts['sortorder'] ) {
						arsort( $hashtags );
					}
				}

				/**
				 * Final filter before the hashtags array is displayed
				 *
				 * @param array  $hashtags The hashtags array
				 * @param string $type The type of the requested hashtags
				 * @param int    $limit How many rows to fetch
				 */
				$hashtags = apply_filters(
					'wbcom_hashtag_widget_array_hashtags',
					$hashtags,
					'buddypress',
					$limit
				);

				$wrapper = '';

				if ( 'list' == $atts['displaystyle'] ) {
					$wrapper = 'bpht-hashtags-wrapper-list';
				} elseif ( 'cloud' == $atts['displaystyle'] ) {
					$wrapper = 'bpht-hashtags-wrapper-cloud';
				}
				?>
				<div>
					<div class="bpht-widget--hashtags">
						<div class="bpht-hashtags-wrapper <?php echo esc_attr( $wrapper ); ?>">
							<?php

							foreach ( $hashtags as $name => $hash_data ) {

								$percentage = 100; // default percentage if tags have no counts (the max is 0)
								if ( $max > 0 ) {
									$percentage = round( $hash_data['ht_count'] / $max * 10 ) * 10;
								}

								$size = 'bpht-hashtag--box bpht-hashtag--size' . $percentage;

								if ( 'list' == $atts['displaystyle'] ) {
									$size = '';
								}

								if ( 2 == $atts['displaystyle'] ) {
									$size = 'bpht-hashtag--size' . $percentage;
								}

								?>
								<?php
								$site_url     = trailingslashit( get_bloginfo( 'url' ) );
								$forum_slug   = get_option( '_bbp_root_slug' );
								$search_slug  = get_option( '_bbp_search_slug' );
								$activity_url = site_url( bp_get_activity_root_slug() );
								echo '<div data-size="' . esc_attr( $size ) . '">';
								echo ' <a href="' . esc_url( $activity_url ) . '/?s=%23' . esc_html( htmlspecialchars( $name ) ) . '" rel="nofollow" class="hashtag" id="' . esc_attr( htmlspecialchars( $name ) ) . '">#' . esc_html( htmlspecialchars( $name ) ) . '</a>';
								echo '</div>';
							}
							?>
						</div>
					</div>
				</div>
				<?php } else { ?>
					<div>
						<span class='bpht-text--muted'><?php echo esc_html_e( 'No hashtags', 'buddypress-hashtags' ); ?></span>
					</div>
				<?php } ?>
			</div>
		<?php
		return ob_get_clean();
	}

	public function bpht_render_bbpress_hashtags( $atts ) {
		// 0 - cloud    name   asc
		// 1 - list     size   desc

		$atts = shortcode_atts(
			array(
				'displaystyle' => 'cloud',
				'sortby'       => 'name',
				'sortorder'    => 'asc',
				'limit'        => '12',
			),
			$atts,
			'bpht_bbpress_hashtags'
		);

		if ( ! isset( $atts['sortby'] ) ) {
			$atts['sortby'] = 'asc';
		}

		if ( ! isset( $atts['sortorder'] ) ) {
			$atts['sortorder'] = 'name';
		}

		if ( ! isset( $atts['displaystyle'] ) ) {
			$atts['displaystyle'] = 'cloud';
		}
		ob_start();
		?>

		<div>
			<?php

			if ( ! isset( $atts['limit'] ) ) {
				$atts['limit'] = 12;
			}

			$limit = $atts['limit'];

			global $wpdb;
			$table_name = $wpdb->prefix . 'bpht_hashtags';
			$hashtags   = array();
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {
				$_hashtags = $wpdb->get_results( "SELECT * FROM $table_name WHERE ht_type IN ('bbpress') ORDER BY ht_count DESC LIMIT $limit" );
				if ( $_hashtags ) {
					foreach ( $_hashtags as $key => $ht_data ) {
						$hashtags[ $ht_data->ht_name ] = array(
							'ht_count'      => $ht_data->ht_count,
							'ht_last_count' => $ht_data->ht_last_count,
							'ht_type'       => $ht_data->ht_type,
						);
					}
				}
			}

			$max = 0;

			if ( count( $hashtags ) && is_array( $hashtags ) ) {
				$result = array();

				if ( 'name' == $atts['sortby'] ) {
					if ( 'asc' == $atts['sortorder'] ) {
						ksort( $hashtags );
					}

					if ( 'desc' == $atts['sortorder'] ) {
						krsort( $hashtags );
					}
				} elseif ( 'size' == $atts['sortby'] ) {
					if ( 'asc' == $atts['sortorder'] ) {
						asort( $hashtags );
					}

					if ( 'desc' == $atts['sortorder'] ) {
						arsort( $hashtags );
					}
				}
				$wrapper = '';

				if ( 'list' == $atts['displaystyle'] ) {
					$wrapper = 'bpht-hashtags-wrapper-list';
				} elseif ( 'cloud' == $atts['displaystyle'] ) {
					$wrapper = 'bpht-hashtags-wrapper-cloud';
				}
				?>
				<div>
					<div class="bpht-widget--hashtags">
						<div class="bpht-hashtags-wrapper <?php echo esc_attr( $wrapper ); ?>">
							<?php

							foreach ( $hashtags as $name => $hash_data ) {

								$percentage = 100; // default percentage if tags have no counts (the max is 0)
								if ( $max > 0 ) {
									$percentage = round( $hash_data['ht_count'] / $max * 10 ) * 10;
								}

								$size = 'bpht-hashtag--box bpht-hashtag--size' . $percentage;

								if ( 'list' == $atts['displaystyle'] ) {
									$size = '';
								}

								if ( 2 == $atts['displaystyle'] ) {
									$size = 'bpht-hashtag--size' . $percentage;
								}

								?>
								<?php
								$site_url    = trailingslashit( get_bloginfo( 'url' ) );
								$forum_slug  = get_option( '_bbp_root_slug' );
								$search_slug = get_option( '_bbp_search_slug' );
								$search_slug = ( $search_slug == '' ) ? 'search' : $search_slug;
								echo '<div data-size="' . esc_attr( $size ) . '">';
								echo ' <a href="' . esc_url( $site_url ) . esc_url( $forum_slug ) . '/' . esc_url( $search_slug ) . '/%23' . esc_html( htmlspecialchars( $name ) ) . '" rel="nofollow" class="hashtag" id="' . esc_attr( htmlspecialchars( $name ) ) . '">#' . esc_html( htmlspecialchars( $name ) ) . '</a>';
								echo '</div>';
							}
							?>
						</div>
					</div>
				</div>
				<?php } else { ?>
					<div>
						<span class='bpht-text--muted'><?php echo esc_html_e( 'No hashtags', 'buddypress-hashtags' ); ?></span>
					</div>
				<?php } ?>
			</div>
		<?php
		return ob_get_clean();
	}

	/*
	 * Delete Activity hashtag count when delete activity
	 *
	 */
	public function bpht_delete_buddypress_activity_hashtag_table( $args ) {
		global $wpdb;

		if ( isset( $args['id'] ) && $args['id'] != '' ) {

			$activity_id      = $args['id'];
			$activity_content = $wpdb->get_results( "SELECT content FROM {$wpdb->prefix}bp_activity  WHERE id=" . $activity_id );

			/* Get Deleted Activity Content*/
			if ( ! empty( $activity_content ) ) {
				foreach ( $activity_content as $content ) {
					/*  Search hashtag in activity content*/
					preg_match_all( '/#([^\s]+)/', $content->content, $matches );
					if ( ! empty( $matches[1] ) ) {
						foreach ( $matches[1] as $hashtag ) {
							$hashtag = str_replace( array( '</a>', '<br' ), array( '' ), wp_strip_all_tags( $hashtag ) );
							/* Check hashtag in hashtag table */
							$hashtags_count = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = 'buddypress' AND ht_name='" . $hashtag . "'" );

							if ( ! empty( $hashtags_count ) ) {
								foreach ( $hashtags_count as $value ) {

									/* If count 1 then delete hashtag from table */
									if ( $value->ht_count == 1 ) {
										$wpdb->get_results( "DELETE FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = 'buddypress' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
									} else {
										/* More then one count then reduced hashtag count */
										$wpdb->get_results( "UPDATE  {$wpdb->prefix}bpht_hashtags SET ht_count = ht_count - 1  WHERE ht_type = 'buddypress' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/*
	 * Delete Activity hashtag count when delete activity
	 *
	 */
	public function bpht_delete_buddypress_post_hashtag_table( $post_id ) {
		global $wpdb;

		$post         = get_post( $post_id );
		$post_content = $post->post_content;
		$post_type    = $post->post_type;

		preg_match_all( '/#([^\s]+)/', $post_content, $matches );
		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $hashtag ) {
				$hashtag = str_replace( '</a>', '', $hashtag );
				/* Check hashtag in hashtag table */
				$hashtags_count = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "'" );

				if ( ! empty( $hashtags_count ) ) {
					foreach ( $hashtags_count as $value ) {

						/* If count 1 then delete hashtag from table */
						if ( $value->ht_count == 1 ) {
							$wpdb->get_results( "DELETE FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
						} else {
							/* More then one count then reduced hashtag count */
							$wpdb->get_results( "UPDATE  {$wpdb->prefix}bpht_hashtags SET ht_count = ht_count - 1  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
						}
					}
				}
			}
		}
	}


	/*
	 * Delete Comment hashtag count when delete comment
	 *
	 */

	public function bpht_deleted_comment_hashtag_table( $comment_ID, $comment ) {
		global $wpdb;

		$comment_content   = $comment->comment_content;
		$comment_post_type = get_post_type( $comment->comment_post_ID );
		$post_type         = ( $comment_post_type != '' ) ? $comment_post_type : 'post';

		preg_match_all( '/#([^\s]+)/', $comment_content, $matches );
		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $hashtag ) {
				$hashtag = str_replace( '</a>', '', $hashtag );
				/* Check hashtag in hashtag table */
				$hashtags_count = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "'" );

				if ( ! empty( $hashtags_count ) ) {
					foreach ( $hashtags_count as $value ) {

						/* If count 1 then delete hashtag from table */
						if ( $value->ht_count == 1 ) {
							$wpdb->get_results( "DELETE FROM {$wpdb->prefix}bpht_hashtags  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
						} else {
							/* More then one count then reduced hashtag count */
							$wpdb->get_results( "UPDATE  {$wpdb->prefix}bpht_hashtags SET ht_count = ht_count - 1  WHERE ht_type = '" . $post_type . "' AND ht_name='" . $hashtag . "' AND ht_id=" . $value->ht_id );
						}
					}
				}
			}
		}
	}

	public function bpht_bea_get_activity_content( $content ) {
		return bp_activity_filter_kses( $content );
	}

	/**
	 * Add sub nav item in activity directory
	 *
	 * @since 2.9.4
	 * @param array $nav_items
	 * @return array $nav_items
	 */
	public function bpht_activity_directory_nav_items( $nav_items ) {
		$count = '';

		$nav_items['followed_hashtag'] = array(
			'component' => 'activity',
			'slug'      => 'hashtag',
			'li_class'  => array( 'dynamic' ),
			'link'      => bp_loggedin_user_domain() . bp_get_activity_slug() . '/hashtag/',
			'text'      => __( 'Followed Hashtag', 'buddypress-hashtags' ),
			'count'     => $count,
			'position'  => 45,
		);

		return $nav_items;
	}

	/**
	 * Add sub nav under member profile setting and activity tabs
	 *
	 * @since 2.9.4
	 */
	public function bpht_followed_hashtag_tab() {
		bp_core_new_subnav_item(
			array(
				'name'            => __( 'Hashtags', 'buddypress-hashtags' ),
				'slug'            => 'hashtags',
				'parent_url'      => trailingslashit( bp_displayed_user_domain() . 'settings' ),
				'parent_slug'     => 'settings',
				'screen_function' => array( $this, 'bpht_render_member_hastag_callback' ),
				'position'        => 100,
			)
		);

		// Add sub nav on member profile activity
		bp_core_new_subnav_item(
			array(
				'name'            => __( 'Followed Hashtags', 'buddypress-hashtags' ),
				'slug'            => 'hashtag',
				'parent_url'      => trailingslashit( bp_displayed_user_domain() . bp_get_activity_slug() ),
				'parent_slug'     => bp_get_activity_slug(),
				'screen_function' => array( $this, 'bpht_render_member_followed_hastag_callback' ),
				'position'        => 100,
			)
		);
	}

	/**
	 * Callback function for sub nav under activity nav on member profile.
	 *
	 * @since 2.9.4
	 */
	public function bpht_render_member_followed_hastag_callback() {
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Callback function for sub nav under settings nav on member profile.
	 *
	 * @since 2.9.4
	 */
	public function bpht_render_member_hastag_callback() {
		add_action( 'bp_template_content', array( $this, 'bpht_render_member_hastags_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render save tags settings on member profile
	 *
	 * @since 2.9.4
	 */
	public function bpht_render_member_hastags_content() {
		$user_id = bp_displayed_user_id() ? bp_displayed_user_id() : bp_loggedin_user_id();
		$tags    = bpht_get_user_hashtags( $user_id, 'profile' );
		$value   = array();
		if ( false !== $tags && is_array( $tags ) ) {
			foreach ( $tags as $tag ) {
				$value[] = array( 'value' => '#' . $tag->hashtag_items );
			}
		}
		?>
		<textarea type="text" id="bp-hashtag" class='bp-hashtag-box' placeholder="<?php esc_html_e( 'Add New Hashtags', 'buddypress-hashtags' ); ?>"><?php echo esc_html( json_encode( $value ) ); ?></textarea>
		<?php
	}

	public function bpht_add_hashtag_callback() {
		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'bpht_ajax_security' ) ) {
			wp_send_json_error();
		}

		$tag = isset( $_POST['tag'] ) ? sanitize_text_field( wp_unslash( $_POST['tag'] ) ) : '';

		if ( ! empty( $tag ) ) {
			bpht_db_buddypress_hashtag_entry( $tag, 'profile' );
			wp_send_json_success();
		}

	}

	public function bpht_remove_hashtag_callback() {
		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'bpht_ajax_security' ) ) {
			wp_send_json_error();
		}

		$tag = isset( $_POST['tag'] ) ? sanitize_text_field( wp_unslash( $_POST['tag'] ) ) : '';

		if ( ! empty( $tag ) ) {
			$removed = bpht_delete_user_hashtag( bp_loggedin_user_id(), $tag );
			if ( $removed ) {
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}
	}

	public function bpht_add_hashtag_feed_url( $feed_url, $scope ) {
		if ( ! empty( $scope ) && 'hashtag' === $scope && bp_is_activity_directory() ) {
			$feed_url = bp_loggedin_user_domain() . bp_get_activity_slug() . '/hashtag/feed/';
		}
		return $feed_url;
	}

	/**
	 * Set up activity arguments for use with the 'hashtag' scope.
	 *
	 * @since 2.9.4
	 *
	 * @param array $retval Empty array by default.
	 * @param array $filter Current activity arguments.
	 * @return array $retval
	 */
	public function bpht_activity_filter_hashtag_scope( $retval = array(), $filter = array() ) {
		$bp               = buddypress();
		global $wpdb;
		// Determine the user_id.
		if ( ! empty( $filter['user_id'] ) ) {
			$user_id = $filter['user_id'];
		} else {
			$user_id = bp_displayed_user_id()
				? bp_displayed_user_id()
				: bp_loggedin_user_id();
		}

		// Should we show all items regardless of sitewide visibility?
		$show_hidden = array();
		if ( ! empty( $user_id ) && $user_id !== bp_loggedin_user_id() ) {
			$show_hidden = array(
				'column' => 'hide_sitewide',
				'value'  => 0,
			);
		}
		$hashtags = bpht_get_user_hashtags( $user_id , 'profile');
		$activity_ids = array();
		// get activity id's.
		if ( ! empty( $hashtags ) ) {
			foreach ( $hashtags as $hashtag ) {
				$hashtag_activity = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT	*  FROM {$bp->activity->table_name} WHERE `content` LIKE %s;",
						'%#' . $hashtag->hashtag_items . '%'
					), ARRAY_A
				);
				foreach ( $hashtag_activity as $hashtag_activity_id ) {
					$activity_ids[] = $hashtag_activity_id['id'];
				}
			}
		}
		if ( false !== $hashtags && is_array( $hashtags ) ) {
			foreach ( $hashtags as $hashtag ) {
				$retval = array(
					'relation' => 'OR',
					array(
						'column'  => 'content',
						'compare' => 'LIKE',
						'value'   => '#' . $hashtag->hashtag_items,
					),
					array(
						'column'  => 'id',
						'compare' => 'IN',
						'value'   => $activity_ids,
					),
					$show_hidden,

					// Overrides.
					'override' => array(
						'display_comments' => 'stream',
						'filter'           => array( 'user_id' => 0 ),
						'show_hidden'      => true,
					),
				);

			}
		} else {
			$retval = array(
					'relation' => 'AND',
					array(
						'column'  => 'content',
						'compare' => 'LIKE',
						'value'   => '#nohashtagfrom' . $user_id,
					),
					$show_hidden,

					// Overrides.
					'override' => array(
						'display_comments' => 'stream',
						'filter'           => array( 'user_id' => 0 ),
						'show_hidden'      => true,
					),
				);
		}		
		return $retval;
	}

	/**
	 * Add hastag menu in admin bar
	 *
	 * @param  array $wp_admin_nav for admin menu
	 * @return void
	 */
	public function bphashtag_setup_admin_bar( $wp_admin_nav = array() ) {
			global $wp_admin_bar;
			// Setup the logged in user variables.
			$settings_link = trailingslashit( bp_loggedin_user_domain() . bp_get_settings_slug() );
			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'my-account-buddypress',
						'id'     => 'my-account-' . 'hastag',
						'title'  => 'Hashtag',
						'href'   => $settings_link . '/hashtags',
					)
				);
			}
	}

	/**
	 * Serach query override for buddyboss
	 *
	 * @param  array $where_conditions activity search query.
	 * @param  mixed $r search parameter.
	 * @param  mixed $select_sql search activity.
	 * @param  mixed $from_sql query.
	 * @param  mixed $join_sql query.
	 * @return array $where_conditions
	 */
	public function bphashtag_override_buddyboss_serach_activity_query( $where_conditions, $r, $select_sql, $from_sql, $join_sql ){
		global $wpdb;
		$search_terms_like              = '%' . bp_esc_like( $r['search_terms'] ) . '%';
		$where_conditions['search_sql'] = $wpdb->prepare( 'a.content LIKE %s', $search_terms_like );
		return $where_conditions;
	}
}
