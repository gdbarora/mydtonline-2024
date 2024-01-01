<?php

//This widget is created to show the  groups the current user is a member


class CurrentUserGroupsWidget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'current_user_groups_widget',
            __('User Groups(Custom)', 'text_domain'),
            array(
                'description' => __('Display groups of the current user.', 'text_domain'),
            )
        );
    }

    public function widget($args, $instance)
    {
        // Add custom CSS classes to the widget container
        $widget_classes = 'widget widget_bp_groups_widget buddypress widget';
        echo '<aside class="' . esc_attr($widget_classes) . '">';

        $displayed_user_id =  bp_displayed_user_id();
        $current_user_id = get_current_user_id();
        $prefix = 'My ';

        if($displayed_user_id !== $current_user_id){
            $prefix = explode( ' ', bp_get_displayed_user_fullname())[0]."'s ";
        }

        // Display a fixed title as an H2 heading without a link
        echo '<h2 class="widget-title">' .$prefix. esc_html__('Groups', 'text_domain') . '</h2>';

        $group_args = array(
            'user_id' => $displayed_user_id,
            'type' => 'alphabetical',
            'per_page' => -1,
        );

        $groups_html = '';

        if (bp_has_groups($group_args)) {
            $groups_html .= '<ul id="groups-list" class="item-list"  style="max-height:12lh; overflow-Y: scroll;">';
            while (bp_groups()) {
                bp_the_group();
                $groups_html .= '<li ' . bp_get_group_class() . '>';
                $groups_html .= '<div class="item-avatar"><a href="' . bp_get_group_permalink() . '">' . bp_get_group_avatar_thumb() . '</a></div>';
                $groups_html .= '<div class="item"><div class="item-title">' . bp_get_group_link() . '</div>';
                $groups_html .= '<div class="item-meta"><span class="activity">' . sprintf(__('active %s', 'buddyboss'), bp_get_group_last_active()) . '</span></div></div></li>';
            }
            $groups_html .= '</ul>';
        } else {
            $groups_html .= '<div class="widget-error">There are no groups to display</div>';
        }

        echo $groups_html;

        echo '</aside>'; // Close the widget container
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        // This space intentionally left blank to prevent users from entering a custom title
    }

    public function update($new_instance, $old_instance)
    {
        // Save widget settings (no changes needed)
        $instance = array();

        return $instance;
    }
}



// Register the widget
function register_current_user_groups_widget()
{
    register_widget('CurrentUserGroupsWidget');
}
add_action('widgets_init', 'register_current_user_groups_widget');

