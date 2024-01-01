<?php
class Posts_With_Most_Engagement extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'posts-with-most-engagement',
            'Posts With Most Engagement',
            'Display Posts With Most Engagements'
        );
    }

    protected function initial_widget_content(){}

    protected function getData()
    {
        global $wpdb;
        // Fetch data for the selected year and return it as JSON
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                ua.id AS Post_ID,
                CASE
                    WHEN ua.component = 'activity' THEN u.display_name
                    WHEN ua.component = 'groups' THEN g.name
                    ELSE NULL
                END AS Location,
                DATE(ua.date_recorded) AS Post_Date,
                u.display_name AS Author,
                ua.content AS Post_Content,
                IF(ua.id = likes.activity_id, likes.meta_value, 0) AS Reactions,
                COUNT(DISTINCT CASE WHEN ca.type = 'activity_comment' THEN ca.id END) AS Comments
            FROM wp_bp_activity ua
            LEFT JOIN wp_users u ON ua.user_id = u.ID
            LEFT JOIN wp_bp_groups g ON ua.item_id = g.id AND ua.component = 'groups'
            LEFT JOIN wp_bp_activity ca ON ua.id = ca.item_id
            LEFT JOIN (
                SELECT activity_id, meta_value
                FROM wp_bp_activity_meta
                WHERE meta_key = 'favorite_count'
            ) likes ON likes.activity_id = ua.id
            WHERE ua.type = 'activity_update'
                AND ua.component IN ('groups', 'activity')
                
            GROUP BY ua.id, ua.item_id, Post_Date, Post_Content, Location, Reactions"
            )
        );

        return $results;
    }

    public function getTableData()
    {
        global $wpdb;
    
        // Fetch data using the getData method
        $data = $this->getData();
    
        // Define an empty HTML table
        $tableHtml = '<table class="display compact">';
        $tableHtml .= '<thead><tr><th>Post ID</th><th>Location</th><th>Post Date</th><th>Author</th><th>Post Content</th><th>Reactions</th><th>Comments</th></tr></thead>';
        $tableHtml .= '<tbody>';
    
        // Loop through the data and add rows to the table
        foreach ($data as $row) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td><a href="' . esc_url(bp_activity_get_permalink($row->Post_ID)) . '">' . esc_html($row->Post_ID) . '</a></td>';
            $tableHtml .= '<td>' . esc_html($row->Location) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Post_Date) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Author) . '</td>';
            $tableHtml .= '<td>' . wp_kses_post($row->Post_Content) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Reactions) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Comments) . '</td>';
            $tableHtml .= '</tr>';
        }
    
        // Close the table body and table
        $tableHtml .= '</tbody>';
        $tableHtml .= '</table>';
    
        return $tableHtml;
    }
    

    protected function getWidgetContent()
{
    global $wpdb;

    // Fetch data using the getData method
    $data = $this->getData();

    // Initialize arrays to store post data
    $topPostsThisWeek = [];
    $topPostsAllTime = [];

    // Sort the data by Reactions for this week
    usort($data, function ($a, $b) {
        return $b->Reactions - $a->Reactions;
    });

    // Extract the top 5 posts for this week
    $topPostsThisWeek = array_slice($data, 0, 5);

    // Sort the data by Reactions for all time
    usort($data, function ($a, $b) {
        return $b->Reactions - $a->Reactions;
    });

    // Extract the top 5 posts for all time
    $topPostsAllTime = array_slice($data, 0, 5);

    // Generate HTML for the top posts this week
    $htmlContentThisWeek = $this->generatePostHtml($topPostsThisWeek, 'Top Posts This Week');

    // Generate HTML for the top posts all time
    $htmlContentAllTime = $this->generatePostHtml($topPostsAllTime, 'Top Posts All Time');

    // Prepare data in the requested format
    $response = [
        'widget_id' => $this->id_base,
        'html_content' => $htmlContentThisWeek.$htmlContentAllTime,
        'html_content_all_time' => $htmlContentAllTime,
    ];

    echo json_encode($response);
}

// Helper function to generate HTML for top posts
protected function generatePostHtml($posts, $title)
{
    $html = '<h3>' . $title . '</h3>';
    $html .= '<table>';
    $html .= '<thead><tr><th>Post ID</th><th>Reactions</th><th>Comments</th></tr></thead>';
    $html .= '<tbody>';

    foreach ($posts as $post) {
        $html .= '<tr>';
        $html .= '<td><a href="' . esc_url(bp_activity_get_permalink($post->Post_ID)) . '">' . esc_html($post->Post_ID) . '</a></td>';
        // $html .= '<td>' . esc_html($post->Location) . '</td>';
        // $html .= '<td>' . esc_html($post->Post_Date) . '</td>';
        // $html .= '<td>' . esc_html($post->Author) . '</td>';
        // $html .= '<td>' . esc_html($post->Post_Content) . '</td>';
        $html .= '<td>' . esc_html($post->Reactions) . '</td>';
        $html .= '<td>' . esc_html($post->Comments) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}

}
