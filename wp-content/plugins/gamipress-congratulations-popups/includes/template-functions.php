<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Congratulations_Popups\Template_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin templates directory on GamiPress template engine
 *
 * @since 1.0.0
 *
 * @param array $file_paths
 *
 * @return array
 */
function gamipress_congratulations_popups_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/congratulations-popups/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/congratulations-popups/';
    $file_paths[] = GAMIPRESS_CONGRATULATIONS_POPUPS_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_congratulations_popups_template_paths' );

/**
 * Pattern tags
 *
 * @since  1.0.0

 * @return array The registered pattern tags
 */
function gamipress_congratulations_popups_get_pattern_tags() {

    $pattern_tags = array();

    // User
    $pattern_tags[] = '<strong>' . __( 'User Tags', 'gamipress-congratulations-popups' ) . '</strong>';

    $pattern_tags = array_merge( $pattern_tags, array(
        '{user}'                => __( 'User display name.', 'gamipress-congratulations-popups' ),
        '{user_first}'          => __( 'User first name.', 'gamipress-congratulations-popups' ),
        '{user_last}'           => __( 'User last name.', 'gamipress-congratulations-popups' ),
        '{user_id}'             => __( 'User ID (useful for shortcodes that user ID can be passed as attribute).', 'gamipress-congratulations-popups' ),
    ) );

    // Points
    $pattern_tags[] = '<strong>' . __( 'Points Types Tags', 'gamipress-congratulations-popups' ) . '</strong>';

    foreach( gamipress_get_points_types() as $points_type => $data ) {
        $pattern_tags[] = '<span>' . $data['plural_name'] . '</span>';
        $pattern_tags['{' . $points_type . '_image}'] = sprintf( __( '%s image.', 'gamipress-congratulations-popups' ), strtolower( $data['plural_name'] ) );
        $pattern_tags['{' . $points_type . '_balance}'] = sprintf( __( 'User %s balance.', 'gamipress-congratulations-popups' ), strtolower( $data['plural_name'] ) );
        $pattern_tags['{' . $points_type . '_label}'] = sprintf( __( '%s label. Singular or plural is based on user balance.', 'gamipress-congratulations-popups' ), $data['singular_name'] );

    }

    // Achievements
    $pattern_tags[] = '<strong>' . __( 'Achievement Types Tags', 'gamipress-congratulations-popups' ) . '</strong>';

    foreach( gamipress_get_achievement_types() as $achievement_type => $data ) {

        $pattern_tags[] = '<span>' . $data['plural_name'] . '</span>';
        $pattern_tags['{' . $achievement_type . '_id}'] = sprintf( __( 'ID of last %s ID user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_title}'] = sprintf( __( 'Title of last %s user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_url}'] = sprintf( __( 'URL to the last %s user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_link}'] = sprintf( __( 'Link to the last %s user has earned with the title as text.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_excerpt}'] = sprintf( __( 'Excerpt of last %s user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_image}'] = sprintf( __( 'Featured image of last %s user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $achievement_type . '_congratulations}'] = sprintf( __( 'Congratulations text of last %s user has earned.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
    }

    // Ranks
    $pattern_tags[] = '<strong>' . __( 'Rank Types Tags', 'gamipress-congratulations-popups' ) . '</strong>';

    foreach( gamipress_get_rank_types() as $rank_type => $data ) {

        $pattern_tags[] = '<span>' . $data['plural_name'] . '</span>';
        $pattern_tags['{' . $rank_type . '_id}'] = sprintf( __( 'ID of user %s.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $rank_type . '_title}'] = sprintf( __( 'Title of user %s.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $rank_type . '_url}'] = sprintf( __( 'URL to the user %s.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $rank_type . '_link}'] = sprintf( __( 'Link to the user %s with the title as text.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $rank_type . '_excerpt}'] = sprintf( __( 'Excerpt of user %s.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );
        $pattern_tags['{' . $rank_type . '_image}'] = sprintf( __( 'Featured image of user %s.', 'gamipress-congratulations-popups' ), strtolower( $data['singular_name'] ) );

    }

    return apply_filters( 'gamipress_congratulations_popups_pattern_tags', $pattern_tags );

}

/**
 * Parse pattern tags to a given pattern
 *
 * @since  1.0.0
 *
 * @param string    $pattern
 * @param int       $user_id
 *
 * @return string Parsed pattern
 */
function gamipress_congratulations_popups_parse_pattern_tags( $pattern, $user_id ) {

    if( absint( $user_id ) === 0 ) {
        $user_id = get_current_user_id();
    }

    $user = get_userdata( $user_id );

    $pattern_replacements = array();

    // User
    $pattern_replacements = array_merge( $pattern_replacements, array(
        '{user}'                =>  ( $user ? $user->display_name : '' ),
        '{user_first}'          =>  ( $user ? $user->first_name : '' ),
        '{user_last}'           =>  ( $user ? $user->last_name : '' ),
        '{user_id}'             =>  ( $user ? $user->ID : '' ),
    ) );

    // Points
    foreach( gamipress_get_points_types() as $points_type => $data ) {

        $user_points = ( $user ? gamipress_get_user_points( $user->ID, $points_type ) : 0 );

        $pattern_replacements['{' . $points_type . '_image}'] = gamipress_get_points_type_thumbnail( $data['ID'] );
        $pattern_replacements['{' . $points_type . '_balance}'] = gamipress_format_amount( $user_points, $points_type );
        $pattern_replacements['{' . $points_type . '_label}'] = ( $user_points === 1 ? $data['singular_name'] : $data['plural_name'] );

    }

    // Achievements

    foreach( gamipress_get_achievement_types() as $achievement_type => $data ) {

        $achievement = ( $user ? gamipress_get_user_achievements( array(
            'user_id'          => $user->ID,
            'achievement_type' => $achievement_type,
            'limit'            => 1,
        ) ) : false );

        if( isset( $achievement[0] ) ) {
            $achievement = $achievement[0];
        }

        $achievement = ( $achievement ? gamipress_get_post( $achievement->ID ) : false );

        $pattern_replacements['{' . $achievement_type . '_id}']                 = ( $achievement ? $achievement->ID : '' );
        $pattern_replacements['{' . $achievement_type . '_title}']              = ( $achievement ? $achievement->post_title : '' );
        $pattern_replacements['{' . $achievement_type . '_url}']                = ( $achievement ? get_the_permalink( $achievement->ID ) : '' );
        $pattern_replacements['{' . $achievement_type . '_link}']               = ( $achievement ? sprintf( '<a href="%s" title="%s">%s</a>', get_the_permalink( $achievement->ID ), $achievement->post_title, $achievement->post_title ) : '' );
        $pattern_replacements['{' . $achievement_type . '_excerpt}']            = ( $achievement ? $achievement->post_excerpt : '' );
        $pattern_replacements['{' . $achievement_type . '_image}']              = ( $achievement ? gamipress_get_achievement_post_thumbnail( $achievement->ID ) : '' );
        $pattern_replacements['{' . $achievement_type . '_congratulations}']    = ( $achievement ? gamipress_get_post_meta( $achievement->ID, '_gamipress_congratulations_text' ) : '' );
    }

    // Ranks

    foreach( gamipress_get_rank_types() as $rank_type => $data ) {

        $rank = ( $user ? gamipress_get_user_rank( $user->ID, $rank_type ) : false );

        $pattern_replacements['{' . $rank_type . '_id}']        = ( $rank ? $rank->ID : '' );
        $pattern_replacements['{' . $rank_type . '_title}']     = ( $rank ? $rank->post_title : '' );
        $pattern_replacements['{' . $rank_type . '_url}']       = ( $rank ? get_the_permalink( $rank->ID ) : '' );
        $pattern_replacements['{' . $rank_type . '_link}']      = ( $rank ? sprintf( '<a href="%s" title="%s">%s</a>', get_the_permalink( $rank->ID ), $rank->post_title, $rank->post_title ) : '' );
        $pattern_replacements['{' . $rank_type . '_excerpt}']   = ( $rank ? $rank->post_excerpt : '' );
        $pattern_replacements['{' . $rank_type . '_image}']     = ( $rank ? gamipress_get_rank_post_thumbnail( $rank->ID ) : '' );
    }

    $pattern_replacements = apply_filters( 'gamipress_congratulations_popups_parse_pattern_replacements', $pattern_replacements, $pattern );

    return apply_filters( 'gamipress_congratulations_popups_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern );

}

/**
 * Get a string with the desired pattern tags html markup
 *
 * @since  1.0.0
 *
 * @return string Pattern tags html markup
 */
function gamipress_congratulations_popups_get_pattern_tags_html() {

    $js = 'jQuery(this).parent().parent().find(\'.gamipress-pattern-tags-list\').slideToggle();'
        .'jQuery(this).text( ( jQuery(this).text() === \'Hide\' ? \'Show\' : \'Hide\') );';

    $output = '<a href="javascript:void(0);" onclick="' . $js . '">Show</a>';
    $output .= '<ul class="gamipress-pattern-tags-list gamipress-congratulations-popups-pattern-tags-list" style="display: none;">';

    foreach( gamipress_congratulations_popups_get_pattern_tags() as $tag => $description ) {

        if( is_numeric( $tag ) ) {
            $output .= "<li id='{$tag}'>{$description}</li>";
        } else {
            $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

            $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
        }
    }

    $output .= '</ul>';

    return $output;

}