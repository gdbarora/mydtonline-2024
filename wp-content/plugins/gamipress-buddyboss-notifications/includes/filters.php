<?php
/**
 * Filters
 *
 * @package     GamiPress\BuddyBoss_Notifications\Filters
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register custom component
 *
 * @since 1.0.0
 *
 * @param array $component_names
 *
 * @return array
 */
function gamipress_buddyboss_notifications_register_notifications_component( $component_names = array() ) {

    // Force $component_names to be an array
    if ( ! is_array( $component_names ) ) {
        $component_names = array();
    }

    // Add the custom component
    array_push( $component_names, 'gamipress_buddyboss_notifications' );

    return $component_names;

}
add_filter( 'bp_notifications_get_registered_components', 'gamipress_buddyboss_notifications_register_notifications_component' );

/**
 * BuddyBoss Theme avatar support
 *
 * @since 1.0.0
 */
function gamipress_buddyboss_notifications_theme_avatar() {

    $notification = buddypress()->notifications->query_loop->notification;

    $post_id = $notification->item_id;
    $post = gamipress_get_post( $post_id );
    $link = '#';
    $image = '';

    if( ! $post ) {
        return;
    }

    if( in_array( $post->post_type, gamipress_get_achievement_types_slugs() ) ) {

        // Achievement
        $link = get_post_permalink( $post_id );
        $image = gamipress_get_achievement_post_thumbnail( $post_id );

    } else if( $post->post_type === 'step' ) {

        // Step
        $achievement = gamipress_get_step_achievement( $post->ID );

        if( $achievement ) {
            $link = get_post_permalink( $achievement->ID );
            $image = gamipress_get_achievement_post_thumbnail( $achievement->ID );
        }

    } else if( in_array( $post->post_type, array( 'points-award', 'points-deduct' ) ) ) {

        // Points award and deduct
        $points_type_id = gamipress_get_post_field( 'post_parent', $post->ID );
        $image = gamipress_get_points_type_thumbnail( $points_type_id );

    } else if( in_array( $post->post_type, gamipress_get_rank_types_slugs() ) ) {

        // Rank
        $link = get_post_permalink( $post_id );
        $image = gamipress_get_rank_post_thumbnail( $post_id );

    } else if( $post->post_type === 'rank-requirement' ) {

        // Rank requirement
        $rank = gamipress_get_rank_requirement_rank( $post->ID );

        if( $rank ) {
            $link = get_post_permalink( $rank->ID );
            $image = gamipress_get_rank_post_thumbnail( $rank->ID );
        }

    }

    if( ! empty( $image ) ) : ?>
        <a href="<?php echo $link ?>">
            <?php echo $image; ?>
        </a>
    <?php endif;

}
add_action( 'bb_notification_avatar_gamipress_buddyboss_notifications', 'gamipress_buddyboss_notifications_theme_avatar' );

/**
 * Filters the notification content for notifications created by plugins.
 * If your plugin extends the {@link BP_Component} class, you should use the
 * 'notification_callback' parameter in your extended
 * {@link BP_Component::setup_globals()} method instead.
 *
 * @since BuddyBoss 1.9.0
 * @since BuddyBoss 2.6.0 Added $component_action_name, $component_name, $id as parameters.
 *
 * @param string $content               Component action. Deprecated. Do not do checks against this! Use
 *                                      the 6th parameter instead - $component_action_name.
 * @param int    $item_id               Notification item ID.
 * @param int    $secondary_item_id     Notification secondary item ID.
 * @param int    $action_item_count     Number of notifications with the same action.
 * @param string $format                Format of return. Either 'string' or 'object'.
 * @param string $component_action_name Canonical notification action.
 * @param string $component_name        Notification component ID.
 * @param int    $id                    Notification ID.
 *
 * @return string|array If $format is 'string', return a string of the notification content.
 *                      If $format is 'object', return an array formatted like:
 *                      array( 'text' => 'CONTENT', 'link' => 'LINK' )
 */
function gamipress_buddyboss_notifications_format_notifications( $content, $item_id, $secondary_item_id, $action_item_count, $format, $component_action_name, $component_name, $id ) {

    // Bail if not is our component
    if( $component_action_name !== 'gamipress_buddyboss_notifications' ) {
        return $content;
    }

    $user_id = get_current_user_id();

    /**
     * Filter to override the notification content for this item
     *
     * @since 1.0.0
     *
     * @param string  $content
     * @param int   $user_id
     * @param int   $post_id
     * @param int   $user_earning_id
     *
     * @return string
     */
    $content = apply_filters( 'gamipress_buddyboss_notifications_content', $content, $user_id, $item_id, $secondary_item_id );

    $content = do_shortcode( $content );

    if( $format === 'object' ) {
        $content = array(
            'text' => $content,
            'link' => ''
        );
    }

    return $content;

}
add_filter( 'bp_notifications_get_notifications_for_user', 'gamipress_buddyboss_notifications_format_notifications', 10, 8 );

// -----------------------------------------
// ACHIEVEMENTS
// -----------------------------------------

/**
 * Check if notifications are disabled for this achievement
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_achievements_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is an achievement
    if( ! in_array( $post_type, gamipress_get_achievement_types_slugs() ) ) {
        return $disabled;
    }

    $achievement_type_id = gamipress_get_achievement_type_id( $post_type );

    // Bail if disabled from achievement type
    if( (bool) gamipress_get_post_meta( $achievement_type_id, $prefix . 'disable_achievements', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_achievements' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_achievements_disabled', 10, 3 );

/**
 * The notifications content for achievements
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_achievements_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is an achievement
    if( ! in_array( $post_type, gamipress_get_achievement_types_slugs() ) ) {
        return $content;
    }

    $achievement_type_id = gamipress_get_achievement_type_id( $post_type );

    // Get content from achievement type
    $content = gamipress_get_post_meta( $achievement_type_id, $prefix . 'achievement_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'achievement_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have unlocked the {achievement_type} {link}', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_achievement_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_achievements_content', 10, 4 );

// -----------------------------------------
// STEPS
// -----------------------------------------

/**
 * Check if notifications are disabled for this step
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_steps_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a step
    if( $post_type !== 'step' ) {
        return $disabled;
    }

    $achievement = gamipress_get_step_achievement( $post_id );

    // Bail if step achievement not found
    if( ! $achievement ) {
        return $disabled;
    }

    $achievement_type_id = gamipress_get_achievement_type_id( $achievement->post_type );

    // Bail if disabled from achievement type
    if( (bool) gamipress_get_post_meta( $achievement_type_id, $prefix . 'disable_steps', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_steps' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_steps_disabled', 10, 3 );

/**
 * The notifications content for steps
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_steps_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a step
    if( $post_type !== 'step' ) {
        return $content;
    }

    $achievement = gamipress_get_step_achievement( $post_id );

    // Bail if step achievement not found
    if( ! $achievement ) {
        return $content;
    }

    $achievement_type_id = gamipress_get_achievement_type_id( $achievement->post_type );

    // Get content from achievement type
    $content = gamipress_get_post_meta( $achievement_type_id, $prefix . 'step_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'step_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have completed the step "{label}" of the {achievement_type} {achievement_link}', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_step_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_steps_content', 10, 4 );

// -----------------------------------------
// POINTS AWARDS
// -----------------------------------------

/**
 * Check if notifications are disabled for this points award
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_points_awards_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a points award
    if( $post_type !== 'points-award' ) {
        return $disabled;
    }

    $points_type = gamipress_get_points_award_points_type( $post_id );

    // Bail if points award type not found
    if( ! $points_type ) {
        return $disabled;
    }

    // Bail if disabled from points type
    if( (bool) gamipress_get_post_meta( $points_type->ID, $prefix . 'disable_points_awards', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_points_awards' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_points_awards_disabled', 10, 3 );

/**
 * The notifications content for points awards
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_points_awards_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a points award
    if( $post_type !== 'points-award' ) {
        return $content;
    }

    $points_type = gamipress_get_points_award_points_type( $post_id );

    // Bail if points award type not found
    if( ! $points_type ) {
        return $content;
    }

    // Get content from points type
    $content = gamipress_get_post_meta( $points_type->ID, $prefix . 'points_award_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'points_award_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have earned {points} {points_label} for completing "{label}"', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_points_award_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_points_awards_content', 10, 4 );

// -----------------------------------------
// POINTS DEDUCTS
// -----------------------------------------

/**
 * Check if notifications are disabled for this points deduct
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_points_deducts_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a points deduct
    if( $post_type !== 'points-deduct' ) {
        return $disabled;
    }

    $points_type = gamipress_get_points_deduct_points_type( $post_id );

    // Bail if points deduct type not found
    if( ! $points_type ) {
        return $disabled;
    }

    // Bail if disabled from points type
    if( (bool) gamipress_get_post_meta( $points_type->ID, $prefix . 'disable_points_deducts', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_points_deducts' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_points_deducts_disabled', 10, 3 );

/**
 * The notifications content for points deducts
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_points_deducts_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a points deduct
    if( $post_type !== 'points-deduct' ) {
        return $content;
    }

    $points_type = gamipress_get_points_deduct_points_type( $post_id );

    // Bail if points deduct type not found
    if( ! $points_type ) {
        return $content;
    }

    // Get content from points type
    $content = gamipress_get_post_meta( $points_type->ID, $prefix . 'points_deduct_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'points_deduct_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have lost {points} {points_label} for "{label}"', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_points_deduct_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_points_deducts_content', 10, 4 );

// -----------------------------------------
// RANKS
// -----------------------------------------

/**
 * Check if notifications are disabled for this rank
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_ranks_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a rank
    if( ! in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        return $disabled;
    }

    $rank_type_id = gamipress_get_rank_type_id( $post_type );

    // Bail if disabled from rank type
    if( (bool) gamipress_get_post_meta( $rank_type_id, $prefix . 'disable_ranks', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_ranks' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_ranks_disabled', 10, 3 );

/**
 * The notifications content for ranks
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_ranks_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a rank
    if( ! in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        return $content;
    }

    $rank_type_id = gamipress_get_rank_type_id( $post_type );

    // Get content from rank type
    $content = gamipress_get_post_meta( $rank_type_id, $prefix . 'rank_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'rank_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have reached the {rank_type} {link}', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_rank_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_ranks_content', 10, 4 );

// -----------------------------------------
// RANK REQUIREMENTS
// -----------------------------------------

/**
 * Check if notifications are disabled for this rank requirement
 *
 * @since 1.0.0
 *
 * @param bool  $disabled
 * @param int   $user_id
 * @param int   $post_id
 *
 * @return bool
 */
function gamipress_buddyboss_notifications_rank_requirements_disabled( $disabled, $user_id, $post_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a rank requirement
    if( $post_type !== 'rank-requirement' ) {
        return $disabled;
    }

    $rank = gamipress_get_rank_requirement_rank( $post_id );

    // Bail if rank requirement rank not found
    if( ! $rank ) {
        return $disabled;
    }

    $rank_type_id = gamipress_get_rank_type_id( $rank->post_type );

    // Bail if disabled from rank type
    if( (bool) gamipress_get_post_meta( $rank_type_id, $prefix . 'disable_rank_requirements', true ) ) {
        return true;
    }

    // Bail if disabled from settings
    if( (bool) gamipress_buddyboss_notifications_get_option( 'disable_rank_requirements' ) ) {
        return true;
    }

    return $disabled;

}
add_filter( 'gamipress_buddyboss_notifications_disabled', 'gamipress_buddyboss_notifications_rank_requirements_disabled', 10, 3 );

/**
 * The notifications content for rank requirements
 *
 * @since 1.0.0
 *
 * @param string  $content
 * @param int   $user_id
 * @param int   $post_id
 * @param int   $user_earning_id
 *
 * @return string
 */
function gamipress_buddyboss_notifications_rank_requirements_content( $content, $user_id, $post_id, $user_earning_id ) {

    $prefix = '_gamipress_buddyboss_notifications_';

    $post_type = gamipress_get_post_type( $post_id );

    // Bail if not is a rank requirement
    if( $post_type !== 'rank-requirement' ) {
        return $content;
    }

    $rank = gamipress_get_rank_requirement_rank( $post_id );

    // Bail if rank requirement rank not found
    if( ! $rank ) {
        return $content;
    }

    $rank_type_id = gamipress_get_rank_type_id( $rank->post_type );

    // Get content from rank type
    $content = gamipress_get_post_meta( $rank_type_id, $prefix . 'rank_requirement_content', true );

    // Get content from settings
    if( empty( $content ) ) {
        $content = gamipress_buddyboss_notifications_get_option( 'rank_requirement_content', '' );
    }

    // Default content
    if( empty( $content ) ) {
        $content = __( 'You have completed the requirement "{label}" of the {rank_type} {rank_link}', 'gamipress-buddyboss-notifications' );
    }

    $content = gamipress_buddyboss_notifications_parse_rank_requirement_pattern( $content, $user_id, $post_id, $user_earning_id );

    return $content;

}
add_filter( 'gamipress_buddyboss_notifications_content', 'gamipress_buddyboss_notifications_rank_requirements_content', 10, 4 );

/**
 * Gamipress notification used feature image so change avatar url to feature image.
 *
 * @param \WP_REST_Response              $response     The response data.
 *
 * @since 1.7.3
 *
 * @return \WP_REST_Response
 */
function gamipress_buddyboss_notifications_modify_notification_avatar_url( $response ) {

	$data = $response->get_data();
	if (
		! empty( $data ) &&
		! empty( $data['component'] ) &&
		'gamipress_buddyboss_notifications' === $data['component']
	) {
		$post_id = $data['item_id'];
		$post    = gamipress_get_post( $data['item_id'] );
		if ( in_array( $post->post_type, gamipress_get_achievement_types_slugs(), true ) ) {
			$feature_img_urls = gamipress_buddyboss_notifications_get_achievement_post_feature_img_url( $post );
			$image_full  	  = ! empty( $feature_img_urls['image_full'] ) ? $feature_img_urls['image_full'] : false;
			$image_thumb 	  = ! empty( $feature_img_urls['image_thumb'] ) ? $feature_img_urls['image_thumb'] : false;

		} elseif ( 'step' === $post->post_type ) {
			// Step.
			$achievement = gamipress_get_step_achievement( $post->ID );

			if ( $achievement ) {
				$feature_img_urls = gamipress_buddyboss_notifications_get_achievement_post_feature_img_url( $achievement );
				$image_full  	  = ! empty( $feature_img_urls['image_full'] ) ? $feature_img_urls['image_full'] : false;
				$image_thumb 	  = ! empty( $feature_img_urls['image_thumb'] ) ? $feature_img_urls['image_thumb'] : false;
			}
		} elseif ( in_array( $post->post_type, array( 'points-award', 'points-deduct' ), true ) ) {

			// Points award and deduct.
			$points_type_id = gamipress_get_post_field( 'post_parent', $post->ID );
			$post_id        = 0;
			if ( gettype( $points_type_id ) === 'integer' ) {
				$post_id = $points_type_id;
			} elseif ( absint( $points_type_id ) !== 0 ) {
				$post_id = $points_type_id;
			} else {
				$points_types = gamipress_get_points_types();

				if ( isset( $points_types[ $points_type_id ] ) ) {
					$post_id = $points_types[ $points_type_id ]['ID'];
				}
			}
			// Return our image tag with custom size.
			$image_full  = get_the_post_thumbnail_url( $post_id, 'full' );
			$image_thumb = get_the_post_thumbnail_url( $post_id, 'gamipress-points' );

		} elseif ( in_array( $post->post_type, gamipress_get_rank_types_slugs(), true ) ) {
			// Rank. 
			$feature_img_urls = gamipress_buddyboss_notifications_get_rank_post_feature_img_url( $post );
			$image_full  	  = ! empty( $feature_img_urls['image_full'] ) ? $feature_img_urls['image_full'] : false;
			$image_thumb 	  = ! empty( $feature_img_urls['image_thumb'] ) ? $feature_img_urls['image_thumb'] : false;

		} elseif ( 'rank-requirement' === $post->post_type ) {
			// Rank requirement.
			$rank = gamipress_get_rank_requirement_rank( $post->ID );

			if ( $rank ) {
				$feature_img_urls = gamipress_buddyboss_notifications_get_rank_post_feature_img_url( $rank );
				$image_full  	  = ! empty( $feature_img_urls['image_full'] ) ? $feature_img_urls['image_full'] : false;
				$image_thumb 	  = ! empty( $feature_img_urls['image_thumb'] ) ? $feature_img_urls['image_thumb'] : false;
			}
		}

		// Avatars.
		$data['avatar_urls'] = array(
			'full'  => ! empty( $image_full ) ? $image_full : '',
			'thumb' => ! empty( $image_thumb ) ? $image_thumb : '',
		);
		// Read only.
		$data['readonly'] = true;

		$response->set_data( $data );
	}

	return $response;
}


/**
 * Add the achievement post thumbnail to the achievement post type.
 *
 * @since 1.0.0
 * 	 
 * @param \WP_Post $post The post object.
 * @return array
 */
function gamipress_buddyboss_notifications_get_achievement_post_feature_img_url( $post ){
	$image_full  = get_the_post_thumbnail_url( $post->ID, 'full' );
	$image_thumb = get_the_post_thumbnail_url( $post->ID, 'gamipress-achievement' );

	if ( empty( $image_full ) || empty( $image_thumb ) ) {
		// Grab our achievement type's post thumbnail.
		$achievement = get_page_by_path( gamipress_get_post_type( $post->ID ), OBJECT, 'achievement-type' );
		$image_full  = is_object( $achievement ) ? get_the_post_thumbnail_url( $achievement->ID, 'full' ) : false;
		$image_thumb = is_object( $achievement ) ? get_the_post_thumbnail_url( $achievement->ID, 'gamipress-achievement' ) : false;
	}
	return array(
		'image_full'  => $image_full,
		'image_thumb' => $image_thumb,
	);
}

/**
 * Add the rank post thumbnail to the rank post type.
 *
 * @since 1.0.0
 * 	 
 * @param \WP_Post $post The post object.
 * @return array
 */
function gamipress_buddyboss_notifications_get_rank_post_feature_img_url( $post ){
	$image_full  = get_the_post_thumbnail_url( $post->ID, 'full');
	$image_thumb = get_the_post_thumbnail_url( $post->ID, 'gamipress-rank' );

	if ( empty( $image_full ) || empty( $image_thumb ) ) {
		// Grab our rank type's post thumbnail.
		$rank        = get_page_by_path( gamipress_get_post_type( $post_id ), OBJECT, 'rank-type' );
		$image_full  = is_object( $rank ) ? get_the_post_thumbnail_url( $rank->ID, 'full' ) : false;
		$image_thumb = is_object( $rank ) ? get_the_post_thumbnail_url( $rank->ID, 'gamipress-rank' ) : false;
	}
	return array(
		'image_full'  => $image_full,
		'image_thumb' => $image_thumb,
	);
}

add_filter( 'bp_rest_notifications_prepare_value', 'gamipress_buddyboss_notifications_modify_notification_avatar_url', 20, 1 );