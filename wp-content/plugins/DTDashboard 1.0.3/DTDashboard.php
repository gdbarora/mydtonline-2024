<?php
/**
 * Plugin Name: DTDashboard
 * Plugin URI: https://www.statureit.com/
 * Description: This Plugin has some widgets that show statistics about the website.
 * Version: 1.0.3
 * Author: Stature IT
 * Author URI: https://www.statureit.com/
 **/

if (!defined('ABSPATH')) {
	die;
}
?>

<?php
add_action('admin_menu', 'create_dt_dashboard');
function create_dt_dashboard()
{
	add_menu_page(
		'DTDashboard',
		'DTDashboard',
		'manage_options',
		'dt-dashboard',
		'render_dt_dashboard',
		'dashicons-analytics',
		2
	);
}

add_action('admin_enqueue_scripts', 'enqueue_dt_dashboard_styles_and_scripts');
function enqueue_dt_dashboard_styles_and_scripts()
{
	$current_page = isset($_GET['page']) ? $_GET['page'] : '';

	// Check if the current page is your plugin page
	if ($current_page === 'dt-dashboard') {

		wp_enqueue_script('jquery', 'https://code.jquery.com/jquery.min.js', array(), '3.6.0');
		wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '2.9.4');
		wp_enqueue_style('dt-dashboard-styles', plugin_dir_url(__FILE__) . 'Assets/DTStyles.css', array(), '1.0.1');
		wp_enqueue_script('dt-dashboard-scripts', plugin_dir_url(__FILE__) . 'Assets/DTScript.js', array('jquery', 'chart-js', 'data-tables', 'buttons-js', 'buttons-html5-js'), '1.0.1');

		wp_enqueue_script('data-tables', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), '1.11.5', true);
		wp_enqueue_style('data-tables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
		wp_enqueue_script('buttons-js', 'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', array('jquery', 'data-tables'), '2.4.1', true);
		wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js', array(), '3.1.3', true);
		wp_enqueue_script('printtable', 'https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.4.2/js/buttons.print.min.js', array(), '2.4.2', true);
		wp_enqueue_script('buttons-html5-js', 'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', array('buttons-js','jszip','printtable'), '2.4.1', true);
		wp_enqueue_style('data-tables-btns-css', 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css');


		wp_localize_script(
			'dt-dashboard-scripts',
			'ajaxUrl',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
			)
		);

	}
}

require_once plugin_dir_path(__FILE__) . 'Widgets.php';

add_action('widgets_init', 'register_dt_widgets');
function register_dt_widgets()
{
	register_widget('Member_Growth');
	register_widget('Membership_Status');
	register_widget('Member_Rank');
	register_widget('Member_Location');
	register_widget('Member_Languages');
	register_widget('Groups');
	register_widget('Posts_With_Most_Engagement');
	register_widget('Flagged_Words');
	register_widget('Courses');
}

function render_dt_dashboard()
{ ?>
<div id="all-dashboard-widgets" class='main-dt-container'>
	<h1 class='main-heading'>DT-Dashboard</h1>
	<div id='dt-widgets-container'>
		<div id='dashboard-widgets-wrap'>
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="postbox-container-0" class="postbox-double-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<?php the_widget('Member_Growth'); ?>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="column3-sortables" class="meta-box-sortables ui-sortable">
						<?php the_widget('Flagged_Words'); ?>
						<?php the_widget('Groups'); ?>
					</div>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php the_widget('Member_Rank'); ?>
						<?php the_widget('Member_Languages'); ?>
						<?php the_widget('Member_Location'); ?>
						<?php the_widget('Posts_With_Most_Engagement'); ?>
					</div>
				</div>
				<div id="postbox-container-3" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<?php the_widget('Membership_Status'); ?>
						<?php the_widget('Courses'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php }


