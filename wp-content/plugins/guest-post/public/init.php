<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once GPS_PLUGIN_PATH . 'public/submission-handler.php';

/**
 * Render Scripts and Styles for frontend uses
 */
add_action( "wp_enqueue_scripts", "guest_post_enueue_scripts" );

function guest_post_enueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'bootstrap', GPS_PLUGIN_URI . 'public/assets/js/bootstrap.bundle.min.js', array(), '4.4.1', true );
    wp_enqueue_style( 'bootstrap', GPS_PLUGIN_URI . 'public/assets/css/bootstrap.min.css', array(), '4.4.1', 'all' );
    wp_enqueue_style( 'guest-post', GPS_PLUGIN_URI . 'public/assets/css/guest-post.css', array(), strtotime( 'now' ), 'all' );
    wp_enqueue_script( 'guest-post', GPS_PLUGIN_URI . 'public/assets/js/guest-post.js', array(), strtotime( 'now' ), true );
    wp_localize_script( 'guest-post', 'gp_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( "guest-post" ),
        'sucess'  => __( 'Post created successfully', 'guest-post' ),
        'error'   => __( 'Something went wrong please try again later', 'guest-post' )
            )
    );
}

/**
 * Render Frontend Post Form  
 * $atts : role , pass user role , default - 'author'
 * This form only visible passed user role user
 */
add_shortcode( 'GUEST_POST_FORM', 'guest_post_form_callback' );

function guest_post_form_callback( $atts ) {
    ob_start();

    $atts         = shortcode_atts( array( 'role' => 'author' ), $atts, 'GUEST_POST_FORM' );
    $user_role    = $atts[ 'role' ];
    $current_user = wp_get_current_user();
    

    //if ( is_user_logged_in() && in_array( $user_role, ( array ) $current_user->roles ) ):
        require_once GPS_PLUGIN_PATH . 'public/post-form.php';
    // else:
    //     echo wp_sprintf( '<div class="alert alert-warning"><strong>Hey!!, </strong> %s  %s</div>', __( 'Please login with user role - ', 'guest-post' ), $user_role );
    // endif;
    $output = ob_get_clean();
    return $output;
}

/**
 * Show Only Current user media in wp.media popup
 */
add_filter( 'ajax_query_attachments_args', 'guest_post_user_attachments' );

function guest_post_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can( 'activate_plugins' ) && !current_user_can( 'edit_others_posts' ) ) {
        $query[ 'author' ] = $user_id;
    }
    return $query;
}

/* * */
add_shortcode( 'GUEST_POST_LIST', 'guest_post_list_callback' );

function guest_post_list_callback( $atts ) {
    ob_start();

    require_once GPS_PLUGIN_PATH . 'public/posts-list.php';

    $output = ob_get_clean();
    return $output;
}
