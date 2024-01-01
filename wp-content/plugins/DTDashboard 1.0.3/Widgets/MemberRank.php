<?php     
class Member_Rank extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'member-rank',
            'Member Rank',
            'Display Members Rank'
        );
    }

    protected function initial_widget_content(){}

    protected function getData()
    {
        global $wpdb;
        // Fetch data for the selected year and return it as JSON
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT u.ID,
                DATE(u.user_registered) AS Start_Date,
                COALESCE(MAX(CASE WHEN field.name = 'First Name' THEN data.value END), '---') AS First_Name,
                COALESCE(MAX(CASE WHEN field.name = 'Last Name' THEN data.value END), '---') AS Last_Name,
                CASE WHEN last_activity.date_recorded >= NOW() - INTERVAL 72 HOUR THEN 'Active' ELSE 'Inactive' END AS Status,
                COALESCE(MAX(CASE WHEN field.name = 'Enagic Rank' THEN data.value END), '---') AS Rank,
                COALESCE(MAX(CASE WHEN field.name = 'Country' THEN data.value END), '---') AS Country,
                COALESCE(MAX(CASE WHEN field.name = 'Distributorship Duration' THEN data.value END), '---') AS Distributorship_Duration
         FROM {$wpdb->users} AS u
         LEFT JOIN {$wpdb->prefix}bp_xprofile_data AS data ON u.ID = data.user_id
         LEFT JOIN {$wpdb->prefix}bp_xprofile_fields AS field ON field.id = data.field_id
         LEFT JOIN (SELECT user_id, MAX(date_recorded) AS date_recorded FROM {$wpdb->prefix}bp_activity WHERE type = 'last_activity' GROUP BY user_id) AS last_activity ON u.ID = last_activity.user_id
         WHERE u.user_status = 0
         AND field.name IN (
             'Country',
             'Enagic Rank',
             'First Name',
             'Last Name',
             'Distributorship Duration'
         )
         GROUP BY u.ID;
         "
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
    $tableHtml .= '<thead><tr><th>Start Date</th><th>Distributorship Duration</th><th>First Name</th><th>Last Name</th><th>Rank</th><th>Status</th><th>Country</th></tr></thead>';
    $tableHtml .= '<tbody>';

    // Loop through the data and add rows to the table
    foreach ($data as $row) {
        $tableHtml .= '<tr>';
        $tableHtml .= '<td>' . esc_html($row->Start_Date) . '</td>';        
        $tableHtml .= '<td>' . esc_html($row->Distributorship_Duration) . '</td>';
        $tableHtml .= '<td>' . esc_html($row->First_Name) . '</td>';
        $tableHtml .= '<td>' . esc_html($row->Last_Name) . '</td>';
        $tableHtml .= '<td>' . esc_html($row->Rank) . '</td>';
        $tableHtml .= '<td>' . esc_html($row->Status) . '</td>';
        $tableHtml .= '<td>' . esc_html($row->Country) . '</td>';
        $tableHtml .= '</tr>';
    }

    // Close the table body and table
    $tableHtml .= '</tbody>';
    $tableHtml .= '</table>';

    return $tableHtml;
}

protected function getWidgetContent()
{
    // Get data using the getData method
    $data = $this->getData();

    // Initialize an associative array to store the count of users by rank
    $rankCounts = [];

    // Define the value to exclude as a non-rank
    $excludedValue = '---'; // Change this value to the one you want to exclude

    // Loop through the data and count users by rank, excluding the specified value
    foreach ($data as $row) {
        $rank = $row->Rank;

        // Exclude the specified value
        if ($rank !== $excludedValue) {
            // Check if the rank already exists in the array, if not, initialize it to 0
            if (!isset($rankCounts[$rank])) {
                $rankCounts[$rank] = 0;
            }

            // Increment the count for the current rank
            $rankCounts[$rank]++;
        }
    }

    // Prepare data in the requested format
    $data = [
        'widget_id' => $this->id_base,
        'html_content' => $this->getRankWidgetHtml($rankCounts),
        'rank_counts' => $rankCounts,
    ];

    echo json_encode($data);
}


// Helper function to generate HTML content for the rank widget
protected function getRankWidgetHtml($rankCounts)
{
    // Prepare an HTML table to display the rank counts
    $html = '<table>';
    $html .= '<thead><tr><th>Rank</th><th>Members</th></tr></thead>';
    $html .= '<tbody>';

    foreach ($rankCounts as $rank => $count) {
        $html .= '<tr>';
        $html .= '<td>' . esc_html($rank) . '</td>';
        $html .= '<td>' . esc_html($count) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}


}