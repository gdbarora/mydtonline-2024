<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Profanity_Public {

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
	 * Plugin general setting.
	 *
	 * @var array $wbbprof_settings
	 */
	private $wbbprof_settings;

	/**
	 * Remove keyword from the community.
	 *
	 * @var array $keywords
	 */
	private $keywords;

	/**
	 * Remove character from the community.
	 *
	 * @var array $character
	 */
	private $character;

	/**
	 * Remove character from the words.
	 *
	 * @var array $word_rendering
	 */
	private $word_rendering;

	/**
	 * Case Insensitive matching type is better as it captures more words.
	 *
	 * @var array $case
	 */
	private $case;

	/**
	 * When strict filtering is ON, embedded keywords are filtered.
	 *
	 * @var array $whole_word
	 */
	private $whole_word;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name      = $plugin_name;
		$this->version          = $version;
		$wbbprof_settings       = bp_get_option( 'wbbprof_settings' );
		$this->wbbprof_settings = &$wbbprof_settings;

		if ( isset( $this->wbbprof_settings['keywords'] ) ) {
			$keywords       = array_map( 'trim', explode( ',', $this->wbbprof_settings['keywords'] ) );
			$keywords       = array_unique( $keywords );
			$this->keywords = &$keywords;
		}

		if ( isset( $this->wbbprof_settings['character'] ) ) {
			$character = $this->wbbprof_settings['character'];
			$symbol    = '';
			switch ( $character ) {
				case 'asterisk':
					$symbol = '*';
					break;
				case 'dollar':
					$symbol = '$';
					break;
				case 'question':
					$symbol = '?';
					break;
				case 'exclamation':
					$symbol = '!';
					break;
				case 'hyphen':
					$symbol = '-';
					break;
				case 'hash':
					$symbol = '#';
					break;
				case 'tilde':
					$symbol = '~';
					break;
				case 'blank':
					$symbol = ' ';
					break;
				default:
					if ( apply_filters( 'wbbprof_custom_character', $symbol ) ) {
						$symbol = apply_filters( 'wbbprof_custom_character', $symbol );
					} else {
						$symbol = '*';
					}
					break;
			}
			$this->character = &$symbol;
		}

		if ( isset( $this->wbbprof_settings['word_render'] ) ) {
			$word_rendering       = $this->wbbprof_settings['word_render'];
			$this->word_rendering = &$word_rendering;
		} else {
			$this->word_rendering = 'first';
		}

		if ( isset( $this->wbbprof_settings['case'] ) ) {
			$case       = $this->wbbprof_settings['case'];
			$this->case = &$case;
		}

		if ( isset( $this->wbbprof_settings['strict_filter'] ) ) {
			$whole_word       = 'off' == $this->wbbprof_settings['strict_filter'] ? false : true;
			$this->whole_word = &$whole_word;
		}
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
		 * defined in Buddypress_Profanity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Profanity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_buddypress() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-profanity-public.css', array(), $this->version, 'all' );
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
		 * defined in Buddypress_Profanity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Profanity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_buddypress() ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-profanity-public.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 *
	 * Function for filtering activity staus updates.
	 *
	 * @param string $content Activity status update string.
	 */
	public function wbbprof_bp_get_activity_content_body( $content ) {
		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'status_updates', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( 'incase' == $this->case ) {
								$content = $this->wbbprof_profain_word_i( $keyword, $replacement, $content, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$content = $this->wbbprof_profain_word( $keyword, $replacement, $content, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $content;
	}

	/**
	 *
	 * Function for filtering activity comment.
	 *
	 * @param string $content Activity comment string.
	 */
	public function wbbprof_bp_activity_comment_content( $content ) {
		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'activity_comments', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( 'incase' == $this->case ) {
								$content = $this->wbbprof_profain_word_i( $keyword, $replacement, $content, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$content = $this->wbbprof_profain_word( $keyword, $replacement, $content, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $content;
	}

	/**
	 *
	 * Function for filtering message content.
	 *
	 * @param string $content Message string.
	 */
	public function wbbprof_bp_get_the_thread_message_content( $content ) {
		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'messages', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( 'incase' == $this->case ) {
								$content = $this->wbbprof_profain_word_i( $keyword, $replacement, $content, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$content = $this->wbbprof_profain_word( $keyword, $replacement, $content, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $content;
	}

	/**
	 *
	 * Function for filtering message subject.
	 *
	 * @param string $content Message string.
	 */
	public function wbbprof_bp_get_message_thread_subject( $content ) {
		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'messages', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( 'incase' == $this->case ) {
								$content = $this->wbbprof_profain_word_i( $keyword, $replacement, $content, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$content = $this->wbbprof_profain_word( $keyword, $replacement, $content, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $content;
	}

	/**
	 *
	 * Function for word sensoring.
	 *
	 * @param string $wbbprof_render_type Word Rendering type.
	 * @param string $keyword             Keyword to remove.
	 * @param string $char_symbol         Symbol to replace with keywords.
	 */
	public function wbbprof_censor_word( $wbbprof_render_type, $keyword, $char_symbol ) {

		$keyword_length = mb_strlen( $keyword, 'UTF-8' );

		switch ( $wbbprof_render_type ) {
			case 'first':
				$first_keyword = mb_substr( $keyword, 0, 1, 'UTF-8' );
				$keyword       = $first_keyword . str_repeat( $char_symbol, mb_strlen( mb_substr( $keyword, 1 ), 'UTF-8' ) );
				break;
			case 'all':
				$keyword = str_repeat( $char_symbol, mb_strlen( substr( $keyword, 0 ), 'UTF-8' ) );
				break;
			case 'fisrt_last':
			case 'first_last':
				$first_keyword = mb_substr( $keyword, 0, 1, 'UTF-8' );
				$last_keyword  = mb_substr( $keyword, -1, 1, 'UTF-8' );
				$keyword       = $first_keyword . str_repeat( $char_symbol, mb_strlen( mb_substr( $keyword, 2 ), 'UTF-8' ) ) . $last_keyword;
				break;
			case 'last':
				$last_keyword = mb_substr( $keyword, -1, 1, 'UTF-8' );
				$keyword      = str_repeat( $char_symbol, mb_strlen( mb_substr( $keyword, 0, -1 ), 'UTF-8' ) ) . $last_keyword;
				break;
			default:
				$first_keyword = mb_substr( $keyword, 0, 1, 'UTF-8' );
				$last_keyword  = mb_substr( $keyword, -1, 1, 'UTF-8' );
				$keyword       = $first_keyword . str_repeat( $char_symbol, mb_strlen( mb_substr( $keyword, 2 ), 'UTF-8' ) ) . $last_keyword;
				break;
		}
		return $keyword;
	}

	/**
	 *
	 * Function to replace words with character when case sensitive.
	 *
	 * @param string  $fword           The keyword to be replaced.
	 * @param string  $replacement     The keyword to be replaced with.
	 * @param string  $wbbprof_content The content to find the keyword.
	 * @param boolean $whole_word      Strict filtering or not.
	 */
	public function wbbprof_profain_word( $fword, $replacement, $wbbprof_content, $whole_word = true ) {
		$fword   = str_replace( '/', '\\/', preg_quote( $fword ) ); // allow '/' in keywords.
		$pattern = $whole_word ? "/\b$fword\b/" : "/$fword/";

		$wbbprof_content = preg_replace( $pattern, $replacement, $wbbprof_content );

		return $wbbprof_content;
	}

	/**
	 *
	 * Function to replace words with character when case insensitive.
	 *
	 * @param string  $fword               The keyword to be replaced.
	 * @param string  $replacement         The keyword to be replaced with.
	 * @param string  $wbbprof_content     The content to find the keyword.
	 * @param string  $wbbprof_render_type Word Rendering type.
	 * @param string  $keyword             Keyword to remove.
	 * @param string  $char_symbol         Symbol to replace with keywords.
	 * @param boolean $whole_word          Strict filtering or not.
	 */
	public function wbbprof_profain_word_i( $fword, $replacement, $wbbprof_content, $wbbprof_render_type, $keyword, $char_symbol, $whole_word = true ) {

		$fword   = str_replace( '/', '\\/', preg_quote( $fword ) ); // allow '/' in keywords.
		$pattern = $whole_word ? "/\b$fword\b/i" : "/$fword/i";

		$wbbprof_content = preg_replace_callback(
			$pattern,
			function( $m ) use ( $wbbprof_render_type, $keyword, $char_symbol ) {
				return $this->wbbprof_censor_word( $wbbprof_render_type, $m[0], $char_symbol );
			},
			$wbbprof_content
		);
		return $wbbprof_content;
	}

	public function wbbprof_bbp_get_title( $title, $bbp_id ) {

		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'bbpress_title', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( $this->case == 'incase' ) {
								$title = $this->wbbprof_profain_word_i( $keyword, $replacement, $title, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$title = $this->wbbprof_profain_word( $keyword, $replacement, $title, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $title;
	}

	public function wbbprof_bbp_get_reply_content( $content, $bbp_id ) {

		if ( ! empty( $this->wbbprof_settings ) && isset( $this->wbbprof_settings['filter_contents'] ) ) {
			if ( in_array( 'bbpress_content', $this->wbbprof_settings['filter_contents'] ) ) {
				if ( is_array( $this->keywords ) ) {
					foreach ( $this->keywords as $key => $keyword ) {
						$keyword = trim( $keyword );
						if ( strlen( $keyword ) > 2 ) {
							$replacement = $this->wbbprof_censor_word( $this->word_rendering, $keyword, $this->character );
							if ( $this->case == 'incase' ) {
								$content = $this->wbbprof_profain_word_i( $keyword, $replacement, $content, $this->word_rendering, $keyword, $this->character, $this->whole_word );
							} else {
								$content = $this->wbbprof_profain_word( $keyword, $replacement, $content, $this->whole_word );
							}
						}
					}
				}
			}
		}
		return $content;
	}


	public function wbbprof_bp_core_replace_tokens_in_text( $text, $tokens ) {

		$unescaped = array();
		$escaped   = array();

		foreach ( $tokens as $token => $value ) {
			if ( ! is_string( $value ) && is_callable( $value ) ) {
				$value = call_user_func( $value );
			}

			// Tokens could be objects or arrays.
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$unescaped[ '{{{' . $token . '}}}' ] = $this->wbbprof_bp_get_activity_content_body( $value );
			$escaped[ '{{' . $token . '}}' ]     = esc_html( $this->wbbprof_bp_get_activity_content_body( $value ) );
		}

		$text = strtr( $text, $unescaped );  // Do first.
		$text = strtr( $text, $escaped );

		return $text;
	}

}
