<?php
/**
 * BuddyBoss Custom Notification Class.
 */

defined('ABSPATH') || exit;

if (!class_exists('BP_Core_Notification_Abstract')) {
    return;
}

/**
 * Set up the Custom notification class.
 */
class BP_Custom_Notification extends BP_Core_Notification_Abstract
{

    /**
     * Instance of this class.
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Get the instance of this class.
     *
     * @return null|BP_Custom_Notification|Controller|object
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor method.
     */
    public function __construct()
    {
        $this->start();
    }

    /**
     * Initialize all methods inside it.
     *
     * @return mixed|void
     */
    public function load()
    {

        /**
         * Register Notification Group.
         *
         * @param string $group_key         Group key.
         * @param string $group_label       Group label.
         * @param string $group_admin_label Group admin label.
         * @param int    $priority          Priority of the group.
         */
        $this->register_notification_group(
            'gamekeeper_notifications',
            esc_html__('Gamekeeper Notifications', 'buddyboss'), // For the frontend.
            esc_html__('Gamekeeper Notifications', 'buddyboss') // For the backend.
        );

        $this->register_custom_notification();
    }

    /**
     * Register notification for user mention.
     */
    public function register_custom_notification()
    {
        /**
         * Register Notification Type.
         *
         * @param string $notification_type        Notification Type key.
         * @param string $notification_label       Notification label.
         * @param string $notification_admin_label Notification admin label.
         * @param string $notification_group       Notification group.
         * @param bool   $default                  Default status for enabled/disabled.
         */
        $this->register_notification_type(
            'member_enagic_rank_update',
            esc_html__('Gamipress Enagic Rank Update', 'buddyboss'),
            esc_html__('Gamipress Enagic Rank Update', 'buddyboss'),
            'gamekeeper_notifications',
            true,
            false,
            'Notifies the gamekeeper if a user updates his enagic rank above 5A'
        );
        $this->register_notification_type(
            'member_soc_rank_update',
            esc_html__('Gamipress SOC Rank Update', 'buddyboss'),
            esc_html__('Gamipress SOC Rank Update', 'buddyboss'),
            'gamekeeper_notifications',
            true,
            false,
            'Notifies the gamekeeper if a user updates his soc rank above 4 Star'
        );


        /**
         * Add email schema.
         *
         * @param string $email_type        Type of email being sent.
         * @param array  $args              Email arguments.
         * @param string $notification_type Notification Type key.
         */
        // $this->register_email_type(
        //     'custom-at-message',
        //     array(
        //         'email_title'         => __( 'email title', 'buddyboss' ),
        //         'email_content'       => __( 'email content', 'buddyboss' ),
        //         'email_plain_content' => __( 'email plain text content', 'buddyboss' ),
        //         'situation_label'     => __( 'Email situation title', 'buddyboss' ),
        //         'unsubscribe_text'    => __( 'You will no longer receive emails when custom notification performed.', 'buddyboss' ),
        //     ),
        //     'member_enagic_rank_update'
        // );

        /**
         * Register notification.
         *
         * @param string $component         Component name.
         * @param string $component_action  Component action.
         * @param string $notification_type Notification Type key.
         * @param string $icon_class        Notification Small Icon.
         */
        $this->register_notification(
            'gamekeeper_notifications',
            'member_enagic_rank_update', // Use the correct action here
            'member_enagic_rank_update'  // Use the correct notification type here
        );

        $this->register_notification(
            'gamekeeper_notifications',
            'member_soc_rank_update',    // Use the correct action here
            'member_soc_rank_update'     // Use the correct notification type here
        );

        /**
         * Register Notification Filter.
         *
         * @param string $notification_label    Notification label.
         * @param array  $notification_types    Notification types.
         * @param int    $notification_position Notification position.
         */
        $this->register_notification_filter(
            __('Enagic Rank Notification Filter', 'buddyboss'),
            array('member_enagic_rank_update'),
            5
        );
        $this->register_notification_filter(
            __('SOC update Notification Filter', 'buddyboss'),
            array('member_soc_rank_update'),
            5
        );
    }

    /**
     * Format the notifications.
     *
     * @param string $content               Notification content.
     * @param int    $item_id               Notification item ID.
     * @param int    $secondary_item_id     Notification secondary item ID.
     * @param int    $action_item_count     Number of notifications with the same action.
     * @param string $component_action_name Canonical notification action.
     * @param string $component_name        Notification component ID.
     * @param int    $notification_id       Notification ID.
     * @param string $screen                Notification Screen type.
     *
     * @return array
     */
    public function format_notification($content, $achievement_id, $user_id, $action_item_count, $component_action_name, $component_name, $notification_id, $screen)
    {

        $user = get_userdata($user_id);
        $member_name = bp_core_get_user_displayname($user_id);
        $profile_url = bp_core_get_user_domain($user_id);

        $achievement_name = get_the_title($achievement_id);


        if ('gamekeeper_notifications' === $component_name && 'member_enagic_rank_update' === $component_action_name) {

            $avatar = bp_core_fetch_avatar(array('item_id' => $user_id, 'type'=> 'thumb', 'html' => false));

            $text = esc_html__($member_name . ' has requested to upgrade their '.get_post_type_object(gamipress_get_post_type($achievement_id))->labels->singular_name.' to ' . $achievement_name, 'buddyboss');

            /**
             * Change the text for Push Notifications  
             */
            if ($screen == "app_push" || $screen == "web_push") {
                $text = esc_html__('Custom Push Notification Text Only.', 'buddyboss');
            }

            return array(
                'text' => $text,
                'link' => $profile_url,
            );
        }

        return $content;
    }
}