<?php
/*
Plugin Name: Jailed Users
Description: Restrict jailed role from modifying content for a specified duration.
Version: 1.0
Author: Your Name
*/

// Activation hook
register_activation_hook(__FILE__, 'ju_activation');

function ju_activation()
{
	$jailed_users = get_users(array('role' => 'jailed'));

	// Get the selected restriction duration from admin settings
	$restriction_duration = get_option('ju_restriction_duration', 30); // Default to 30 days

	// Calculate the restriction end date
	$restriction_end_date = strtotime("+$restriction_duration days");

	// Set the restriction end date for jailed users
	foreach ($jailed_users as $user) {
		update_user_meta($user->ID, 'ju_restriction_end_date', $restriction_end_date);
	}
}




function custom_bp_activity_custom_update($content) {
    // Check if the current user is jailed
    if (is_user_jailed()) {
		return false;
    }

    return $content;
}

add_filter('bb_user_can_create_activity', 'custom_bp_activity_custom_update', 10, 1);
add_filter('bb_user_can_send_messages', 'custom_bp_activity_custom_update', 10, 1);
add_filter('bp_activity_can_comment', 'custom_bp_activity_custom_update', 10, 1);
add_filter('bp_activity_can_favorite', 'custom_bp_activity_custom_update', 10, 1);


add_action('bbp_new_topic_pre_extras', 'prevent_jailed_users_from_creating_discussion', 10, 1);

function prevent_jailed_users_from_creating_discussion($forum_id)
{
	if (is_user_jailed()) {
		// Jailed users are not allowed to create discussions.
		bbp_add_error('bbp_topic_permissions', __('<strong>ERROR</strong>: Jailed users do not have permission to create new discussions.', 'buddyboss'));
	}
}

function is_user_jailed()
{
	$current_user = wp_get_current_user();
	return in_array('jailed', $current_user->roles);
}

// Action to check if the user is jailed before adding a forum reply
add_action('bbp_new_reply_pre_extras', 'prevent_jailed_users_from_reply', 10, 1);

// Function to prevent jailed users from replying to forum topics
function prevent_jailed_users_from_reply($reply_id)
{
    if (is_user_jailed()) {
        // Jailed users are not allowed to reply to forum topics.
        bbp_add_error('bbp_reply_permissions', __('<strong>ERROR</strong>: Jailed users do not have permission to reply to forum topics.', 'buddyboss'));
    }
}


// Hook this function to run daily using WordPress's cron system.
add_action('ju_daily_cron', 'check_and_remove_jailed_role_daily');

// Schedule the daily cron event if it doesn't exist.
if (!wp_next_scheduled('ju_daily_cron')) {
    wp_schedule_event(current_time('timestamp'), 'daily', 'ju_daily_cron');
}

// Function to check and remove the "jailed" role daily.
function check_and_remove_jailed_role_daily() {
    $jailed_users = get_users(array('role' => 'jailed'));

    $current_time = current_time('timestamp');

    foreach ($jailed_users as $user) {
        $user_id = $user->ID;
        $restriction_end_date = get_user_meta($user_id, 'ju_restriction_end_date', true);

        if ($restriction_end_date && $current_time > $restriction_end_date) {
            $user->remove_role('jailed');
            delete_user_meta($user_id, 'ju_restriction_end_date');
        }
    }
}



register_deactivation_hook(__FILE__, 'ju_deactivation');

function ju_deactivation()
{
	$jailed_users = get_users(array('role' => 'jailed'));
	foreach ($jailed_users as $user) {
		delete_user_meta($user->ID, 'ju_restriction_end_date');
	}
}

// Add an admin menu item for the plugin
function ju_add_admin_menu()
{
	add_menu_page(
		'Jailed Users',
		'Jailed Users',
		'manage_options',
		'ju-settings',
		'ju_settings_page'
	);
}
add_action('admin_menu', 'ju_add_admin_menu');

// Create the admin settings page
function ju_settings_page()
{
	if (current_user_can('manage_options')) {
		if (isset($_POST['ju_restriction_duration'])) {
			// Save the selected restriction duration
			$restriction_duration = intval($_POST['ju_restriction_duration']);
			update_option('ju_restriction_duration', $restriction_duration);
		}

		// Get the current restriction duration
		$current_duration = get_option('ju_restriction_duration', 30); // Default to 30 days

		// Handle user addition to 'jailed' role and update user meta
		if (isset($_POST['add_to_jail'])) {
			$user_id = sanitize_text_field($_POST['selected_user']);
			$user = get_user_by('ID', $user_id);

			// Check if the user exists and is not already in the 'jailed' role
			if ($user && !in_array('jailed', $user->roles)) {
				$user->add_role('jailed');
				// Use JavaScript to display an alert instead of echoing a message.
				echo "<script>alert('User " . $user->display_name . " has been added to the \"jailed\" role.');</script>";

				if (in_array('administrator', $user->roles)) {
					// You can also display an alert for administrators if needed.
					echo "<script>alert('User " . $user->display_name . " is an administrator and can remove himself from the \"jailed\" role.');</script>";
				}
			}

				// Get the selected restriction duration
				$restriction_duration = get_option('ju_restriction_duration', 30);

				// Calculate the restriction end date
				$restriction_end_date = strtotime("+$restriction_duration days");

				// Update the user meta with the restriction end date
				update_user_meta($user->ID, 'ju_restriction_end_date', $restriction_end_date);
			}
		

		// Handle user removal from 'jailed' role
		if (isset($_POST['remove_user'])) {
			$user_id = sanitize_text_field($_POST['remove_user']);
			$user = get_user_by('ID', $user_id);

			// Check if the user exists and is in the 'jailed' role
			if ($user && in_array('jailed', $user->roles)) {
				$user->remove_role('jailed');
				
				// Also remove the user meta with the restriction end date
				delete_user_meta($user->ID, 'ju_restriction_end_date');
				echo "<script>alert('User " . $user->display_name . " has been removed successfully from the \"jailed\" role.');</script>";

			}
		}

		// Display the settings form
		?>
		<div class="wrap">
			<h2>Jailed User Restrictions Settings</h2>
			<form method="post" action="">
				<label for="ju_restriction_duration">Select Restriction Duration (in days):</label>
				<input type="number" id="ju_restriction_duration" name="ju_restriction_duration" min="1"
					value="<?php echo $current_duration; ?>">
				<input type="submit" value="Save">
			</form>

			<h3>Add User to 'jailed' Role</h3>
			<form method="post" action="">
				<label for="selected_user">Select User to Jail:</label>
				<select name="selected_user" id="selected_user">
					<?php
					$users = get_users(); // Get a list of all users
					foreach ($users as $user) {
						echo '<option value="' . $user->ID . '">' . $user->display_name . '</option>';
					}
					?>
				</select>
				<input type="submit" name="add_to_jail" value="Add to Jail">
			</form>

			<h3>Jailed Users</h3>
			<?php
			$jailed_users = get_users(array('role' => 'jailed'));
			if (!empty($jailed_users)) {
				echo '<form method="post" action="">'; // Change here
				echo '<table class="widefat">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Restriction End Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';
				foreach ($jailed_users as $user) {
					$user_id = $user->ID;
					$user_name = $user->display_name;
					$user_email = $user->user_email;
					$restriction_end_date = get_user_meta($user_id, 'ju_restriction_end_date', true);
					$formatted_end_date = $restriction_end_date ? date('Y-m-d', $restriction_end_date) : 'Not available';
					echo '<tr>
            <td>' . $user_name . '</td>
            <td>' . $user_email . '</td>
            <td>' . $formatted_end_date . '</td>
            <td>
                <button type="submit" name="remove_user" value="' . $user_id . '">Remove</button> 
            </td>
        </tr>';
				}
				echo '</tbody>
    </table>';
				echo '</form>'; // Close the form
			} else {
				echo 'No jailed users found.';
			}
			?>

		</div>
		<?php
	}
}

