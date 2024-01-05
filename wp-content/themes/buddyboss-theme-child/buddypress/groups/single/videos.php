<?php
/**
 * BuddyBoss - Groups Video
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/videos.php.
 *
 * @since   BuddyBoss 1.7.0
 * @version 1.7.0
 */
?>
<div class="bb-video-container bb-media-container group-video">
<div class="bb-video-album-actions-wrap bb-media-actions-wrap">
	<h2 class="bb-title">
		<?php esc_html_e('Video Albums', 'buddyboss'); ?>
	</h2>
	<?php
	$group_admins = bp_group_admin_ids(groups_get_current_group(), 'array');
	if(array_search(bp_loggedin_user_id(), $group_admins) !== false){
		?>
	<div class="bb-video-album-actions">
		<a href="#" id="bp-create-video-album" class="bb-add-video button small outline">
			<?php esc_html_e('Add Video Albums', 'buddyboss'); ?>
		</a>
	</div>
	<?php
	}
	?>
</div>
<div id="bp-custom-video-albums-directory" data-current-album="0"><div class='albumclickable item'><span folder-id='0'>Albums</span></div></div>
<div id="custom-video-albums-stream" class="custom-video-albums" data-bp-custom-list="custom-video-albums" data-album-id="0">
		
	</div><!-- .video-albums -->
</div>
<?php
bp_get_template_part('video/uploader');
bp_get_template_part('video/create-video-album');
?>


<div class="bb-video-container bb-media-container group-video">
	<?php

	bp_get_template_part('media/theatre');
	bp_get_template_part('video/theatre');
	bp_get_template_part('document/theatre');

	switch (bp_current_action()):

		// Home/Video.
		case 'videos':
			if (
				bp_is_group_video() &&
				(
					groups_can_user_manage_video(bp_loggedin_user_id(), bp_get_current_group_id()) ||
					groups_is_user_mod(bp_loggedin_user_id(), bp_get_current_group_id()) ||
					groups_is_user_admin(bp_loggedin_user_id(), bp_get_current_group_id())
				)
			) {
				bp_get_template_part('video/add-video');
			} else {
				?>
				<h2 class="bb-title">
					<?php esc_html_e('Videos', 'buddyboss'); ?>
				</h2>
				<?php
			}

			bp_nouveau_group_hook('before', 'video_content');

			bp_get_template_part('video/actions');

			?>
			<div id="video-stream" class="video" data-bp-list="video">

				<div id="bp-ajax-loader">
					<?php bp_nouveau_user_feedback('group-video-loading'); ?>
				</div>

			</div><!-- .media -->
			<?php

			bp_nouveau_group_hook('after', 'video_content');

			break;

		// Any other.
		default:
			bp_get_template_part('groups/single/plugins');
			break;
	endswitch;
	?>
</div>