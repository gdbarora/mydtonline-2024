<?php
// Add the following code to your theme's functions.php or in a custom plugin

// Hook to handle AJAX requests
add_action('wp_ajax_bp_custom_create_video_album', 'bp_custom_create_video_album_callback');

function getAlbumChilds($parent)
{
    global $wpdb;

    // Replace 'bp_video_albums' with your actual table name
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve child folders
    $query = $wpdb->prepare("SELECT child_folders FROM $table_name WHERE id = %d", $parent);

    // Fetch results from the database
    $child_folders = $wpdb->get_var($query);
    // wp_send_json($child_folders);

    // Return an empty array if no child folders are found
    return !empty($child_folders) ? $child_folders : array();
}

function getAlbumVideos($parent)
{
    global $wpdb;

    // Replace 'bp_video_albums' with your actual table name
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve attached videos
    $query = $wpdb->prepare("SELECT attached_videos FROM $table_name WHERE id = %d", $parent);

    // Fetch results from the database
    $attached_videos = $wpdb->get_var($query);

    // Return the raw response
    return $attached_videos;
}
function bp_custom_create_video_album_callback()
{
    // Get the album title from the AJAX request
    $album_title = sanitize_text_field($_POST['albumTitle']);
    $group_id = sanitize_text_field($_POST['groupId']);
    $parent = sanitize_text_field($_POST['parentFolderId']);
    $user_id = sanitize_text_field($_POST['user_id']);

    

    // Check if the table exists
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        // Table doesn't exist, create it
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            userid mediumint(9) NOT NULL,
            groupid mediumint(9) DEFAULT 0,
            parent mediumint(9) DEFAULT 0,
            title text NOT NULL,
            child_folders longtext NOT NULL,
            attached_videos longtext NOT NULL,
            date_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            date_modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Insert data into the table
    $user_id = get_current_user_id();
    $date_created = current_time('mysql');
    $date_modified = current_time('mysql');

    if ($parent !== '0') {
        $childFolders = maybe_unserialize(getAlbumChilds($parent));
    }

    $result = $wpdb->insert(
        $table_name,
        array(
            'userid' => $user_id,
            'groupid' => $group_id,
            'parent' => $parent,
            'title' => $album_title,
            'date_created' => $date_created,
            'date_modified' => $date_modified, // Added date_modified
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s')
    );
    if ($parent != 0) {
        $inserted_id = $wpdb->insert_id;
        $childFolders[] = $inserted_id;

        update_child_folders_by_id($parent, $childFolders);
    }

    if ($result) {
        // Data inserted successfully
        $response = array(
            'success' => true,
            'data' => array(
                'id' => $wpdb->insert_id,
                'userid' => $user_id,
                'groupid' => $group_id,
                'parent' => $parent,
                'title' => $album_title,
                'child_folders' => $childFolders,
                'attached_videos' => '',
                'date_created' => $date_created,
                'date_modified' => $date_modified,
            ),
        );
    } else {
        // Error inserting data
        $response = array(
            'success' => false,
            'error' => 'Could not create album',
        );
    }

    // Send JSON response
    wp_send_json($response);
}


// Add the AJAX action for getting video albums
add_action('wp_ajax_bp_custom_get_video_album', 'bp_custom_get_video_album');
add_action('wp_ajax_nopriv_bp_custom_get_video_album', 'bp_custom_get_video_album'); // If you want to allow non-logged-in users

function bp_custom_get_video_album()
{
    // Get parameters from AJAX request
    $group_id = sanitize_text_field($_POST['groupId']);
    $parent_folder_id = sanitize_text_field($_POST['parentFolderId']);


    if ($parent_folder_id !== '0') {
        // Assume you have a function to get video albums
        $albumIds = maybe_unserialize(getAlbumChilds($parent_folder_id));
        $videoIds = maybe_unserialize(getAlbumVideos($parent_folder_id));
        $albums = getAlbumsFromIds($albumIds);
    } else {
        $albums = get_video_albums($group_id, $parent_folder_id);
    }

    $html = get_video_albums_template($albums, $videoIds);
    $ancestors = getDirectoryHTML($parent_folder_id);

    // Send JSON response
    wp_send_json(array('albums' => $albums, 'content' => $html, 'currentAlbumId' => $parent_folder_id, 'directoryHTML'=>$ancestors));
}

function getDirectoryHTML($folderId) {
    $ancestors = array($folderId);
    
    // Fetch all ancestors until the parent ID is empty
    while ($folderId != 0) {
        $folderId = getParent($folderId);
        if ($folderId !== null) {
            $ancestors[] = $folderId;
        }
    }
// Reverse the array
$reversedAncestors = array_reverse($ancestors);
$html = "";

    foreach($reversedAncestors as $ancestor){
        $albumTitle = getAlbumTitle($ancestor);
       
        $html.=" <div class='albumclickable item'><span folder-id='{$ancestor}'>{$albumTitle}</span></div>";
    }

    // Now, $ancestors array contains all ancestor IDs
    // You can use it to generate HTML or perform other actions
    return $html;
}

function getAlbumTitle($id=0){
    
    global $wpdb;
    // Replace 'bp_video_albums' with your actual table name
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve the parent ID for the specified ID
    $query = $wpdb->prepare("SELECT title FROM $table_name WHERE id = %d", $id);

    // Fetch the parent ID from the database
    $title = $wpdb->get_var($query);

    return $id != 0? $title: 'Albums';
}
function getParent($id = 0) {
    global $wpdb;

    // Replace 'bp_video_albums' with your actual table name
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve the parent ID for the specified ID
    $query = $wpdb->prepare("SELECT parent FROM $table_name WHERE id = %d", $id);

    // Fetch the parent ID from the database
    $parentId = $wpdb->get_var($query);

    return $parentId;
}

function getAlbumsFromIds($ids)
{
    global $wpdb;

    // Replace 'bp_video_albums' with your actual table name
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Create a comma-separated list of IDs for the IN clause
    $ids_list = implode(',', array_map('intval', $ids));

    // Query to retrieve attached videos for the specified IDs
    $query = "SELECT * FROM $table_name WHERE id IN ($ids_list)";

    // Fetch results from the database
    $albums_data = $wpdb->get_results($query, ARRAY_A);

    return $albums_data;
}

function get_video_albums($group_id, $parent_folder_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Modify the query based on your requirements
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE groupid = %d AND parent = %d", $group_id, $parent_folder_id);

    // Fetch results from the database
    $albums = $wpdb->get_results($query, ARRAY_A);

    return $albums;
}

function get_album_data($parent_folder_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Modify the query based on your requirements
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $parent_folder_id);

    // Fetch results from the database
    $album_data = $wpdb->get_results($query, ARRAY_A);

    return $album_data;
}

function getVideoThumbnailAlbum($albumId){
    global $wpdb;
    $videos = maybe_unserialize(getAlbumVideos($albumId));
   // Replace 'wp_bp_media' with your actual table name
   $table_name = $wpdb->prefix . 'bp_media';

   // Prepare and execute the SQL query
   $query = $wpdb->prepare("SELECT attachment_id FROM $table_name WHERE id = %d", $videos[0]);
   $attachmentId = $wpdb->get_var($query);
   $previewImageId = get_post_meta($attachmentId, 'bp_video_preview_thumbnail_id', true);
   $previewImage = get_post_meta($previewImageId, '_wp_attachment_metadata', true);
//    echo '<pre>';
//    print_r($previewImage);
//    echo '</pre>';
return '';
}
// getVideoThumbnailAlbum(9);
function get_video_albums_template($albums = array(), $videos = array())
{
    ob_start(); // Start output buffering

    echo '<div id="albums-dir-list" class="bb-albums bb-albums-dir-list">
        <ul class="bb-albums-list">';
    if (!empty($albums) || !empty($videos)):
        foreach ($albums as $album) {
            $videoCount = !empty($album['attached_videos']) ? count(maybe_unserialize($album['attached_videos'])) : 0;
            $folderCount = !empty($album['child_folders']) ? count(maybe_unserialize($album['child_folders'])) : 0;
            echo '<li class="bb-album-list-item custom-video-album">
            <div class="bb-album-cover-wrap">
                <a class="bs-cover-wrap" data-album-id="' . esc_attr($album['id']) . '">
             <span class="delete-video-album"><i class="fa fa-trash"></i></span>

                <img decoding="async" src="'.esc_url('https://www.pngkit.com/png/full/267-2678423_bacteria-video-thumbnail-default.png').'">
                    <div class="bb-album-content-wrap">
                        <h4>' . esc_html($album['title']) . '</h4>
                        <span class="bb-album_date">' . esc_html(date('F j, Y', strtotime($album['date_created']))) . '</span>
                        <div class="bb-album_stats">
                            <span class="bb-album_stats_videos"><i class="bb-icon-l bb-icon-video"></i> ' . esc_html($videoCount) . '</span>
                            <span class="bb-album_stats_spacer">&middot;</span>
                            <span class="bb-album_stats_videos"><i class="bb-icon-l bb-icon-folder"></i> ' . esc_html($folderCount) . '</span>
                        </div>
                    </div>
                </a>
            </div>
        </li>';
        }
    if(!empty($videos)):

    $include_ids = implode(',', $videos);

    // Set up the parameters for the bp_has_videos loop
    $args = array(  // Set the number of videos to display per page
        'include' => $include_ids,  // Include specific video IDs
    );

    // Check if there are videos
    if (bp_has_video($args)):
        while (bp_video()):
            bp_the_video();
            bp_get_template_part('video/entry');
        endwhile;
    endif;
endif;
    else:
        echo '<li>No albums found</li>';
    endif;

    echo '</ul></div>';



    $html_output = ob_get_clean(); // Get the buffered output and clean the buffer
    return $html_output;
}

function update_child_folders_by_id($record_id, $child_folders_data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Serialize the array before updating
    $serialized_data = maybe_serialize($child_folders_data);

    // Specify the conditions for the update
    $where = array('id' => intval($record_id));

    // Update the record
    $result = $wpdb->update(
        $table_name,
        array('child_folders' => $serialized_data),
        $where,
        array('%s'), // Assuming child_folders is a string, adjust accordingly if it's a different type
        array('%d')  // Format for the WHERE clause ID
    );

    return $result !== false; // Return true if the update was successful, false otherwise
}

add_action('wp_ajax_bp_move_video_to_album', 'bp_move_video_to_album_callback');

function bp_move_video_to_album_callback()
{
    // Your server-side logic here
    $groupId = $_POST['groupId'];
    $parentFolderId = $_POST['parentFolderId'];
    $attachmentId = $_POST['attachmentId'];
    $message = '';

    // Perform actions, update the database, etc.

    $previousVideos = maybe_unserialize(getAlbumVideos($parentFolderId));
    if (!in_array($attachmentId, $previousVideos)) {
        $newVideos = $previousVideos;
        $newVideos[] = $attachmentId;
        $previousParent = findPreviousParent($attachmentId);

        updateParentVideos($newVideos, $parentFolderId, $previousParent, $attachmentId);
        $message = 'Video has been moved successfullly.';

    } else {
        $message = 'Video is already in selected Folder.';
    }


    $response = array('success' => true, 'message' => $message);
    wp_send_json($response);
    wp_die(); // Always include this to terminate script execution
}

function removeVideoFromParent($videoId, $parentId){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Retrieve the previous parent videos
    $previousParentVideos = maybe_unserialize(getAlbumVideos($parentId));

    // Find the key of the attachment ID in the array
    $keyToRemove = array_search($videoId, $previousParentVideos);

    // If the attachment ID is found, unset the key from the array
    if ($keyToRemove !== false) {
        unset($previousParentVideos[$keyToRemove]);
    }

    // Serialize the array before updating the previous parent
    $serialized_previous_parent_data = maybe_serialize($previousParentVideos);

    // Specify the conditions for updating the previous parent record
    $where_previous_parent = array('id' => intval($parentId));

    // Update the previous parent record
    $result_previous_parent = $wpdb->update(
        $table_name,
        array('attached_videos' => $serialized_previous_parent_data),
        $where_previous_parent,
        array('%s'), // Assuming attached_videos is a string, adjust accordingly if it's a different type
        array('%d')  // Format for the WHERE clause ID
    );
    return $result_previous_parent;
}
function updateParentVideos($newVideos, $parentId, $previousParentId, $attachmentId) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

$result_previous_parent = removeVideoFromParent($attachmentId, $previousParentId);
    // Serialize the array before updating the current parent
    $serialized_data = maybe_serialize($newVideos);

    // Specify the conditions for updating the current parent record
    $where_current_parent = array('id' => intval($parentId));

    // Update the current parent record
    $result_current_parent = $wpdb->update(
        $table_name,
        array('attached_videos' => $serialized_data),
        $where_current_parent,
        array('%s'), // Assuming attached_videos is a string, adjust accordingly if it's a different type
        array('%d')  // Format for the WHERE clause ID
    );

    return $result_previous_parent !== false && $result_current_parent !== false;
}

function findPreviousParent($videoId){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve the parent album ID for the given video
    $parentAlbumId = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM $table_name WHERE attached_videos LIKE '%%%s%%'",
            $videoId
        )
    );

    return $parentAlbumId;
}

// Add AJAX action for deleting video albums
add_action('wp_ajax_bp_delete_video_album', 'bp_delete_video_album_callback');

function bp_delete_video_album_callback() {
    $selectedFolder = isset($_POST['parentFolderId']) ? intval($_POST['parentFolderId']) : 0;
    modifyParentAlbums($selectedFolder);
    deleteVideoAlbum($selectedFolder);
    wp_send_json_success('Album deleted successfully');
}

function getParentAlbum($id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Query to retrieve the parent album ID for the given video
    $parentAlbumId = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT parent FROM $table_name WHERE id = %d",
            $id
        )
    );

    return $parentAlbumId;
}


function modifyParentAlbums($id){
    $parentId = getParentAlbum($id);
    $childFolders = maybe_unserialize(getAlbumChilds($parentId));
    if ($parentId != 0) {
        // Unset the specific ID from the child folders
        $indexToRemove = array_search($id, $childFolders);
        if ($indexToRemove !== false) {
            unset($childFolders[$indexToRemove]);
        }

        $result = update_child_folders_by_id($parentId, $childFolders);
    }
    // Return true if the deletion was successful, otherwise false
    return $result !== false;
}

function deleteVideoAlbum($id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bp_video_albums';

    // Delete the album with the given ID
    $result = $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );


    // Return true if the deletion was successful, otherwise false
    return $result !== false;
}


// Add your custom function to the bp_video_deleted_videos action hook
//add_action('bp_video_deleted_videos', 'my_custom_function', 10, 1);

// Define your custom function
function removeVideoIdFromAlbum($videos) {
    $video_ids_deleted = wp_parse_id_list( wp_list_pluck( $videos, 'id' ) );
    foreach($video_ids_deleted as $id){
        $parentId = findPreviousParent($id);
        if ($parentId != 0) {
    
            $result = removeVideoFromParent($id, $parentId);

        }
        // Return true if the deletion was successful, otherwise false
    }
    return true;
  
}

// Add your custom function to the bp_video_after_delete action hook
add_action('bp_video_after_delete', 'removeVideoIdFromAlbum', 10, 1);
?>