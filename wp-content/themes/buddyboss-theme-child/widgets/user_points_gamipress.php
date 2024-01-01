<?php
class User_Points_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'user_gamipress_points',
            __('Gamipress - Points (Stature)', 'your_text_domain'), // Replace with your actual text domain
            array(
                'description' => __('Display user points for different periods.', 'your_text_domain'), // Replace with your actual text domain
            )
        );
    }

    public function widget($args, $instance) {
        $displayed_user_id = bp_displayed_user_id();

        echo $args['before_widget'];

        $widget_classes = 'widget widget_bp_coach_widget buddypress widget';
        echo '<aside class="' . esc_attr($widget_classes) . '">';
        echo '<div class="points-this-month-gp"><strong>Points this month: </strong>' . do_shortcode("[gamipress_user_points current_user='no' type='point' inline='yes' columns='3' thumbnail='no' label='no' layout='left' align='none' period='this-month' user_id='{$displayed_user_id}']") . '</div>';
        echo '<div class="points-this-year-gp"><strong>Points this year: </strong>' . do_shortcode("[gamipress_user_points current_user='no' type='point' inline='yes' columns='3' thumbnail='no' label='no' layout='left' align='none' period='this-year' user_id='{$displayed_user_id}']") . '</div>';
        echo '</aside>'; // Close the widget container
        echo $args['after_widget'];
    }

    public function form($instance) {
        // This space intentionally left blank to prevent users from entering a custom title
    }

    public function update($new_instance, $old_instance) {
        // Save widget settings (no changes needed)
        $instance = array();
        return $instance;
    }
}

// Register the widget
function register_user_points_widget() {
    register_widget('User_Points_Widget');
}
add_action('widgets_init', 'register_user_points_widget');
