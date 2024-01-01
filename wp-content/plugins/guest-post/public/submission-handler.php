<?php

add_action('wp_ajax_gp_post_submission', 'gp_post_submission_callback');
add_action('wp_ajax_nopriv_gp_post_submission', 'gp_post_submission_callback');

function gp_post_submission_callback()
{
    check_ajax_referer('guest-post', 'security');

    $post_data = json_decode(stripslashes_deep($_POST['info']));
    if (is_object($post_data)) :

        $post_title   = sanitize_text_field($post_data->title);
        $post_content = wp_filter_post_kses($post_data->content);
        $groupId = sanitize_text_field($post_data->groupid);
        $post_date   = strtotime(sanitize_text_field($post_data->date));

        $postarr = array(
            'post_content' => $post_content,
            'post_title'   => $post_title,
            'post_status'  => 'publish',
            'post_type'    => 'announcements',
        );


        $post_id = wp_insert_post($postarr);
        if (!is_wp_error($post_id)) {
            add_post_meta($post_id, 'bb_group_name', $groupId);
            add_post_meta($post_id, 'bb_announcement_date', $post_date);
            add_post_meta($post_id, 'bb_user_subbmited', true);
            wp_send_json_success(array('post_id' => $post_id), 200);
        } else {
            wp_send_json_error($post_id->get_error_message());
        }
    else :
        wp_send_json_error(array('message' => 'Something went wrong please try again later'));
    endif;
    wp_die();
}

add_action('publish_post', 'guest_post_admin_notify_email', 10, 2);

function guest_post_admin_notify_email($post_id, $post)
{

    if (wp_is_post_revision($post_id))
        return;

    $subject = 'A post has been created';

    $message = "A post has been created on your website:\n\n";
    $message .= "Title:" . $post->post_title . ", \n\n Type: " . $post->post_type;

    wp_mail(get_option('admin_email'), $subject, $message);
}
