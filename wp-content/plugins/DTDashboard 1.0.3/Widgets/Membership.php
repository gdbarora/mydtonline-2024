<?php
class Membership_Status extends Base_DT_Widget
{
	public function __construct()
	{
		parent::__construct(
			'membership-status',
			'Membership Status',
			'Display Active, Inactive and Banned Users count'
		);
	}

	protected function initial_widget_content(){}
	protected function getData()
	{
		global $wpdb;
		// Fetch data for the selected year and return it as JSON
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT u.ID,
                DATE(u.user_registered) AS Start_Date,
                COALESCE(MAX(CASE WHEN field.name = 'First Name' THEN data.value END), '---') AS First_Name,
                COALESCE(MAX(CASE WHEN field.name = 'Last Name' THEN data.value END), '---') AS Last_Name,
                CASE WHEN last_activity.date_recorded >= NOW() - INTERVAL 72 HOUR THEN 'Active' ELSE 'Inactive' END AS Status,
                COALESCE(MAX(CASE WHEN field.name = 'Enagic Rank' THEN data.value END), '---') AS Rank,
                COALESCE(MAX(CASE WHEN field.name = 'Country' THEN data.value END), '---') AS Country
         FROM {$wpdb->users} AS u
         LEFT JOIN {$wpdb->prefix}bp_xprofile_data AS data ON u.ID = data.user_id
         LEFT JOIN {$wpdb->prefix}bp_xprofile_fields AS field ON field.id = data.field_id
         LEFT JOIN (
             SELECT user_id, MAX(date_recorded) AS date_recorded
             FROM {$wpdb->prefix}bp_activity
             WHERE type = 'last_activity'
             GROUP BY user_id
         ) AS last_activity ON u.ID = last_activity.user_id
         WHERE u.user_status = 0
         AND field.name IN ('Country', 'Enagic Rank', 'First Name', 'Last Name')
         GROUP BY u.ID;"
			)
		);

		return $results;
	}

	public function getTableData()
	{
		global $wpdb;

		// Fetch data using the getData method
		$data = $this->getData();

		// Define an empty HTML table
		$tableHtml = '<table class="display compact">';
		$tableHtml .= '<thead><tr><th>Start Date</th><th>First Name</th><th>Last Name</th><th>Status</th><th>Rank</th><th>Country</th></tr></thead>';
		$tableHtml .= '<tbody>';

		// Loop through the data and add rows to the table
		foreach ($data as $row) {
			$user_id = $row->ID;
			if($this->isUserJailed($user_id)){
				$row->Status = 'Jailed';				
			}
			$tableHtml .= '<tr>';
			$tableHtml .= '<td>' . esc_html($row->Start_Date) . '</td>';
			$tableHtml .= '<td>' . esc_html($row->First_Name) . '</td>';
			$tableHtml .= '<td>' . esc_html($row->Last_Name) . '</td>';
			$tableHtml .= '<td>' . esc_html($row->Status) . '</td>';
			$tableHtml .= '<td>' . esc_html($row->Rank) . '</td>';
			$tableHtml .= '<td>' . esc_html($row->Country) . '</td>';
			$tableHtml .= '</tr>';
		}

		// Close the table body and table
		$tableHtml .= '</tbody>';
		$tableHtml .= '</table>';

		return $tableHtml;
	}


	protected function getWidgetContent()
	{
		global $wpdb;

		// Fetch data for the selected year
		$results = $this->getData();
		foreach ($results as $row) {
			$user_id = $row->ID;
			if($this->isUserJailed($user_id)){
				$row->Status = 'Jailed';				
			}}
		// Initialize counters for active, inactive, and total users
		$activeUsers = 0;
		$inactiveUsers = 0;
		$jailedUsers = 0;

		// Count active and inactive users
		foreach ($results as $result) {
			if ($result->Status === 'Active') {
				$activeUsers++;
			} elseif($result->Status === 'Inactive') {
				$inactiveUsers++;
			}
			else{
				$jailedUsers++;
			}
		}

		// Calculate total users
		$totalUsers = $activeUsers + $inactiveUsers + $jailedUsers;

		// Query to count banned users
		$bannedUsersCount = $wpdb->get_var(
			"SELECT COUNT(`id`) 
         FROM `wp_bp_suspend` 
         WHERE `item_type` = 'user' AND `user_suspended` = 1"
		);

		// Prepare the chart data as an associative array
		$chartData = [
			'labels' => ['Active', 'Inactive', 'Banned'],
			'datasets' => [
				[
					'data' => [$activeUsers, $inactiveUsers, $bannedUsersCount],
					'backgroundColor' => ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(255, 206, 86, 0.2)'],
					'borderColor' => ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)'],
					'borderWidth' => 1,
				],
			],
		];


		// Encode the chart data as a JSON string
		$chartDataJson = json_encode($chartData);

		// Prepare the HTML representation of the chart container with data and script
		$htmlChartContainer = '
    <div id="myPieChartContainer">
        <canvas id="myPieChart"></canvas>
    </div>
    <script>
        var chartData = ' . $chartDataJson . ';
        var ctx = document.getElementById("myPieChart").getContext("2d");
        var myPieChart = new Chart(ctx, {
            type: "pie",
            data: chartData,
            options: {
                responsive: true,
            },
        });
    </script>
    <div id="userCounts">
        <p>Active Members: ' . $activeUsers . '</p>
        <p>Inactive Members: ' . $inactiveUsers . '</p>
        <p>Total Members: ' . $totalUsers . '</p>
        <p>Jailed Members: ' . $jailedUsers . '</p>
        <p>Banned Members: ' . $bannedUsersCount . '</p> <!-- Display banned users count -->
        <p>Invites Pending: ' . $this->getInvitedUsersCount() . '</p> <!-- Display invited users count -->
    </div>
';

		// Create an array object to store the HTML content and widget ID
		$responseArray = array(
			'widget_id' => $this->id_base,
			'html_content' => $htmlChartContainer,
			'active_users' => $activeUsers,
			'inactive_users' => $inactiveUsers,
			'jailed_users' => $jailedUsers,
			'total_users' => $totalUsers,
		);

		// Output the array as JSON
		echo json_encode($responseArray);
		exit; // Terminate the script
	}

	protected function isUserJailed($user_id) {
		// Get the user object
		$user = get_user_by('ID', $user_id);

		// Check if the user has the 'jailed' role
		if (is_a($user, 'WP_User') && in_array('jailed', $user->roles)) {
			return true; // User is jailed
		} else {
			return false; // User is not jailed
		}
	}


	protected function getInvitedUsersCount()
	{
		$count = 0;

		// Define custom query parameters
		$args = array(
			'post_type' => 'bp-invite', // Replace 'bp-invite' with your actual custom post type
			'posts_per_page' => -1, // Set to -1 to retrieve all posts, or specify a number
		);

		// Instantiate the query
		$query = new WP_Query($args);

		// Check if there are any posts
		if ($query->have_posts()) {
			// Start the loop
			while ($query->have_posts()):
			$query->the_post();

			// Get the specific post meta value
			$registered_date = get_post_meta(get_the_ID(), '_bp_invitee_registered_date', true);
			if (!$registered_date) {
				$count++;
			}
			endwhile;
		}
		return $count; 
	}


}