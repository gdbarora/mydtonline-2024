<?php
class Member_Location extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'member-location',
            'Member Location',
            'Display Members Locations'
        );
    }

    protected function initial_widget_content()
    {
        echo '<label for="start_date">From:</label>';
        echo '<input type="date" id="start_date" name="start_date">';

        echo '<label for="end_date">To:</label>';
        echo '<input type="date" id="end_date" name="end_date">';
    }


    protected function getData($selectedYear=null, $fromDate = null, $toDate = null)
    {
        global $wpdb;

        $query = "
            SELECT u.ID,
                DATE(u.user_registered) AS Start_Date,
                COALESCE(MAX(CASE WHEN field.name = 'First Name' THEN data.value END), '---') AS First_Name,
                COALESCE(MAX(CASE WHEN field.name = 'Last Name' THEN data.value END), '---') AS Last_Name,
                CASE WHEN last_activity.date_recorded >= NOW() - INTERVAL 72 HOUR THEN 'Active' ELSE 'Inactive' END AS Status,
                COALESCE(MAX(CASE WHEN field.name = 'Enagic Rank' THEN data.value END), '---') AS Rank,
                COALESCE(MAX(CASE WHEN field.name = 'Country' THEN data.value END), '---') AS Country,
                COALESCE(MAX(CASE WHEN field.name = 'City' THEN data.value END), '---') AS City,
                COALESCE(MAX(CASE WHEN field.name = 'Province / State' THEN data.value END), '---') AS 'Prov / State'
            FROM {$wpdb->users} AS u
            LEFT JOIN {$wpdb->prefix}bp_xprofile_data AS data ON u.ID = data.user_id
            LEFT JOIN {$wpdb->prefix}bp_xprofile_fields AS field ON field.id = data.field_id
            LEFT JOIN (SELECT user_id, MAX(date_recorded) AS date_recorded FROM {$wpdb->prefix}bp_activity WHERE type = 'last_activity' GROUP BY user_id) AS last_activity ON u.ID = last_activity.user_id
            WHERE u.user_status = 0
            AND field.name IN (
                'Country',
                'City',
                'Province / State',
                'Enagic Rank',
                'First Name',
                'Last Name'
            )
            GROUP BY u.ID";

        if ($fromDate && $toDate) {
            $query .= " HAVING Start_Date BETWEEN %s AND %s";
            $results = $wpdb->get_results($wpdb->prepare($query, $fromDate, $toDate));
        } else {
            $results = $wpdb->get_results($query);
        }

        return $results;
    }


    public function getTableData($selectedYear=null, $fromDate = null, $toDate = null)
    {
        global $wpdb;

        // Fetch data using the getData method
        $data = $this->getData(null, $fromDate, $toDate);

        // Define an empty HTML table
        $tableHtml = '<table class="display compact">';
        $tableHtml .= '<thead><tr><th>Start Date</th><th>First Name</th><th>Last Name</th><th>Status</th><th>Rank</th><th>City</th><th>Prov / State</th><th>Country</th></tr></thead>';
        $tableHtml .= '<tbody>';

        // Loop through the data and add rows to the table
        foreach ($data as $row) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . esc_html($row->Start_Date) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->First_Name) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Last_Name) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Rank) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Status) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->City) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->{'Prov / State'}) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Country) . '</td>';
            $tableHtml .= '</tr>';
        }

        // Close the table body and table
        $tableHtml .= '</tbody>';
        $tableHtml .= '</table>';

        return $tableHtml;
    }

    protected function getWidgetContent($selectedYear, $fromDate, $toDate)
    {
        // Get data using the getData method
        $data = $this->getData($selectedYear, $fromDate, $toDate);

        // Initialize an associative array to store the count of users by country
        $countryCounts = [];

        // Loop through the data and count users by country, excluding '---'
        foreach ($data as $row) {
            $country = $row->Country;

            // Exclude '---'
            if ($country !== '---') {
                // Check if the country already exists in the array, if not, initialize it to 0
                if (!isset($countryCounts[$country])) {
                    $countryCounts[$country] = 0;
                }

                // Increment the count for the current country
                $countryCounts[$country]++;
            }
        }

        // Sort the countries by count in descending order
        arsort($countryCounts);

        // Take the top 5 countries
        $top5Countries = array_slice($countryCounts, 0, 5, true);

        // Prepare data in the requested format
        $data = [
            'widget_id' => $this->id_base,
            'html_content' => $this->getCountryWidgetHtml($top5Countries),
            'top_5_countries' => $top5Countries,
        ];

        echo json_encode($data);
    }

    // Helper function to generate HTML content for the country widget
    protected function getCountryWidgetHtml($top5Countries)
    {
        // Prepare an HTML table to display the top 5 countries
        $html = '<table>';
        $html .= '<thead><tr><th>Country</th><th>Members</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($top5Countries as $country => $count) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($country) . '</td>';
            $html .= '<td>' . esc_html($count) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}