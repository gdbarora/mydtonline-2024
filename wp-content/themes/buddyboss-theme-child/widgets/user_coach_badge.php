<?php
class Coach_Badge_Widget extends WP_Widget
{


    public function __construct()
    {
        parent::__construct(
            'current_coach_widget',
            __('Coach - The Dream team', 'text_domain'),
            array(
                'description' => __('Display coach badge the current coach.', 'text_domain'),
            )
        );
    }

    public function widget($args, $instance)
    {


        $user_award_coach = gamipress_get_user_achievements(
            array(
                'user_id' => bp_displayed_user_id(),
                'achievement_type' => 'coach',
                'groupby' => 'achievement_id',
                'limit' => 1,
            )
        );

        // Check if the user has the coach achievement
        if (!empty($user_award_coach)) {
            // Add custom CSS classes to the widget container
            $widget_classes = 'widget widget_bp_coach_widget buddypress widget';
            echo '<aside class="' . esc_attr($widget_classes) . '">';
            echo '<div class="coach_badge">';
            echo gamipress_get_achievement_post_thumbnail($user_award_coach[0]->ID, 'full');
            echo '</div>';

            echo '</aside>'; // Close the widget container
            echo $args['after_widget'];
        }

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
function register_coach_badge_widget()
{
    register_widget('Coach_Badge_Widget');
}
add_action('widgets_init', 'register_coach_badge_widget');
