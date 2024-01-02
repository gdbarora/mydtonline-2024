<?php
//This code block is for gamipress notifications and some basic frontend changes when profile is updated
function bb_get_achievement_id_by_title(string $title = '', string $post_type = '')
{
    $posts = get_posts(
        array(
            'post_type' => $post_type,
            'title' => $title,
            'numberposts' => 1,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'orderby' => 'post_date ID',
            'order' => 'ASC',
            'fields' => 'ids'
        )
    );

    return empty($posts) ? get_the_ID() : $posts[0];
}

//To get the latest achievement by type
function get_latest_achievement_by_type($user_id, $type)
{
    global $wpdb;
    // Adjust the table name based on your database prefix
    $table_name = $wpdb->prefix . 'gamipress_user_earnings';
    $query = $wpdb->prepare(
        "SELECT title
        FROM $table_name
        WHERE post_type = %s
        AND user_id = %d
        ORDER BY date DESC
        LIMIT 1",
        $type,
        $user_id
    );
    $result = $wpdb->get_var($query);
    return $result ? $result : false;
}

// Include custom notification file.
require_once trailingslashit(get_template_directory() . '-child') . 'class-gamekeeper-notification.php';

add_action(
    'bp_init',
    function () {
        // Register custom notification in preferences screen.
        if (class_exists('BP_Custom_Notification')) {
            BP_Custom_Notification::instance();
        }
    }
);
function handleJobFieldUpdate($jobFieldId, $jobFieldOldValue, $jobFieldNewValue, $userId)
{
    if ($jobFieldNewValue === 'Not Yet') {
        xprofile_set_field_data(1905, $userId, date("9999-13-11 00:00:00"));
    }
}
function sendNotificationToGameKeepers($achievementId, $userId, $componentAction)
{
    $achievementTitle = get_the_title($achievementId);
    $achievementType = get_post_type($achievementId);

    $gameKeepers = get_users(array('fields' => 'ID', 'role__in' => array('game_keeper')));
    if (is_array($gameKeepers) && count($gameKeepers) > 0) {
        foreach ($gameKeepers as $gameKeeper) {
            $notification_args = array(
                'user_id' => $gameKeeper,
                'component_name' => 'gamekeeper_notifications',
                'component_action' => $componentAction,
                'item_id' => $achievementId,
                'secondary_item_id' => $userId,
                'recorded_time' => bp_core_current_time(),
                'is_new' => 1,
            );
            $achievementStatus = get_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, true);
            $isReviewed = ($achievementStatus === '1' || $achievementStatus === '0');
            if (!$isReviewed) {
                bp_notifications_add_notification($notification_args);
            }
        }
    }

    $achievementTitle = get_the_title($achievementId);
    $achievementType = get_post_type($achievementId);

    $achievementStatus = get_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, true);
    if ($achievementStatus !== 1) {
        update_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, 2);
    }
}
function handleEnagicRankUpdate($enagicRankFieldId, $enagicRankOldValue, $enagicRankNewValue, $userId)
{
    $achievementId = bb_get_achievement_id_by_title($enagicRankNewValue, 'enagic-rank');
    $isHigherRank = ($enagicRankNewValue > $enagicRankOldValue);
    $isApprovalRequired = ($enagicRankNewValue > '5A');

    if ($isHigherRank) {
        if ($isApprovalRequired) {
            sendNotificationToGameKeepers($achievementId, $userId, 'member_enagic_rank_update');
            xprofile_set_field_data($enagicRankFieldId, $userId, $enagicRankOldValue);
        } else {
            $hasUserAchievement = gamipress_has_user_earned_achievement($achievementId, $userId);
            if (!$hasUserAchievement) {
                gamipress_award_achievement_to_user($achievementId, $userId);
            }
            xprofile_set_field_data($enagicRankFieldId, $userId, $enagicRankNewValue);
        }

    } else {
        xprofile_set_field_data($enagicRankFieldId, $userId, $enagicRankOldValue);
    }

}

function handleSOCRankUpdate($socRankFieldId, $socRankOldvalue, $socRankNewValue, $userId)
{
    $achievementId = bb_get_achievement_id_by_title($socRankNewValue, 'card-star');
    $isHigherRank = ($socRankNewValue > $socRankOldvalue);
    $isApprovalRequired = ($socRankNewValue > 'Star 4');

    if ($isHigherRank) {
        if ($isApprovalRequired) {
            sendNotificationToGameKeepers($achievementId, $userId, 'member_enagic_rank_update');
            xprofile_set_field_data($socRankFieldId, $userId, $socRankOldvalue);
        } else {
            $hasUserAchievement = gamipress_has_user_earned_achievement($achievementId, $userId);
            if (!$hasUserAchievement) {
                gamipress_award_achievement_to_user($achievementId, $userId);
            }
            xprofile_set_field_data($socRankFieldId, $userId, $socRankOldvalue);
        }

    } else {
        xprofile_set_field_data($socRankFieldId, $userId, $socRankOldvalue);
    }

}


//The below function is used to make the profile completion widget show the hidden field complete
add_action('xprofile_updated_profile', 'update_achievements_with_review', 10, 5);
function update_achievements_with_review($user_id, $field_ids, $errors, $old_values, $new_values)
{
    //Field Ids;
    $jobFieldId = 2335;
    $enagicRankFieldId = 8;
    $socRankFieldId = 1896;

    //Field Values
    $jobFieldOldValue = isset($old_values[$jobFieldId]['value']) ? $old_values[$jobFieldId]['value'] : '';
    $jobFieldNewValue = isset($new_values[$jobFieldId]['value']) ? $new_values[$jobFieldId]['value'] : '';

    $enagicRankOldValue = isset($old_values[$enagicRankFieldId]) ? $old_values[$enagicRankFieldId]['value'] : '1A';
    $enagicRankNewValue = isset($new_values[$enagicRankFieldId]) ? $new_values[$enagicRankFieldId]['value'] : '1A';

    $socRankOldvalue = isset($old_values[$socRankFieldId]) ? $old_values[$socRankFieldId]['value'] : 'Star 1';
    $socRankNewValue = isset($new_values[$socRankFieldId]) ? $new_values[$socRankFieldId]['value'] : 'Star 1';


    handleJobFieldUpdate($jobFieldId, $jobFieldOldValue, $jobFieldNewValue, $user_id);

    handleEnagicRankUpdate($enagicRankFieldId, $enagicRankOldValue, $enagicRankNewValue, $user_id);

    handleSOCRankUpdate($socRankFieldId, $socRankOldvalue, $socRankNewValue, $user_id);
}



// Ajax handler for awarding requested achievement
add_action('wp_ajax_manageRequestedAchievement', 'manageRequestedAchievement');
function manageRequestedAchievement()
{
    $achievementId = intval($_POST['achievement_id']);
    $userId = intval($_POST['user_id']);
    $notificationId = intval($_POST['notification_id']);
    $action = sanitize_text_field($_POST['review_action']);

    $achievementTitle = get_the_title($achievementId);
    $achievementType = get_post_type($achievementId);

    $profileFieldId = $achievementType === 'enagic-rank' ? '8' : '1896';

    $currentUserId = get_current_user_id();

    $achievementReviews = get_user_meta($userId, 'reviewedAchievement_' . $achievementType, true);
    $newAchievementReviewsArray = !empty($achievementReviews) ? $achievementReviews : array(); //Array to store updated reviews

    $reviewedAchievementData = array(
        'Status' => $action,
        'Reviewed By' => $currentUserId,
        'Achievement' => $achievementId,
        'Time' => date('Y-m-d H:i:s'),
    );
    $newAchievementReviewsArray[$notificationId] = $reviewedAchievementData;
    update_user_meta($userId, 'reviewedAchievement_' . $achievementType, $newAchievementReviewsArray);

    $achievementStatus = get_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, true);
    $hasAchievementApproved = ($achievementStatus === 1);

    if (!$hasAchievementApproved) { //Check if achievement is not already approved
        if ($action === 'approve') {

            $hasUserAchievement = gamipress_has_user_earned_achievement($achievementId, $userId);
            if (!$hasUserAchievement) {
                gamipress_award_achievement_to_user($achievementId, $userId);
            }
            update_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, 1);
            xprofile_set_field_data($profileFieldId, $userId, $achievementTitle);
            $reviewedAchievementData['Message'] = 'The achievement ' . $achievementTitle . ' is successfully approved.';
        } else {
            update_user_meta($userId, 'awarded_' . $achievementType . '_' . $achievementTitle, 0);
            $reviewedAchievementData['Message'] = 'The achievement ' . $achievementTitle . ' is successfully rejected.';
        }
    } else {
        $reviewedAchievementData['Message'] = 'The achievement ' . $achievementTitle . ' is already approved.';
    }

    wp_send_json_success($reviewedAchievementData);
}

add_filter('bp_get_the_notification_description', 'custom_notification_description_filter', 10, 2);

function custom_notification_description_filter($description, $notification)
{
    // Check if this is the notification you want to customize
    if ($notification->component_name === 'gamekeeper_notifications' && ($notification->component_action === 'member_enagic_rank_update' || $notification->component_action === 'member_soc_rank_update')) {
        // Fetch additional data based on secondary item ID
        $requesterId = $notification->secondary_item_id;

        $achievementId = $notification->item_id;
        $achievementTitle = get_the_title($achievementId);
        $achievementType = get_post_type($achievementId);

        $achievementStatus = get_user_meta($requesterId, 'awarded_' . $achievementType . '_' . $achievementTitle, true);
        $hasAchievementReviewed = ($achievementStatus === '2');


        if ($hasAchievementReviewed) {
            $approveBtn = '<button class="awardAchievement button small ' . $achievementTitle . '" achievement-id="' . $achievementId . '" user-id="' . $requesterId . '" notification-id="' . $notification->id . '" review-action="approve">Approve</button>';
            $rejectBtn = '<button class="rejectAchievement button outline small ' . $achievementTitle . '" achievement-id="' . $achievementId . '" user-id="' . $requesterId . '" notification-id="' . $notification->id . '" review-action="reject">Reject</button>';
        } else {
            $achievementReviews = get_user_meta($requesterId, 'reviewedAchievement_' . $achievementType, true);
            if ($achievementStatus === '1') {
                $message = 'The user request has been approved.';
            } else {
                $message = 'The user request has been rejected.';
            }
        }

        $description .= '<div class="reviewAchievementUpdate" style="margin-top: 3px; gap: 5px; display: flex;">' . $approveBtn . $rejectBtn . $message . '</div>';
    }
    return $description;
}
