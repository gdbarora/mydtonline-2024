<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Hashtags {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Hashtags_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BPHT_PLUGIN_VERSION' ) ) {
			$this->version = BPHT_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-hashtags';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Buddypress_Hashtags_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Hashtags_i18n. Defines internationalization functionality.
	 * - Buddypress_Hashtags_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Hashtags_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-hashtags-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-hashtags-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-hashtags-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-hashtags-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom license file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		/* Enqueue hashtags widget. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-hastags-wdget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-bbpress-hastags-wdget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-post-hashtags-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-page-hashtags-widget.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bpht-general-functions.php';

		$this->loader = new Buddypress_Hashtags_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Hashtags_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Hashtags_i18n();

		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Buddypress_Hashtags_Admin( $this->get_plugin_name(), $this->get_version() );

		$bpht_general_settings = get_option( 'bpht_general_settings' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpht_add_menu_buddypress_hashtags' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpht_add_admin_register_setting' );

		//ajax action to clear buddypress hashtag table
		$this->loader->add_action( 'wp_ajax_bpht_clear_buddypress_hashtag_table', $plugin_admin, 'bpht_clear_buddypress_hashtag_table' );
		if ( class_exists( 'bbPress' ) ) {
			$this->loader->add_action( 'wp_ajax_bpht_clear_bbpress_hashtag_table', $plugin_admin, 'bpht_clear_bbpress_hashtag_table' );
		}
		$this->loader->add_action( 'wp_ajax_bpht_clear_post_hashtag_table', $plugin_admin, 'bpht_clear_post_hashtag_table' );
		$this->loader->add_action( 'wp_ajax_bpht_clear_page_hashtag_table', $plugin_admin, 'bpht_clear_page_hashtag_table' );

		if ( ! isset( $bpht_general_settings['disable_on_blog_posts'] ) ) {
			remove_filter( 'wp_insert_post_data', 'bbp_fix_post_author', 30, 2 );
			$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'bpht_update_hashtags_links_on_save_post', 99, 2 );
			$this->loader->add_filter( 'preprocess_comment', $plugin_admin, 'bpht_update_hashtags_links_on_comment_process', 99 );
		}

		$this->loader->add_action( 'wp_ajax_bpht_delete_hashtag', $plugin_admin, 'bpht_delete_hashtag' );
		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'wbcom_hide_all_admin_notices_from_setting_page' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public         = new Buddypress_Hashtags_Public( $this->get_plugin_name(), $this->get_version() );
		$bpht_general_settings = get_option( 'bpht_general_settings' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 99 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'bp_activity_comment_content', $plugin_public, 'bpht_activity_comment_hashtags_filter', 20, 2 );
		$this->loader->add_filter( 'bea_get_activity_content', $plugin_public, 'bpht_bea_get_activity_content', 999 );
		$this->loader->add_filter( 'bp_activity_new_update_content', $plugin_public, 'bpht_activity_hashtags_filter' );
		$this->loader->add_filter( 'bp_get_activity_content_body', $plugin_public, 'bpht_activity_hashtags_filter', 8 );
		$this->loader->add_filter( 'groups_activity_new_update_content', $plugin_public, 'bpht_activity_hashtags_filter' );

		$this->loader->add_filter( 'bp_blogs_activity_new_post_content', $plugin_public, 'bpht_activity_hashtags_filter' );
		$this->loader->add_filter( 'bp_blogs_activity_new_comment_content', $plugin_public, 'bpht_activity_hashtags_filter' );

		//support edit activity stream plugin
		$this->loader->add_filter( 'bp_edit_activity_action_edit_content', $plugin_public, 'bpht_activity_hashtags_filter' );

		if ( ! isset( $bpht_general_settings['disable_on_bbpress'] ) ) {

			$this->loader->add_filter( 'bbp_new_topic_pre_content', $plugin_public, 'bpht_bbpress_hashtags_filter' );
			$this->loader->add_filter( 'bbp_edit_topic_pre_content', $plugin_public, 'bpht_bbpress_edit_hashtags_filter', 10, 2 );
			$this->loader->add_filter( 'bbp_new_reply_pre_content', $plugin_public, 'bpht_bbpress_hashtags_filter' );
			$this->loader->add_filter( 'bbp_edit_reply_pre_content', $plugin_public, 'bpht_bbpress_edit_hashtags_filter', 10, 2 );

			$this->loader->add_filter( 'bbp_get_topic_content', $plugin_public, 'bpht_bbp_get_hashtags_filter_content', 10 );
			$this->loader->add_filter( 'bbp_get_reply_content', $plugin_public, 'bpht_bbp_get_hashtags_filter_content', 10 );
		}

		//ajax query string for comment search
		$this->loader->add_filter( 'bp_ajax_querystring', $plugin_public, 'bpht_activity_hashtags_querystring', 11, 2 );
		$this->loader->add_filter( 'bp_dtheme_ajax_querystring', $plugin_public, 'bpht_activity_hashtags_querystring', 11, 2 );

		$this->loader->add_action( 'widgets_init', $plugin_public, 'bpht_register_hashtag_widget' );
		$this->loader->add_shortcode( 'bpht_bp_hashtags', $plugin_public, 'bpht_render_buddypress_hashtags' );
		if ( class_exists( 'bbPress' ) ) {
			$this->loader->add_shortcode( 'bpht_bbpress_hashtags', $plugin_public, 'bpht_render_bbpress_hashtags' );
		}

		$this->loader->add_action( 'bp_before_activity_delete', $plugin_public, 'bpht_delete_buddypress_activity_hashtag_table' );
		$this->loader->add_action( 'delete_post', $plugin_public, 'bpht_delete_buddypress_post_hashtag_table' );
		$this->loader->add_action( 'deleted_comment', $plugin_public, 'bpht_deleted_comment_hashtag_table', 20, 2 );

		if ( ! isset( $bpht_general_settings['disable_follow_hashtag'] ) ) {
			$this->loader->add_filter( 'bp_nouveau_get_activity_directory_nav_items', $plugin_public, 'bpht_activity_directory_nav_items', 10 );
			$this->loader->add_filter( 'bp_setup_nav', $plugin_public, 'bpht_followed_hashtag_tab', 10 );
		}

		$this->loader->add_action( 'wp_ajax_hashtag_add', $plugin_public, 'bpht_add_hashtag_callback' );
		$this->loader->add_action( 'wp_ajax_remove_hashtag', $plugin_public, 'bpht_remove_hashtag_callback' );
		$this->loader->add_filter( 'bp_nouveau_ajax_object_template_loader', $plugin_public, 'bpht_add_hashtag_feed_url', 10, 2 );
		$this->loader->add_filter( 'bp_activity_set_hashtag_scope_args', $plugin_public, 'bpht_activity_filter_hashtag_scope', 10, 2 );
		$this->loader->add_action( 'bp_setup_admin_bar', $plugin_public, 'bphashtag_setup_admin_bar', 80 );
		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			$this->loader->add_filter( 'bp_activity_get_where_conditions', $plugin_public, 'bphashtag_override_buddyboss_serach_activity_query', 10, 5 );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Buddypress_Hashtags_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
