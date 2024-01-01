<?php
abstract class Base_DT_Widget extends WP_Widget
{

    public function __construct($widget_id, $widget_name, $widget_description)
    {
        parent::__construct(
            $widget_id,
            $widget_name,
            array('description' => $widget_description)
        );

        // add_action('wp_ajax_initial_widget_content', array($this, 'initial_widget_content'));
        // add_action('wp_ajax_initial_table_content', array($this, 'initial_table_content'));
        add_action('wp_ajax_table_content', array($this, 'table_content'));
        add_action('wp_ajax_widget_content', array($this, 'widget_content'));
    }

    public function widget($args, $instance)
    {
        // Define variables for widget attributes
        $widget_id = $this->id_base;
        $widget_title = $this->name;

        // Start the widget container
        echo '<div id="' . esc_attr($widget_id) . '" class="postbox">';

        // Widget Header
        echo '<div class="postbox-header">';
        echo '<h2 class="hndle ui-sortable-handle">' . esc_html($widget_title) . '</h2>';

        // Handle Actions (if needed)
        echo '<div class="handle-actions hide-if-no-js">';
        // echo '<button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="handle-order-higher-description">';
        // echo '<span class="screen-reader-text">Move up</span>';
        // echo '<span class="order-higher-indicator" aria-hidden="true"></span>';
        // echo '</button>';
        // echo '<span class="hidden" id="' . esc_attr($widget_id) . '-handle-order-higher-description">Move ' . esc_html($widget_title) . ' box up</span>';
        // echo '<button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="handle-order-lower-description">';
        // echo '<span class="screen-reader-text">Move down</span>';
        // echo '<span class="order-lower-indicator" aria-hidden="true"></span>';
        // echo '</button>';
        // echo '<span class="hidden" id="' . esc_attr($widget_id) . '-handle-order-lower-description">Move ' . esc_html($widget_title) . ' box down</span>';
        echo '<button type="button" class="handlediv" aria-expanded="true">';
        echo '<span class="screen-reader-text">Toggle panel: ' . esc_html($widget_title) . '</span>';
        echo '<span class="toggle-indicator" aria-hidden="true"></span>';
        echo '</button>';
        echo '</div></div>';

        // Widget Content
        echo '<div class="inside">';
        echo '<div class="controls">';
        $this->initial_widget_content();
        echo '</div>';
        echo '<div id="' . esc_attr($widget_id) . '-content">';
        echo '';
        echo '</div>';
        echo '</div>';


        // End the widget container
        echo '</div>';

    }

    abstract protected function initial_widget_content();

    public function table_content()
    { // Check if 'selectedYear' is set in the POST data and not empty
        if (isset($_POST['selectedYear']) && !empty($_POST['selectedYear'])) {
            // Sanitize the selected year
            $selectedYear = sanitize_text_field($_POST['selectedYear']);
        }

        // Check if 'fromDate' is set in the POST data and not empty
        if (isset($_POST['fromDate']) && !empty($_POST['fromDate'])) {
            // Sanitize the 'fromDate'
            $fromDate = sanitize_text_field($_POST['fromDate']);
        }

        // Check if 'toDate' is set in the POST data and not empty
        if (isset($_POST['toDate']) && !empty($_POST['toDate'])) {
            // Sanitize the 'toDate'
            $toDate = sanitize_text_field($_POST['toDate']);
        }

        $widgetId = sanitize_text_field($_POST['widgetId']);
        $widgetInstance = $this->get_widget_instance_by_id($widgetId);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            // Pass the parameters that are defined or not null to getWidgetContent
            $widgetContent = $widgetInstance->getTableData($selectedYear, $fromDate, $toDate);
            echo $widgetContent;
            wp_die();
        }
    }

    public function widget_content()
    {
        // Initialize variables
        $selectedYear = null;
        $fromDate = null;
        $toDate = null;

        // Check if 'selectedYear' is set in the POST data and not empty
        if (isset($_POST['selectedYear']) && !empty($_POST['selectedYear'])) {
            // Sanitize the selected year
            $selectedYear = sanitize_text_field($_POST['selectedYear']);
        }

        // Check if 'fromDate' is set in the POST data and not empty
        if (isset($_POST['fromDate']) && !empty($_POST['fromDate'])) {
            // Sanitize the 'fromDate'
            $fromDate = sanitize_text_field($_POST['fromDate']);
        }

        // Check if 'toDate' is set in the POST data and not empty
        if (isset($_POST['toDate']) && !empty($_POST['toDate'])) {
            // Sanitize the 'toDate'
            $toDate = sanitize_text_field($_POST['toDate']);
        }

        $widgetId = sanitize_text_field($_POST['widgetId']);
        $widgetInstance = $this->get_widget_instance_by_id($widgetId);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            // Pass the parameters that are defined or not null to getWidgetContent
            $widgetContent = $widgetInstance->getWidgetContent($selectedYear, $fromDate, $toDate);
            wp_die();
        }
    }

    protected function get_widget_instance_by_id($widget_id)
    {
        global $wp_widget_factory;

        $registered_widgets = $wp_widget_factory->widgets;
        foreach ($registered_widgets as $widget) {
            if ($widget->id_base === $widget_id) {
                return $widget;
            }
        }

    }

}
?>