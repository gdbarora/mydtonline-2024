<?php
/*
Plugin Name: DT User Login History
Description: A plugin to log and display user login history.
*/

// Create custom table for user login history on plugin activation
function dt_create_user_login_history_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_login_history';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        login_time datetime NOT NULL,
        ip_address varchar(45) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'dt_create_user_login_history_table');

// Log user logins
function dt_log_user_login($user_login, $user) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_login_history';

    $user_id = $user->ID;
    $login_time = current_time('mysql');
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'login_time' => $login_time,
            'ip_address' => $ip_address
        )
    );
}
add_action('wp_login', 'dt_log_user_login', 10, 2);

// Display user login history and return HTML

function dt_display_user_login_history($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_login_history';
    
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d",
            $user_id
        )
    );
	
    $output = ''; // Initialize an empty string to collect the HTML output
	$user_info = get_userdata($user_id);

	
    if (!empty($results)) {
		if ($user_info) {
		$full_name = $user_info->first_name . ' ' . $user_info->last_name;
			$output .= '<h2>Login History of '.$full_name.'</h2>';
	} 
        
        $output .= '<table>';
        $output .= '<tr><th>Time</th><th>IP Address</th></tr>';
        foreach ($results as $result) {
            $output .= '<tr><td>' . $result->login_time . '</td><td>' . $result->ip_address . '</td></tr>';
        }
        $output .= '</table>';
    } else {
        $output .= 'No login history found for your account.';
    }

    return $output;
}
// Create a shortcode to display user login history
function dt_user_login_history_shortcode($atts) {
    ob_start();
    //dt_display_user_login_history();
    return ob_get_clean();
}
add_shortcode('user_login_history', 'dt_user_login_history_shortcode');

function dt_login_history_menu_page() {
    add_menu_page(
        'Login History',
        'Login History',
        'manage_options',
        'dt-login-history',
        'dt_display_login_history_page'
    );
}
add_action('admin_menu', 'dt_login_history_menu_page');

function dt_display_login_history_page() {?>
	
   <?php echo '<div class="wrap">';
    echo '<h2>Login History</h2>';
    
    // Display user login history
    //dt_display_user_login_history();
    the_widget('UserLoginHistoryWidget');
    
    echo '</div>';
}
class UserLoginHistoryWidget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'user_login_history_widget',
            'User Login History Widget',
            array(
                'description' => 'A widget to view the login history of any user.'
            )
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . 'User Login History' . $args['after_title'];?>
<style> 
	div#suggestions{ 
		margin-left: 20em;		
	    cursor: pointer;	
		color:black;
        background: #fff;
        width: 10%;
		border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
     }
	div#suggestions :hover {
    color: RGB(65, 155, 175);
     }
	.suggestion {
    padding: 6px;
    cursor: pointer;
}
</style>
		<script>function clearSuggestions() {document.getElementById("suggestions").innerHTML = "";}</script>
       <?php // Display the search form with suggestions
        echo '<form method="post" action="" onblur="clearSuggestions();">';
        echo '<label for="user_search">Search for a User by:</label>';
        echo '<select name="search_by">
            <option value="username">Username</option>
            <option value="userid">User ID</option>
            <option value="name">Name</option>
        	</select>';
        echo '<input type="text" name="user_search" id="user_search" autocomplete="off" >'; // Turn off browser autocomplete
		echo '<input type="hidden" id="user-id" name="user-id">';
		echo '<div id="suggestions"></div>';
		echo '<div id="login-history"></div>';
        echo '</form>';

        // JavaScript for live suggestions
        echo '<script>
    jQuery(document).ready(function($) {
        var maxSuggestions = 5;
        $("#user_search").on("input", function() {
			$("#login-history").html("");
            var searchBy = $("select[name=search_by]").val();
            var inputText = $(this).val();
            if (inputText !== "") {
                $.post(ajaxurl, {
                    action: "get_user_suggestions",
                    search_by: searchBy,
                    input_text: inputText,
                }, function(data) {
                    $("#suggestions").html(data);
                });
            } else {
                $("#suggestions").html("");
            }
        });

        $("#suggestions").on("click", ".suggestion", function() {
		var userId = $(this).data("user-id");
		var suggestionText = $(this).text();
		$("#user_search").val(suggestionText);
		// Send AJAX request to get user login history
		$.ajax({
			url: ajaxurl, // WordPress AJAX URL
			type: "POST",
			data: {
				action: "get_user_login_history", // The name of the AJAX action
				user_id: userId // Pass the user ID to the server
			},
			beforeSend: function(){
				$("#suggestions").html("");},
			success: function(response) {
				$("#login-history").html(response);
			}
		});
});

    });
</script>';

        // Display user login history
        if (isset($_POST['user_search'])) {
            $search_by = isset($_POST['search_by']) ? sanitize_text_field($_POST['search_by']) : 'username';
            $user_search = sanitize_text_field($_POST['user_search']);

            if ($search_by === 'username') {
                $user = get_user_by('login', $user_search);
            } elseif ($search_by === 'name') {
                // Get user by name
                $users = get_users(array(
                    'search' => '*' . $user_search . '*',
                    'search_columns' => array('user_nicename', 'display_name', 'user_email')
                ));
                if (!empty($users)) {
                    $user = $users[0];
                } else {
                    $user = false;
                }
            } else {
                $user = get_userdata((int) $user_search);
            }

           
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        // Output widget settings form
    }

    public function update($new_instance, $old_instance) {
        // Update widget settings
    }
}

function register_user_login_history_widget() {
    register_widget('UserLoginHistoryWidget');
}
add_action('widgets_init', 'register_user_login_history_widget');

function get_user_login_history(){
		if (isset($_POST['user_id'])) {
			$userId = $_POST['user_id'];
			$output = dt_display_user_login_history($userId);
			wp_send_json($output);
			die();
		}
		}
add_action('wp_ajax_get_user_login_history', 'get_user_login_history');
add_action('wp_ajax_nopriv_get_user_login_history', 'get_user_login_history');

// AJAX function to retrieve user suggestions
function get_user_suggestions() {
    $search_by = isset($_POST['search_by']) ? sanitize_text_field($_POST['search_by']) : 'username';
    $input_text = sanitize_text_field($_POST['input_text']);
	$users =[];
    $suggestions = array();
	if(!empty($input_text)){
    if ($search_by === 'username') {
        // Get suggestions based on username
        $users = get_users(array(
            'search' => '*' . $input_text . '*',
            'search_columns' => array('user_login', 'user_nicename', 'user_email')
        ));
    } elseif ($search_by === 'name') {
        // Get suggestions based on name
        $users = get_users(array(
            'search' => '*' . $input_text . '*',
            'search_columns' => array('user_nicename', 'display_name', 'user_email')
        ));
    } elseif ($search_by === 'userid') {
        // Get suggestions based on user ID
        $users = get_users(array(
            'search' => $input_text,
            'search_columns' => array('ID')
        ));
    }
	foreach ($users as $user) {
           // Format suggestion as "Full Name (Username)" and associate user ID
			$suggestions[] = array(
				'id' => $user->ID,
				'text' => $user->display_name . ' (' . $user->user_login . ')',
			);
        }
    if (!empty($suggestions)) {
		foreach ($suggestions as $suggestion) {
			echo '<div class="suggestion" data-user-id="' . esc_attr($suggestion['id']) . '">' . esc_html($suggestion['text']) . '</div>';
		}
	} else {
        echo '<div class="no-suggestions">No suggestions found</div>';
    }}
	else{ echo '';}

    die();
}

add_action('wp_ajax_get_user_suggestions', 'get_user_suggestions');
add_action('wp_ajax_nopriv_get_user_suggestions', 'get_user_suggestions');
