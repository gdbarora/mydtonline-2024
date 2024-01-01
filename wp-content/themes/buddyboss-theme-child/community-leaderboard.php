<?php
/*
 * Template Name: Leaderboard
 * Template author: Stature
 *
 */
get_header();
echo '<div class="dt-main">';
?>

<div class="community_leaderboard">
	<h1 style="margin-top: 20px;">Community Leaderboard</h1>
	<p class="leaderboard-description">(Results Generated In Pacific Timezone)</p>

	<?php
	if (current_user_can('manage_options')) {
		$today = new DateTime();
		$monday = clone $today;
		$monday->modify('this week');
		$sunday = clone $monday;
		$sunday->modify('next sunday');
	?>
<!-- 	<div class="leaderboard-top-row">
		<?php
		//echo '<h2  class="leaderboard-first-colmn">Add Top buyers for the current week (' . $monday->format('d-m-Y') . ' to ' . $sunday->format('d-m-Y') . ')</h2>';
		?>
		
	</div> -->
</div>
<?php
		echo '<div class="main-popup-overlay">';
		echo '<form id="add_dt_buyers" class="ui-front add_dt_buyers">';
		echo '<span id="close" style="text-align: right; cursor: pointer;">X</span>';
		echo '<input type ="hidden" id="user_id">';
		echo '<label for="profile ">Profile:</label>';
		echo '<input type="text" id="profile" name="profile" autocomplete="off">';
		echo '<label for="points">Buyers:</label>';
		echo '<input type="number" id="points" name="points" autocomplete="off" required>';
		echo '<label for="place">Placement:</label>';
		echo '<select id="place" name="place">
			<option value="1">1st</option>
			<option value="2">2nd</option>
			<option value="3">3rd</option>
		</select>';
		echo '<input id="add_buyer_submit" type="submit" value="Add User" disabled>';
		echo '<input type="reset" value="Reset">';
		echo '<div class="submit-message"></div>';
		echo '</form>';
		echo '</div>';
	}		
?>
<hr>
<div class="community_leaderboard-buyers">
	<button id="leaderboard-popup-btn" class="leaderboard-popup-btn button push-right small" style="float:right">
			Add top buyer
		</button>
	<h2 class='leaderboard-heading'>Top buyers for the current week</h2>

	<?php
	global $wpdb;

	// Assuming your table is named 'wp_dt_buyers_ranked'
	$table_name = $wpdb->prefix . 'dt_buyers_ranked';

	// Get the current week's start and end dates

	$current_week_start = date('Y-m-d', strtotime('monday this week'));
	$current_week_end = date('Y-m-d', strtotime('monday next week'));

	// SQL query to fetch members added in the current week
	$query = $wpdb->prepare(
		"SELECT * FROM $table_name WHERE added_date BETWEEN %s AND %s ORDER BY place",
		$current_week_start,
		$current_week_end
	);


	$results = $wpdb->get_results($query, ARRAY_A);

	echo '<div class="top-community-buyers">';
	foreach ($results as $user) {
		$user_id = $user['user_id'];
		$user['full_name'] = bp_core_get_userlink($user_id);
		$user['avatar'] = bp_core_fetch_avatar(array('item_id' => $user_id, 'type' => 'full'));


		echo '<div class="community-buyer-box" buyer-position="'. $user['rank'] .'">';
		if(current_user_can('manage_options')){
			echo '<button class="remove-top-buyer" data-id="' . $user['id'] . '"><i class="fa fa-trash"></i></button>';
		}
		echo '<div class="buyer-points">' . $user['points'] . ' Buyers</div>';
		echo '<div class="buyer-avatar">' . $user['avatar'] . $user['place'] . '</div>';
		echo '<div class="buyer-name">' . $user['full_name'] . '</div>';
		
		echo '</div>';
	}
	echo '</div>';

	?>

</div>
<div class="community_leaderboard-yearly">
	<h2 class='leaderboard-heading'>Top community participation for year
		<?= date('Y'); ?>
	</h2>
	<?php echo do_shortcode('[gamipress_leaderboard id="3231" title=""]'); ?>
</div>
<div class="community_leaderboard-monthly">
	<h2 class='leaderboard-heading'>Top community participation for
		<?= date('F, Y'); ?>
	</h2> <br>
	<?php echo do_shortcode('[gamipress_leaderboard id="3234" title=""]'); ?>
</div>
<div class="new-rank-qualification-monthly"><br><br>
	<h2 class='leaderboard-heading'>New Rank Qualifications For <?= date('F, Y'); ?></h2><br>
	<?php echo do_shortcode('[gamipress_leaderboard id="3606" title=""]'); ?>
</div>

<?php
echo '</div>';

get_footer();