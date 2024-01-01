<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/includes
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
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Profanity {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Profanity_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-profanity';

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
	 * - Buddypress_Profanity_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Profanity_i18n. Defines internationalization functionality.
	 * - Buddypress_Profanity_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Profanity_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-profanity-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-profanity-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-profanity-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-profanity-public.php';

		/**
		 * The file is used for writing functions used in admin end and front end.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wbbprof-admin-public-functions.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom plugin license file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		$this->loader = new Buddypress_Profanity_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Profanity_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Profanity_i18n();

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

		$plugin_admin = new Buddypress_Profanity_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( bp_core_admin_hook(), $plugin_admin, 'wbbprof_add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wbbprof_admin_register_settings' );
		$this->loader->add_action( 'wp_ajax_wbbprof_reset_keywords', $plugin_admin, 'wbbprof_reset_keywords' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wbcom_hide_all_admin_notices_from_setting_page' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Profanity_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'bp_get_activity_content_body', $plugin_public, 'wbbprof_bp_get_activity_content_body', 20 );
		$this->loader->add_filter( 'bp_get_activity_content', $plugin_public, 'wbbprof_bp_activity_comment_content', 1 );
		$this->loader->add_filter( 'bp_get_the_thread_message_content', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );
		$this->loader->add_filter( 'bp_get_message_thread_content', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );
		$this->loader->add_filter( 'bp_get_message_thread_excerpt', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );
		$this->loader->add_filter( 'bp_get_message_thread_subject', $plugin_public, 'wbbprof_bp_get_message_thread_subject', 1 );
		$this->loader->add_filter( 'bp_get_the_thread_subject', $plugin_public, 'wbbprof_bp_get_message_thread_subject', 1 );
		$this->loader->add_filter( 'bp_better_messages_after_format_message', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );
		$this->loader->add_filter( 'bp_create_excerpt', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );
		$this->loader->add_filter( 'bp_get_the_notification_description', $plugin_public, 'wbbprof_bp_get_the_thread_message_content', 1 );

		/* bbPress Forum, Topics, reply hook to title and content */
		$this->loader->add_action( 'bbp_get_forum_title', $plugin_public, 'wbbprof_bbp_get_title', 10, 2 );
		$this->loader->add_action( 'bbp_get_topic_title', $plugin_public, 'wbbprof_bbp_get_title', 10, 2 );
		$this->loader->add_action( 'bbp_get_reply_title', $plugin_public, 'wbbprof_bbp_get_title', 10, 2 );
		$this->loader->add_action( 'bbp_get_forum_content', $plugin_public, 'wbbprof_bbp_get_reply_content', 10, 2 );
		$this->loader->add_action( 'bbp_get_topic_content', $plugin_public, 'wbbprof_bbp_get_reply_content', 10, 2 );
		$this->loader->add_action( 'bbp_get_reply_content', $plugin_public, 'wbbprof_bbp_get_reply_content', 10, 2 );
		

		$this->loader->add_filter( 'bp_core_replace_tokens_in_text', $plugin_public, 'wbbprof_bp_core_replace_tokens_in_text', 10, 2 );

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
	 * @return    Buddypress_Profanity_Loader    Orchestrates the hooks of the plugin.
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
