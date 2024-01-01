<?php
class User_Rank_Widget extends WP_Widget
{


    public function __construct()
    {
        parent::__construct(
            'current_user_rank_widget',
            __('Gamipress(Custom) - User Rank', 'text_domain'),
            array(
                'description' => __('Display highest rank of the displayed user.', 'text_domain'),
            )
        );
    }

    public function widget($args, $instance)
    {
        $current_userID = bp_displayed_user_id();
        $star_type = xprofile_get_field_data(1896, $current_userID, $multi_format = 'array');
        $rank_type = xprofile_get_field_data(8, $current_userID, $multi_format = 'array');
        $user_award_enagic_rank = gamipress_get_user_achievements(
            array(
                'user_id' => bp_displayed_user_id(),
                'achievement_type' => "enagic-rank",
                'groupby' => 'achievement_id',
                'limit' => 1,
            )
        );
        $user_award_soc_rank = gamipress_get_user_achievements(
            array(
                'user_id' => bp_displayed_user_id(),
                'achievement_type' => "card-star",
                'groupby' => 'achievement_id',
                'limit' => 1,
            )
        );
    
        // Check if there is any content to display
        $has_content = false;
    
        if ($rank_type == "1A") {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=2092 title=no user_id=' . $current_userID . ']');
        } elseif ($rank_type == "2A") {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=2094 title=no user_id=' . $current_userID . ']');
        } elseif ($rank_type == "3A") {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=2096 title=no user_id=' . $current_userID . ']');
        } elseif ($rank_type == "4A") {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=2098 title=no user_id=' . $current_userID . ']');
        } elseif ($rank_type == "5A") {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=2100 title=no user_id=' . $current_userID . ']');
        } elseif (($rank_type == "6A") || ($rank_type == "6A2") || ($rank_type == "6A2-2") || ($rank_type == "6A2-3") || ($rank_type == "6A2-4") || ($rank_type == "6A2-5") || ($rank_type == "6A2-6") || ($rank_type == "6A2-7") || ($rank_type == "6A2-8")) {
            foreach ($user_award_enagic_rank as $user_enagic_rank) {
                $has_content = true;
                echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
                $achievement_thumbnail = gamipress_get_achievement_post_thumbnail($user_enagic_rank->ID, $thumbnail_size);
                echo '<div class="gamipress-achievement user-has-not-earned gamipress-layout-left gamipress-align-none">';
                echo '<div class="gamipress-achievement-image">' . $achievement_thumbnail . '</div>';
                // Uncomment the next line if you want to display the achievement title
                // echo '<div class="gamipress-achievement-description"><h2>' . $user_enagic_rank->title . '</h2></div>';
                echo '</div>';
            }
        }
    
        if ($star_type == 'Star 1') {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=1502 title=no user_id=' . $current_userID . ']');
        } elseif ($star_type == 'Star 2') {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=1505 title=no user_id=' . $current_userID . ']');
        } elseif ($star_type == 'Star 3') {
            $has_content = true;
            echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
            echo do_shortcode('[gamipress_achievement id=1507 title=no user_id=' . $current_userID . ']');
        } elseif (($star_type == 'Star 4') || ($star_type == 'Star 5') || ($star_type == 'Star 6') || ($star_type == 'Star 7') || ($star_type == 'Star 8')) {
            foreach ($user_award_soc_rank as $user_soc_rank) {
                $has_content = true;
                echo '<aside class="widget widget_bp_rank_widget buddypress widget">';
                $achievement_thumbnail = gamipress_get_achievement_post_thumbnail($user_soc_rank->ID, $thumbnail_size);
                echo '<div class="gamipress-achievement user-has-not-earned gamipress-layout-left gamipress-align-none">';
                echo '<div class="gamipress-achievement-image">' . $achievement_thumbnail . '</div>';
                // Uncomment the next line if you want to display the achievement title
                // echo '<div class="gamipress-achievement-description"><h2>' . $user_soc_rank->title . '</h2></div>';
                echo '</div>';
            }
        }
    
        // Close the widget container if there is any content
        if ($has_content) {
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
function register_user_rank_widget()
{
    register_widget('User_Rank_Widget');
}
add_action('widgets_init', 'register_user_rank_widget');
