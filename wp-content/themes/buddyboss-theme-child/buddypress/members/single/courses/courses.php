<?php

/**
 * @package WordPress
 * @subpackage BuddyPress for LearnDash
 */
global $wp;
$current_url = home_url(add_query_arg($_GET, $wp->request));

$filepath = locate_template(
    array(
        'learndash/learndash_template_script.min.js',
        'learndash/learndash_template_script.js',
        'learndash_template_script.min.js',
        'learndash_template_script.js',
    )
);

$view              = get_option('bb_theme_learndash_grid_list', 'grid');
$class_grid_active = ('grid' === $view) ? 'active' : '';
$class_list_active = ('list' === $view) ? 'active' : '';
$class_grid_show   = ('grid' === $view) ? 'grid-view bb-grid' : '';
$class_list_show   = ('list' === $view) ? 'list-view bb-list' : '';

if (!empty($filepath)) {
    wp_enqueue_script('learndash_template_script_js', str_replace(ABSPATH, '/', $filepath), array('jquery'), LEARNDASH_VERSION, true);
    $learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;
} elseif (file_exists(LEARNDASH_LMS_PLUGIN_DIR . '/templates/learndash_template_script' . ((defined('LEARNDASH_SCRIPT_DEBUG') && (LEARNDASH_SCRIPT_DEBUG === true)) ? '' : '.min') . '.js')) {
    wp_enqueue_script('learndash_template_script_js', LEARNDASH_LMS_PLUGIN_URL . 'templates/learndash_template_script' . ((defined('LEARNDASH_SCRIPT_DEBUG') && (LEARNDASH_SCRIPT_DEBUG === true)) ? '' : '.min') . '.js', array('jquery'), LEARNDASH_VERSION, true);
    $learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;
    $data            = array();
    $data['ajaxurl'] = admin_url('admin-ajax.php');
    $data            = array('json' => wp_json_encode($data));
    wp_localize_script('learndash_template_script_js', 'sfwd_data', $data);
}

add_action('wp_footer', array('LD_QuizPro', 'showModalWindow'), 20);
?>

<?php
$user_id  = bp_displayed_user_id();
$defaults = array(
    'user_id'            => get_current_user_id(),
    'per_page'           => false,
    'order'              => 'DESC',
    'orderby'            => 'ID',
    'course_points_user' => 'yes',
    'expand_all'         => false,
    'tax_query' => array(
        array(
            'taxonomy' => 'ld_course_language',  // Replace with your actual taxonomy name
            'field'    => 'slug',
            'terms'    => isset($_GET["filter-languages"])? $_GET["filter-languages"] :'english',
        ),
    )
);
$atts     = apply_filters('bp_learndash_user_courses_atts', $defaults);
$atts     = wp_parse_args($atts, $defaults);
if (false === $atts['per_page']) {
    $atts['per_page'] = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Per_Page', 'per_page');
    $atts['quiz_num'] = $atts['per_page'];
} else {
    $atts['per_page'] = intval($atts['per_page']);
}

if ($atts['per_page'] > 0) {
    $atts['paged'] = 1;
} else {
    unset($atts['paged']);
    $atts['nopaging'] = true;
}

$user_courses       = apply_filters('bp_learndash_user_courses', ld_get_mycourses($user_id, $atts));
$usermeta           = get_user_meta($user_id, '_sfwd-quizzes', true);
$quiz_attempts_meta = empty($usermeta) ? false : $usermeta;
$quiz_attempts      = array();
$profile_pager      = array();

if ((isset($atts['per_page'])) && (intval($atts['per_page']) > 0)) {
    $atts['per_page'] = intval($atts['per_page']);
    if ((isset($_GET['ld-profile-page'])) && (!empty($_GET['ld-profile-page']))) {
        $profile_pager['paged'] = intval($_GET['ld-profile-page']);
    } else {
        $profile_pager['paged'] = 1;
    }

    $profile_pager['total_items'] = count($user_courses);
    $profile_pager['total_pages'] = ceil(count($user_courses) / $atts['per_page']);
    $user_courses                 = array_slice($user_courses, ($profile_pager['paged'] * $atts['per_page']) - $atts['per_page'], $atts['per_page'], false);
}

if (!empty($quiz_attempts_meta)) {
    foreach ($quiz_attempts_meta as $quiz_attempt) {
        $c                          = learndash_certificate_details($quiz_attempt['quiz'], $user_id);
        $quiz_attempt['post']       = get_post($quiz_attempt['quiz']);
        $quiz_attempt['percentage'] = !empty($quiz_attempt['percentage']) ? $quiz_attempt['percentage'] : (!empty($quiz_attempt['count']) ? $quiz_attempt['score'] * 100 / $quiz_attempt['count'] : 0);

        if ($user_id === get_current_user_id() && !empty($c['certificateLink']) && ((isset($quiz_attempt['percentage']) && $quiz_attempt['percentage'] >= $c['certificate_threshold'] * 100))) {
            $quiz_attempt['certificate'] = $c;
        }
        $quiz_attempts[learndash_get_course_id($quiz_attempt['quiz'])][] = $quiz_attempt;
    }
}

?>

<div id="bb-learndash_profile" class="<?php echo empty($user_courses) ? 'user-has-no-lessons' : ''; ?>">
    <div id="learndash-content" class="learndash-course-list abc">

        <?php
        if (!empty($user_courses)) {
            $cat_topics = [];
        ?>
            <form id="bb-courses-directory-form" class="bb-courses-directory" method="get" action="<?php echo esc_url($current_url); ?>">
                <div class="flex align-items-center bb-courses-header">
                    <div id="courses-dir-search" class="bs-dir-search" role="search"></div>
                    <div class="sfwd-courses-filters flex push-right">
                        <div class="select-wrap">
                            <select id="sfwd_prs-order-by" name="orderby">
                                <?php echo buddyboss_theme()->learndash_helper()->print_sorting_options(); ?>
                            </select>
                        </div>
                      
                        <div class="select-wrap">
                            <?php if ('' !== trim(buddyboss_theme()->learndash_helper()->print_categories_options())) { ?>
                                <select id="sfwd_cats-order-by" name="filter-categories">
                                    <?php echo buddyboss_theme()->learndash_helper()->print_categories_options(); ?>
                                </select>
                            <?php } ?>
                        </div>
                        <div class="select-wrap">
                            <?php $member_child_cat = get_terms([
                                            'taxonomy'   => 'ld_course_category',
                                            'hide_empty' => false,
                                            'parent' => '121',
                                            'orderby' => 'meta_value_num',
                                            'order' => 'ASC',
											'meta_query' => [[
																'key' => 'course_category_order',
																'type' => 'NUMERIC',
															]],// Use the term ID of the parent category
                                        ]); 

                                    $operation_child_cat = get_terms([
                                            'taxonomy'   => 'ld_course_category',
                                            'hide_empty' => false,
                                            'parent' => '122',
                                            'orderby' => 'meta_value_num',
                                            'order' => 'ASC', // Use the term ID of the parent category
											'meta_query' => [[
																'key' => 'course_category_order',
																'type' => 'NUMERIC',
															]],
                                        ]); 

                                        $categoryMap = array(
												'all'		=> $member_child_cat,
                                                'member-training' => $member_child_cat,
                                                'operation-traning' => $operation_child_cat
                                            );

                                            // Encode the PHP arrays as JSON
                                            $categoryMapJson = json_encode($categoryMap);?>
                            <script>
                                    // Define an object mapping parent categories to their child categories
                                    var categoryMap = <?php echo $categoryMapJson; ?>;

                                    // Function to update filter-topics based on selected filter-categories
                                    function updateTopicsSelect() {
                                        var selectedCategory = document.getElementById('sfwd_cats-order-by').value;
                                        var topicsSelect = document.getElementById('sfwd_topics-order-by');

                                        console.log(selectedCategory);
                                        console.log(categoryMap[selectedCategory]);
                                        // Clear existing options
                                        topicsSelect.innerHTML = '<option value="all">All Topics</option>';

                                        // Add new options based on the selected parent category
                                        var topics = categoryMap[selectedCategory] || [];
                                        console.log(topics);
                                       //topics.unshift('all'); // Add 'All Topics' as the first option
                                        Object.keys(topics).forEach(function(key) {
                                          const topic = topics[key];
                                          
                                          const option = document.createElement('option');
                                          option.value = topic.slug;
                                          option.innerHTML = topic.name;
                                          topicsSelect.appendChild(option);
                                        });

                                    }

                                    // Attach the updateTopicsSelect function to the change event of filter-categories
                                    document.getElementById('sfwd_cats-order-by').addEventListener('change', updateTopicsSelect);

                                    // Initial population of filter-topics based on the default selected category
                                    updateTopicsSelect();
                                </script>
                            <?php 
                                        // Get all child categories of $categories
                                        $child_categories = get_terms([
                                            'taxonomy'         =>    'ld_course_category',
											'orderby'          =>    'meta_value_num',
											'order'            =>    'ASC',
											'hide_empty'       =>    false,
											'parent'		   =>	121,
											'meta_query' => [[
												'key' => 'course_category_order',
												'type' => 'NUMERIC',
											]],
                                        ]); 
                            ?>
                            <select id="sfwd_topics-order-by" name="filter-topics">
                                <option value="all">All Topics</option>
                                <?php foreach($child_categories as $Topic): ?>
                                <option value="<?php echo $Topic->name; ?>"><?php echo $Topic->name; ?></option> 
                                <?php endforeach; ?>
                            </select>
							
							   <!-- New code for language filter -->
							<?php
                                $languages = get_terms(
                                    array(
                                        'taxonomy' => 'ld_course_language',
                                        'hide_empty' => false,
                                    )
                                );

                                $default_language = 'english'; // Set your default language slug here
                            
                                if (!empty($languages) && !is_wp_error($languages)):
                                    ?>
                                    <select id="sfwd_languages-order-by" name="filter-languages">
                                        <option value="all">All Languages</option>
                                        <?php foreach ($languages as $language): ?>
                                            <option value="<?php echo esc_attr($language->slug); ?>" <?php selected($default_language, $language->slug); ?>>
                                                <?php echo esc_html($language->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                        </div>
                    </div>
                    <div class="bb-secondary-list-tabs flex align-items-center" id="subnav" aria-label="Members directory secondary navigation" role="navigation">
                        <div class="grid-filters" data-view="ld-course">
                            <a href="#" class="layout-view layout-view-course layout-grid-view bp-tooltip <?php echo esc_attr($class_grid_active); ?>" data-view="grid" data-bp-tooltip-pos="up" data-bp-tooltip="<?php esc_attr_e('Grid View', 'buddyboss-theme'); ?>">
                                <i class="dashicons dashicons-screenoptions" aria-hidden="true"></i>
                            </a>

                            <a href="#" class="layout-view layout-view-course layout-list-view bp-tooltip <?php echo esc_attr($class_list_active); ?>" data-view="list" data-bp-tooltip-pos="up" data-bp-tooltip="<?php esc_attr_e('List View', 'buddyboss-theme'); ?>">
                                <i class="dashicons dashicons-menu" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="grid-view bb-grid">
                    <div id="course-dir-list" class="course-dir-list bs-dir-list">
                       
                       
                       
                       
                       <?php


            

                        // @todo -  CUSTOM CODE START HERE
                        if (!empty($user_courses)) :
                            global $post;
                            $_post = $post;
                            $courses_userenrolled = learndash_user_get_enrolled_courses( $user_id, array(), true );
                            $childCategories = [];
                        
                            if (!isset($_REQUEST['filter-categories']) || (isset($_REQUEST['filter-categories']) && ($_REQUEST['filter-categories'] == 'all'))) :
                                $categories = get_terms([
                                    'taxonomy'         =>    'ld_course_category',
                                    'orderby'          =>    'meta_value_num',
                                    'order'            =>    'ASC',
                                    'hide_empty'       =>    true,
                                    'parent'         =>    0,
                                    'meta_query' => [[
                                        'key' => 'course_category_order',
                                        'type' => 'NUMERIC',
                                    ]],
                                ]);
                                //var_dump($categories);
                                //var_dump($courses_userenrolled);

                                if (is_array($categories) && count($categories) > 0) :
                                    foreach ($categories as $category) :
                                        $parent = $category->term_id; // Replace this with your actual parent term ID

                                            // Set hide_empty based on the parent value
                                            $hide_empty = ($parent === 122) ? true : false;
                                        $categories = get_terms([
                                            'taxonomy'         =>    'ld_course_category',
                                            'orderby'          =>    'meta_value_num',
                                            'order'            =>    'ASC',
                                            'hide_empty'       =>    $hide_empty,
                                            'parent'          =>    $parent,
                                            'post__in'      =>      $courses_userenrolled,
                                            'meta_query' => [[
                                                'key' => 'course_category_order',
                                                'type' => 'NUMERIC',
                                            ]],
                                        ]);
                                        if (is_array($categories) && count($categories) > 0) :
                                            foreach ($categories as $cat) :
                                                array_push($childCategories, $cat);
                                            endforeach;
                                        endif;
                                    endforeach;
                                endif;
                            endif;

                            $catUserCourses = [];
                            foreach ($user_courses as $course_id) :
                                $category = get_the_terms($course_id, 'ld_course_category');
                                if (is_array($category) && count($category) > 0) :
                                    foreach ($category as $term) :
                                        $catUserCourses[$term->term_id][] = $course_id;
                                    endforeach;

                                   

                                endif;
                            endforeach;
                            
                            //var_dump($childCategories);
                            foreach ($childCategories as $cat) :
                        ?>
                                <h2><?php echo $cat->name; ?></h2>
<!--                                 <div class="course_category_desc py-2"><?php echo nl2br($cat->description); ?></div> -->
                                <ul class="bb-course-list bb-course-items <?php echo esc_attr($class_grid_show . $class_list_show); ?>" aria-live="assertive" aria-relevant="all">
                                    <?php
                                    if (isset($catUserCourses[$cat->term_id]) && count($catUserCourses[$cat->term_id]) > 0) :
                                        $user_courses = $catUserCourses[$cat->term_id];
                                        
                                        foreach ($user_courses as $course_id) :
                                            if ($course_id !== 0) :
                                                $course = get_post($course_id);
                                                $post   = $course;
                                                get_template_part('learndash/ld30/template-course-item');                                                

                                            endif;
                                        endforeach;
                                    else :
                                        echo '<aside class="bp-feedback bp-template-notice ld-feedback info"><span class="bp-icon" aria-hidden="true"></span><p>';
                                        echo __('Sorry, No course avaliable.', 'buddyboss-theme');
                                        echo '</p></aside>';
                                    endif;
                                    ?>
                                </ul>
                            <?php
                            endforeach;
                            $post = $_post;
                        endif;


                        $page                = (int) $profile_pager['paged'];
                        $num_results_on_page = (int) $atts['per_page'];
                        $total_pages         = (int) $profile_pager['total_pages'];
                        $pagination_url      = trailingslashit($current_url) . 'page/';

                        if (1 < $total_pages) {
                            ?>
                            <div class="bb-lms-pagination">
                                <?php
                                if (1 < $page) {
                                    $j = $page - 1;

                                    echo sprintf(
                                        '<a class="prev page-numbers" id="page_a_link" href="%1$s">%2$s</a>',
                                        esc_url($pagination_url . $j),
                                        esc_html__('« Previous', 'buddyboss-theme')
                                    );
                                }

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    if ($i !== $page) {
                                        echo sprintf(
                                            '<span><a class="page-numbers" id="page_a_link" href="%1$s">%2$s</a></span>',
                                            esc_url($pagination_url . $i),
                                            esc_attr($i)
                                        );
                                    } else {
                                        echo sprintf(
                                            '<span class="current page-numbers" id="page_a_link" style="font-weight: bold;">%1$s</a></span>',
                                            esc_attr($i)
                                        );
                                    }
                                }

                                if ($page !== $total_pages) {
                                    $j = $page + 1;
                                    echo sprintf(
                                        '<a class="next page-numbers" id="page_a_link" href="%1$s">%2$s</a>',
                                        esc_url($pagination_url . $j),
                                        esc_html__('Next »', 'buddyboss-theme')
                                    );
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </form>
        <?php
        } else {


            $args = array(
                'hide_empty' => 0,
                'pad_counts' => true,
                'taxonomy'         =>    'ld_course_category',
                'child_of' => '121'
            );
            $categories = get_terms( $args ); 
            ?>
            <?php
            foreach($categories as $category) {
            
                if($category->count == 0) { ?>
                <h2><?php echo $category->name; ?></h2>
                <ul class="bb-course-list bb-course-items <?php echo esc_attr($class_grid_show . $class_list_show); ?>" aria-live="assertive" aria-relevant="all">
                    <?php
                    if (isset($catUserCourses[$cat->term_id]) && count($catUserCourses[$cat->term_id]) > 0) :
                        $user_courses = $catUserCourses[$cat->term_id];
                        foreach ($user_courses as $course_id) :
                            if ($course_id !== 0) :
                                $course = get_post($course_id);
                                $post   = $course;
                                get_template_part('learndash/ld30/template-course-item');
                            endif;
                        endforeach;
                    else:
                        echo '<aside class="bp-feedback bp-template-notice ld-feedback info"><span class="bp-icon" aria-hidden="true"></span><p>';
                        echo __('Sorry, No course avaliable.', 'buddyboss-theme');
                        echo '</p></aside>';
                    endif;
                    ?>
                </ul>
                <?php 
                } 
        
            }

            //shivdev ends
        ?>


                                

            <aside class="bp-feedback bp-messages info">
                <span class="bp-icon" aria-hidden="true"></span>
                <p>
                    <?php
                    printf(
                        /* translators: The course label. */
                        esc_html__('Sorry, no %s were found.', 'buddyboss-theme'),
                        esc_html(LearnDash_Custom_Label::label_to_lower('courses'))
                    );
                    ?>
                </p>
            </aside>
        <?php
        }
        ?>
    </div>
</div>