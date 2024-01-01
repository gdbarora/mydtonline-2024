<?php
/* Template Name: Testing Page Template*/
get_header();//Gets the header
$displayed_user_id = bp_displayed_user_id();
do_shortcode("[gamipress_user_points type='all' columns='3' thumbnail='yes' label='yes' layout='left' align='none' period='this-month' user_id='{$displayed_user_id}']");

echo 'hi';
return;
global $wpdb;

// Custom SQL query to get the top 20 users with max points
$query = "
    SELECT user_id, SUM(points) as total_points
    FROM {$wpdb->prefix}gamipress_user_points
    GROUP BY user_id
    ORDER BY total_points DESC
    LIMIT 20
";

$top_users = $wpdb->get_results($query);

// Output the result
foreach ($top_users as $user) {
    $user_id = $user->user_id;
    $total_points = $user->total_points;

    echo "User ID: $user_id, Total Points: $total_points<br>";
}

return;

?>

<div class='mm_tabs'>

<?php
// Creating Tabs for all three Masterminds
// Get all categories for the custom taxonomy "masterminds_category"
$categories = get_terms(
	array(
		'taxonomy' => 'masterminds_category',
		'hide_empty' => false,
	)
);

// Loop through each category to create tabs
foreach ($categories as $category) {	

	echo '<button class="tablinks" evt-cg="'.esc_attr($category->slug).'">'.esc_html($category->name).'</button>';// Tab for Each Category

} ?>

</div>

<!-- Displaying The Latest Event  -->
<div class='mm_event_replay'>
	<div id='mm_latest_event'></div>
	<div class='mm_event_replay_info'>
		<h2 id="mm_event_title"></h2>
		<p id="mm_evt_details"></p>
	</div>
</div>

<div class="mm_dropdowns">
	
	<select id="tag_filter">
			<option value="0">Sort By Topic</option>
	</select>

	<select id="presenter_filter">
		<option value="0">Filter by 6A Journey</option>
	</select>

	<select id='mm_sort'>
		<option value='0'>Latest</option>
		<option value='1'>Oldest</option>
	</select>
		
</div>

<hr>

<div class='mm_evts_list'>

	<?php
	// Fetch all posts of the 'masterminds' post type based on the selected category
	foreach ($categories as $category) {
		$posts_args = array(
			'post_type' => 'masterminds',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'masterminds_category',
					'field' => 'slug',
					'terms' => $category->slug,
				),
			),
			'meta_key' => 'event_date',
			// Set the meta key for ACF field 'event_date'
			'orderby' => 'meta_value',
			// Sort by meta value (i.e., 'event_date')
			'order' => 'DESC', //Newest to Oldest
		);

		$posts_query = new WP_Query($posts_args);

		if ($posts_query) {
			?>
			<div id="<?php echo esc_attr($category->slug); ?>" class="tabcontent">
				<h2><?php echo esc_html($category->name); ?></h2>
				<div class='evt-type-details' m-id="<?php echo esc_attr($category->slug); ?>"><p></p></div>
				<hr>
				<ul>

					<?php
					//Loop to get details of each post
					while ($posts_query->have_posts()) {

						$posts_query->the_post();

						// Get post details
						$post_id = get_the_ID();
						$post_title = get_the_title();
						$post_content = get_the_content();
						$post_date = get_the_date();
						$mm_event_date = get_field('event_date');//get event date
						$mm_event_presenter = get_field('event_presentor');//get presenter 6a journey
						$mm_evt_link = get_field('event_vimeo_link');//get event replay link
						$mm_evt_topic_array = wp_get_post_terms($post_id,'masterminds_tags', array( 'fields' => 'names' ));
						$vimeoId = extractVimeoId($mm_evt_link);
						
						
						//Create a li element for display?>

						<li class="evt_list_item" mm-evt-src='<?php echo getVimeoFrame($vimeoId); ?>'>
							<div class="evnt_items-left d-flex">
								<div class="evt_item_tbn"><img src="<?php echo getVimeoThumbnail($vimeoId); ?>"/></div>
								<div class="event_item_info">
									<h3 class="mm_event_title"><?php echo $post_title; ?></h3>
									<div class='mm_event_date'><?php echo $mm_event_date; ?></div>	
									<div class='mm_event_presenter'><span>6A Journey: </span><?php echo $mm_event_presenter; ?></div>
								<?php if (!empty($mm_evt_topic_array)) { echo '<div id="mm_evt_topic" style="display: none;">';
		foreach ($mm_evt_topic_array as $mm_evt_topic) {
			echo '<span>' . $mm_evt_topic . '</span>';
		}
																		echo '</div>';
	} ?>
								</div>
							</div>
							<div class="evnt_items-right">
							<?php
						
							$rows = get_field('event_documents_to_be_shared');//get rows of repeater field
							if( $rows ) {
								echo '<div class = "mm_evt_docs"><h4>Files & Links</h4>'; // show this heading if rows exists
								foreach( $rows as $row ) {//get and show links for each document or link attached
									$file = $row['document'];
									$link = $row['link'];
									echo '<a href="'.$file['url'].'">'.$file['filename'].'</a>';
									echo '<a href="'.$link.'">'.$link.'</a>';
								}
							}
							
							?>
							</div>
						</li>

					<?php

					} 
					
					?>
			
				</ul>
</div>

		<?php

		}

	wp_reset_postdata();

	}?>
</div>
<?php get_footer(); //get footer?>