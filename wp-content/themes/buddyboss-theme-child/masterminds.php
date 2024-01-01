<?php
/* Template Name: Masterminds API*/

get_header(); //Gets the header


?>
<style>
	#mastermind-overlay {
		position: fixed; /* Sit on top of the page content */
		display: none; /* Hidden by default */
		width: 100%; /* Full width (cover the whole page) */
		height: 100%; /* Full height (cover the whole page) */
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0,0,0,0.5); /* Black background with opacity */
		z-index: 999; /* Specify a stack order in case you're using a different order for other elements */
		cursor: pointer; /* Add a pointer on hover */
	}
	li.mastermind-replay button {
		max-width: 250px;
	}
	div#editVideoModal form.dtstature-form{display: flex; flex-direction: column; gap: 2px;}

	div#editVideoModal form.dtstature-form.dt-hidden{display:none;}

	div#dt-video-container {
		display: flex;
		gap: 20px;
		background: #fff;
		padding: 20px;
		border-radius: 20px;
		margin-bottom: 20px;}

	section#mastermind-replay {
		position: relative;
		margin: 0 20px;
		padding-bottom: 56.25%;
		height: 0;
		overflow: hidden;
		max-width: 100%;
	}

	section#mastermind-replay iframe,
	section#mastermind-replay embed,
	section#mastermind-replay object {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}

	nav.mastermind-tabs {
		padding: 15px;
		display: flex;
		gap: 15px;
		justify-content: center;
		align-content: center;
	}


	section#mastermind-controls {
		margin: 0 20px;
		margin-top: 3em;
		text-align: right;
		border-bottom: solid;
	}

	.mastermind-content-header {
		margin: 0 20px;
		border-bottom: solid;
		padding: 20px 0;
	}

	ul#mastermind-replay-list {
		padding: 20px 0;
		list-style-type: none;
		margin: 0;
	}

	li.mastermind-replay {
		display: grid;
		grid-template-columns: 20% 40% auto;
		padding: 20px;
		border-radius: 20px;
		margin-bottom: 10px;
		gap: 20px;
	}


	.mastermind-thumbnail {
		border-radius: 20px;
		max-width: 200px;
		aspect-ratio: 4/3;
		overflow: hidden;
	}


	.mastermind-title-desc h3:hover {
		color: #419BAF;
	}

	li.mastermind-replay:hover {
		background: #419BAF20;
		cursor: pointer;
	}


	ul#mastermind-replay-list li.mastermind-replay.loading-skeleton {
		padding-top: 190px;
		margin-bottom: 5px;
	}

	.loading-skeleton {
		background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
		background-size: 200% 100%;
		animation: loading 1.5s infinite;
	}

	@keyframes loading {
		0% {
			background-position: 200% 0;
		}

		100% {
			background-position: -200% 0;
		}
	}
</style>
<script src="/wp-content/themes/buddyboss-theme-child/assets/js/masterminds.js"></script>
<?php
$access_token = get_field('access_token');
$folders = get_field('folder_details');
if ($folders) {
	$tab_count = 1;
?>
<div id="all-masterminds">

	<nav class='mastermind-tabs'>
		<?php
	foreach ($folders as $folder) {

		$masterminds_folders = $folder['masterminds'];
		usort($masterminds_folders, function($a, $b) {
			return $b['year'] - $a['year'];
		});
		$mastermind_latest_folder =$masterminds_folders[0]['folder_id'];


		$folder_label = $folder['folder_label'];
		$folder_desc = $folder['mastermind_description'];
		$folder_select_roles = $folder['wordpress_roles_visibility'];
		$current_user = wp_get_current_user();
		$folder_available_filter = $folder['available_filters'];

		$mastermind_year_html='';
		foreach ($masterminds_folders as $mastermind){
			$folder_id = $mastermind['folder_id'];
			$folder_year = $mastermind['year'];
			$mastermind_year_html .= '<option value="' . $folder_id . '">' . $folder_year . '</option>';
		}

		$user_has_role = false;


		foreach ($folder_select_roles as $selected_role) {
			if (in_array($selected_role, $current_user->roles)) {
				$user_has_role = true;
				break;
			}
		}

		if ($user_has_role) {
			if ($folder_label !== '') {
				$activeClass = ($tab_count === 1) ? ' active' : '';

				echo '<button class="tablinks' . esc_html($activeClass) . '" folder_id="' . esc_html($mastermind_latest_folder) . '" data-desc="' . esc_html($folder_desc) . '" data-years="' . esc_html($mastermind_year_html) . '" available_filters="'.implode(',', $folder_available_filter).'">' . esc_html($folder_label) . '</button>';// Tab for Each Category
				$tab_count++;
			}
		}



	}
		?>
	</nav>
	<?php
}
	?>


	<section id='mastermind-replay'></section>

	<section id='mastermind-controls'>
		<select id='mastermind-year-filter'>
		</select>
		<?php
		//echo $folder_id;
		// Check if $folder_label is equal to 'Business Builders' before rendering the select
		?>
		<select id='mastermind-topic-filter'>
			<option value="0">Filter by Topic</option>
		</select>
		<select id="mastermind-6ajourney-filter">
			<option value="0">Filter by 6A Journey</option>
		</select>

		<select id='mastermind-sort-order'>
			<option value='desc'>Latest</option>
			<option value='asc'>Oldest</option>
		</select>
	</section>

	<section id='mastermind-content'>
		<div class="mastermind-content-header">
			<h2 id="mastermind-heading">

			</h2>
			<p id="mastermind-description">

			</p>
		</div>
		<div id="mastermind-pagination">

		</div>
		<ul id="mastermind-replay-list">

		</ul>

	</section>
</div>

<div id="editVideoModal" class="dt-modal dt-hidden">



	<div class="dt-modal-content">
		<div id="dt-video-container">

		</div>
		<form class="dtstature-form dt-hidden" id="dtstature-jsonForm">
			<input type="hidden" id="vimeo-video-id" name="video_id">
			<input type="hidden" id="video-being-edited" name="video-being-edited">

			<label class="dtstature-label" for="dtstature-title">Title:</label>
			<input class="dtstature-input" type="text" id="dtstature-title" name="title" required=""><br>

			<label class="dtstature-label" for="dtstature-topic">Topic:</label>
			<input class="dtstature-input" type="text" id="dtstature-topic" name="topic" required=""><br>

			<label class="dtstature-label" for="dtstature-journey">6A-Journey:</label>
			<input class="dtstature-input" type="text" id="dtstature-journey" name="journey" required=""><br>

			<label class="dtstature-label" for="dtstature-tags">Tags (comma-separated):</label>
			<input class="dtstature-input" type="text" id="dtstature-tags" name="tags" required=""><br>

			<div class="dtstature-links-container" id="dtstature-links-container">
				<label class="dtstature-label" for="dtstature-links">Additional Links:</label>
				<div class="dtstature-link-input" style="
														 position: relative;
														 ">
					<input class="dtstature-input" type="text" name="links" required="" style="
																							   width: 100%;
																							   ">
					<button class="dtstature-button" type="button" id="addLinkButton" style="
																							 position: absolute;
																							 right: 0;
																							 ">Add Link</button>
				</div>
				<div id="dt-links-box">

				</div>
			</div>
			<div class="dt-form-controls">
				<button class="dtstature-button" type="button" id="updateDescriptionButton">Update Data</button>			
				<button class="dtstature-button" type="button" id="closeEditButton">X</button></div>
		</form>
	</div>

</div>

<div id="mastermind-overlay"></div>

