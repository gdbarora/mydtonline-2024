<?php
class Courses extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'courses',
            'Courses',
            'Display Courses Stats'
        );
    }
    protected function initial_widget_content()
    {
    }

    protected function getWidgetContent()
{
    // Calculate dates for the current month, previous month, and month before previous
    $current_month_start = strtotime(date('Y-m-01'));
    $next_month_start = strtotime(date('Y-m-01', strtotime('+1 month')));
    $previous_month_start = strtotime(date('Y-m-01', strtotime('-1 month')));
    $month_before_previous_start = strtotime(date('Y-m-01', strtotime('-2 months')));
    

    // Get data for the current month, previous month, and month before previous
    $data_current_month = $this->getData($current_month_start, $next_month_start);
    $data_previous_month = $this->getData($previous_month_start, $current_month_start);
    $data_month_before_previous = $this->getData($month_before_previous_start, $previous_month_start);

    // Get formatted month names with year
    $current_month_name = date('F Y', $current_month_start);
    $previous_month_name = date('F Y', $previous_month_start);
    $month_before_previous_name = date('F Y', $month_before_previous_start);

    // Prepare the HTML content with divs
    $html_content = '<section class="dt-flex"><div class="period-container">';
    $html_content .= '<h2>Results for ' . $current_month_name . '</h2>';
    $html_content .= $this->generateCourseList($data_current_month);
    $html_content .= '</div>';

    $html_content .= '<div class="period-container">';
    $html_content .= '<h2>Results for ' . $previous_month_name . '</h2>';
    $html_content .= $this->generateCourseList($data_previous_month);
    $html_content .= '</div>';

    $html_content .= '<div class="period-container">';
    $html_content .= '<h2>Results for ' . $month_before_previous_name . '</h2>';
    $html_content .= $this->generateCourseList($data_month_before_previous);
    $html_content .= '</div></section>';

    $response = [
        'widget_id' => $this->id_base,
        'html_content' => $html_content,
    ];

    echo json_encode($response);
}


private function generateCourseList($data)
{
    if (!empty($data)) {
        // Sort data to get the top 3 fastest and slowest completion times
        usort($data, function ($a, $b) {
            return $a->AvgCompletionTime <=> $b->AvgCompletionTime;
        });

        // Get the top 3 fastest completion times
        $fastest_completion = array_slice($data, 0, 3);

        // Get the top 3 slowest completion times
        $slowest_completion = array_slice(array_reverse($data), 0, 3);

        $html_content = '<h3>Fastest Completion</h3>';
        $html_content .= '<table>';
        $html_content .= '<tr><th>Course Name</th><th>Avg Completion Time</th></tr>';
        foreach ($fastest_completion as $course) {
            $html_content .= '<tr>';
            $html_content .= '<td>' . $course->CourseName . '</td>';
            $html_content .= '<td>' . $course->AvgCompletionTime . '</td>';
            $html_content .= '</tr>';
        }
        $html_content .= '</table>';

        $html_content .= '<h3>Slowest Completion</h3>';
        $html_content .= '<table>';
        $html_content .= '<tr><th>Course Name</th><th>Avg Completion Time</th></tr>';
        foreach ($slowest_completion as $course) {
            $html_content .= '<tr>';
            $html_content .= '<td>' . $course->CourseName . '</td>';
            $html_content .= '<td>' . $course->AvgCompletionTime . '</td>';
            $html_content .= '</tr>';
        }
        $html_content .= '</table>';
    } else {
        $html_content = '<p>No data available</p>';
    }

    return $html_content;
}



    protected function getTableData()
    {
        // Get data from the getData function
        $data = $this->getData();



        // Initialize the HTML table
        $htmlTable = '<table class="display compact">';

        // Create the table header row
        $htmlTable .= '<thead>';
        $htmlTable .= '<tr>';
        $htmlTable .= '<th>Course Name</th>';
        $htmlTable .= '<th>Avg Completion Time</th>';
        $htmlTable .= '<th>Creation Date</th>';
        $htmlTable .= '<th>Completed</th>';
        $htmlTable .= '<th>In Progress</th>';
        $htmlTable .= '</tr>';
        $htmlTable .= '</thead>';

        // Create the table body
        $htmlTable .= '<tbody>';

        // Iterate over the data and create table rows
        foreach ($data as $row) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row->CourseName . '</td>';
            $htmlTable .= '<td>' . $row->AvgCompletionTime . '</td>';
            $htmlTable .= '<td>' . $row->CreationDate . '</td>';
            $htmlTable .= '<td>' . $row->UserCountCompleted . '</td>';
            $htmlTable .= '<td>' . $row->UserCountInProgress . '</td>';
            $htmlTable .= '</tr>';
        }

        // Close the table body and table
        $htmlTable .= '</tbody>';
        $htmlTable .= '</table>';

        // Return the generated HTML table
        return $htmlTable;
    }

    protected function getData($start_date = null, $end_date = null)
    {
        global $wpdb;

        // Define the base SQL query
        $sql = "
        SELECT
        wp_posts.post_title AS CourseName,
        CONCAT(
            FLOOR(AVG(completion_time_in_seconds) / 86400), 'd ',
            FLOOR((AVG(completion_time_in_seconds) % 86400) / 3600), 'h ',
            FLOOR((AVG(completion_time_in_seconds) % 3600) / 60), 'm ',
            FLOOR(AVG(completion_time_in_seconds) % 60), 's'
        ) AS AvgCompletionTime,
        DATE(wp_posts.post_date) AS CreationDate,
        IFNULL(completed_user_count, 0) AS UserCountCompleted,
        IFNULL(in_progress_user_count, 0) AS UserCountInProgress
    FROM
        wp_posts
    JOIN
        (
            SELECT
                wp_learndash_user_activity.post_id,
                CASE
                    WHEN wp_learndash_user_activity.activity_completed IS NOT NULL AND wp_learndash_user_activity.activity_completed <> 0 THEN
                        ABS(
                            TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(wp_learndash_user_activity.activity_started), FROM_UNIXTIME(wp_learndash_user_activity.activity_completed))
                        )
                    ELSE
                        NULL
                END AS completion_time_in_seconds
            FROM
                wp_learndash_user_activity
            " . ($start_date && $end_date ? "WHERE wp_learndash_user_activity.activity_completed >= %s AND wp_learndash_user_activity.activity_completed <= %s" : "") . "
        ) AS completion_times
    ON
        wp_posts.ID = completion_times.post_id
    LEFT JOIN
        (
            SELECT
                post_id,
                COUNT(DISTINCT user_id) AS completed_user_count
            FROM
                wp_learndash_user_activity
            WHERE
                activity_completed IS NOT NULL AND activity_completed <> 0
            GROUP BY
                post_id
        ) AS completed_user_counts
    ON
        wp_posts.ID = completed_user_counts.post_id
    LEFT JOIN
        (
            SELECT
                post_id,
                COUNT(DISTINCT user_id) AS in_progress_user_count
            FROM
                wp_learndash_user_activity
            WHERE
                activity_completed IS NULL OR activity_completed = 0
            GROUP BY
                post_id
        ) AS in_progress_user_counts
    ON
        wp_posts.ID = in_progress_user_counts.post_id
    WHERE
        wp_posts.post_type = 'sfwd-courses'
    GROUP BY
        wp_posts.ID, wp_posts.post_title, completed_user_counts.completed_user_count, in_progress_user_counts.in_progress_user_count;    
    ";

        // Prepare the SQL query with optional parameters
        if ($start_date && $end_date) {
            $sql = $wpdb->prepare($sql, $start_date, $end_date);
        }

        // Execute the SQL query
        $results = $wpdb->get_results($sql);

        return $results;
    }


}