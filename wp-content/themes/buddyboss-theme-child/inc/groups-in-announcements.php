<?php
function bb_groups_in_announcements()
{

	add_meta_box(
		'groups_in_announcements',
		'Announcement Data',
		'add_groups_name_checkbox',
		'announcements',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'bb_groups_in_announcements');


function add_groups_name_checkbox($post)
{
	$bb_group_name			=	groups_get_groups();
	$getSelectedData		=	get_post_meta($post->ID, 'bb_group_name', true);
	$getAnnouncementDate	=	get_post_meta($post->ID, 'bb_announcement_date', true);
	$getAnnouncementDate	=	(!empty($getAnnouncementDate)) ? date("Y-m-d", $getAnnouncementDate) : '';

	echo wp_sprintf('<strong>%s</strong><br/>', __('Select Group'));
	foreach ($bb_group_name['groups'] as $groups) :
		$isSelected = (is_array($getSelectedData) &&  in_array($groups->id, $getSelectedData)) ? 'checked="checked"' : ((!empty($getSelectedData) &&  ($getSelectedData == $groups->id)) ? 'checked="checked"' : '');
		echo wp_sprintf('<label><input type="checkbox" name="bb_group_name[]" value="%d" %s /> %s </label><br/><br/>', $groups->id, $isSelected, $groups->name);
	endforeach;

	echo wp_sprintf('<label> <strong>%s</strong>  <br/><br/>  <input type="date" name="bb_announcement_date" value="%s"  />  </label><br/><br/>', __("Announcement Date"),  $getAnnouncementDate);
}
function save_selected_group_name($post_id)
{
	if (isset($_POST['bb_group_name'])) {
		$groupsname = $_POST['bb_group_name'];
		update_post_meta($post_id, 'bb_group_name', $groupsname);
	}
	if (isset($_POST['bb_announcement_date'])) {
		update_post_meta($post_id, 'bb_announcement_date', strtotime($_POST['bb_announcement_date']));
	}
}
add_action('save_post', 'save_selected_group_name');
