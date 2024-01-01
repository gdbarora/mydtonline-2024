<?php
/**
 * Blocks
 *
 * @package     GamiPress\Leaderboards\Blocks
 * @since       1.1.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin block icons
 *
 * @since 1.1.2
 *
 * @param array $icons
 *
 * @return array
 */
function gamipress_leaderboards_block_icons( $icons ) {

    $icons['leaderboard'] =
        '<svg width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" >
            <path d="M 6.9921875 2.015625 L 6.9921875 7.015625 L 0.9921875 7.015625 L 0.9921875 18.015625 L 6.9921875 18.015625 L 7.0917969 18.015625 L 12.992188 18.015625 L 18.992188 18.015625 L 18.992188 10.015625 L 12.992188 10.015625 L 12.992188 2.015625 L 6.9921875 2.015625 z M 10.181641 3 L 10.873047 3 L 10.873047 7 L 10.03125 7 L 10.03125 4.3105469 L 10.052734 3.890625 C 9.9127347 4.030625 9.8119531 4.1199219 9.7519531 4.1699219 L 9.2929688 4.5410156 L 8.8925781 4.03125 L 10.181641 3 z M 4.109375 7.8457031 A 1.5123 1.5123 0 0 1 4.7597656 7.984375 A 1.00745 1.00745 0 0 1 5.1992188 8.3554688 A 0.968 0.968 0 0 1 5.359375 8.9140625 A 1.25433 1.25433 0 0 1 5.2597656 9.4257812 A 1.75082 1.75082 0 0 1 4.9589844 9.9140625 A 7.276 7.276 0 0 1 4.2597656 10.625 L 3.75 11.105469 L 3.75 11.144531 L 5.4707031 11.144531 L 5.4707031 11.845703 L 2.7089844 11.845703 L 2.7089844 11.265625 L 3.6992188 10.265625 C 3.9892188 9.9656255 4.1892969 9.7545313 4.2792969 9.6445312 A 1.44773 1.44773 0 0 0 4.4707031 9.3144531 A 0.80281 0.80281 0 0 0 4.5292969 9.015625 A 0.45478 0.45478 0 0 0 4.4003906 8.6542969 A 0.5074 0.5074 0 0 0 4.0507812 8.5449219 A 1.04844 1.04844 0 0 0 3.5996094 8.6445312 A 2.50521 2.50521 0 0 0 3.1503906 8.9453125 L 2.6992188 8.4042969 A 3.67134 3.67134 0 0 1 3.1796875 8.0546875 A 1.90064 1.90064 0 0 1 3.5996094 7.9042969 A 1.9806 1.9806 0 0 1 4.109375 7.8457031 z M 15.839844 10.806641 A 1.72094 1.72094 0 0 1 16.839844 11.068359 A 0.81112 0.81112 0 0 1 17.208984 11.777344 A 0.9734 0.9734 0 0 1 16.980469 12.427734 A 1.20318 1.20318 0 0 1 16.330078 12.796875 L 16.330078 12.818359 A 1.29033 1.29033 0 0 1 17.070312 13.117188 A 0.8359 0.8359 0 0 1 17.330078 13.757812 A 1.077 1.077 0 0 1 16.900391 14.677734 A 1.948 1.948 0 0 1 15.679688 15.007812 A 2.923 2.923 0 0 1 14.5 14.787109 L 14.5 14.046875 A 2.6715 2.6715 0 0 0 15.019531 14.248047 A 2.23991 2.23991 0 0 0 15.589844 14.328125 A 1.084 1.084 0 0 0 16.220703 14.177734 A 0.54963 0.54963 0 0 0 16.419922 13.707031 A 0.43316 0.43316 0 0 0 16.189453 13.296875 A 1.76407 1.76407 0 0 0 15.439453 13.177734 L 15.130859 13.177734 L 15.130859 12.517578 L 15.439453 12.517578 A 1.54282 1.54282 0 0 0 16.140625 12.398438 A 0.44731 0.44731 0 0 0 16.359375 11.966797 C 16.359375 11.656797 16.159237 11.507812 15.779297 11.507812 A 1.17937 1.17937 0 0 0 15.369141 11.578125 A 1.78919 1.78919 0 0 0 14.910156 11.806641 L 14.509766 11.207031 A 2.18468 2.18468 0 0 1 15.839844 10.806641 z " />    
        </svg>';

    return $icons;
}
add_filter( 'gamipress_block_icons', 'gamipress_leaderboards_block_icons' );

/**
 * Turn select2 fields into 'post' or 'user' field types
 *
 * @since 1.1.2
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_leaderboards_block_fields( $fields, $shortcode ) {

    switch ( $shortcode->slug ) {
        case 'gamipress_leaderboard':
        case 'gamipress_leaderboard_user_position':
            // Leaderboard ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = 'gp_leaderboard';
            break;
    }

    return $fields;

}
add_filter( 'gamipress_get_block_fields', 'gamipress_leaderboards_block_fields', 11, 2 );
