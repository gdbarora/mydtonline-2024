<?php
function profile_image_callback()
{
    check_ajax_referer('file_upload', 'security');
    $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

    $output = [
        'status' => 400,
        'message' => __('Something went wrong please try again.'),
    ];
    if (in_array($_FILES['file']['type'], $arr_img_ext)) :

        $upload = wp_handle_upload($_FILES['file'], array('test_form' => false));

        $attachment_id = wp_insert_attachment(
            array(
                'guid'           => $upload['url'],
                'post_mime_type' => $upload['type'],
                'post_title'     => basename($upload['file']),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $upload['file']
        );

        if (is_wp_error($attachment_id) || !$attachment_id) :
            $output = [
                'status' => 400,
                'message' => __('Something went wrong please try again.'),
            ];

        else :
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            wp_update_attachment_metadata(
                $attachment_id,
                wp_generate_attachment_metadata($attachment_id, $upload['file'])
            );

            $output = [
                'status' => 200,
                'message' => __('Head shot successfully uploaded.'),
                'attachment_id' => $attachment_id,
                'attachment_url' => wp_get_attachment_url($attachment_id),
            ];
        endif;

    endif;
    wp_send_json($output);
    wp_die();
}
add_action('wp_ajax_profile_image', 'profile_image_callback');
add_action('wp_ajax_nopriv_profile_image', 'profile_image_callback');
