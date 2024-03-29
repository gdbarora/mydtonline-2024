<?php

namespace LearnDash\Hub\Controller;

use Hub\Traits\Time;
use LearnDash\Hub\Framework\Controller;

/**
 * Main Controller, this will register a root page into wp-admin.
 */
class Main_Controller extends Controller {
	use Time;

	/**
	 * Projects constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->register_page(
			__( 'Add-ons', 'learndash_hub' ),
			'learndash-hub',
			array( new Projects_Controller(), 'display' ),
			'learndash-lms'
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_loaded', [ $this, 'maybe_update' ] );
	}

	/**
	 * Check if we need to take action for new update.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function maybe_update(): void {
		$version_option_name = 'learndash_hub_version';
		$version             = get_option( $version_option_name, '' );

		if ( empty( $version ) || version_compare( $version, '1.3.0', '<' ) ) {
			// updated to 1.3, try to flush the cache.
			delete_option( 'learndash-hub-projects-api' );
		}

		update_option( $version_option_name, HUB_VERSION );
	}

	/**
	 * All the scripts should be registered here, later we can use it when render the view.
	 */
	public function register_scripts() {
		$scripts = array(
			'licensing',
			'projects',
			'settings',
		);
		foreach ( $scripts as $script ) {
			wp_register_script(
				'learndash-hub-' . $script,
				hub_asset_url( '/assets/scripts/' . $script . '.js' ),
				array(
					'react',
					'react-dom',
					'wp-i18n',
				),
				HUB_VERSION,
				true
			);
		}
		wp_register_style(
			'learndash-hub-fontawesome',
			hub_asset_url( '/assets/css/fontawesome.min.css' ),
			array(),
			HUB_VERSION
		);
		wp_register_style(
			'learndash-hub',
			hub_asset_url( '/assets/css/app.css' ),
			array( 'learndash-hub-fontawesome' ),
			HUB_VERSION
		);
	}
}
