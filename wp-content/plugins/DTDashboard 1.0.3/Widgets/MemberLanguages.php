<?php
class Member_Languages extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'member-languages',
            'Member Languages',
            'Display Members Languages'
        );
    }
    protected function initial_widget_content()
    {
        echo '<label for="start_date">From:</label>';
        echo '<input type="date" id="start_date" name="start_date">';

        echo '<label for="end_date">To:</label>';
        echo '<input type="date" id="end_date" name="end_date">';
    }


    protected function getData($selectedYear = null, $fromDate = null, $toDate = null)
    {
        global $wpdb;

        $query = "SELECT
        u.ID,
        DATE(u.user_registered) AS Start_Date,
        COALESCE(MAX(CASE WHEN field.name = 'First Name' THEN data.value END), '---') AS First_Name,
        COALESCE(MAX(CASE WHEN field.name = 'Last Name' THEN data.value END), '---') AS Last_Name,
        CASE WHEN last_activity.date_recorded >= NOW() - INTERVAL 72 HOUR THEN 'Active' ELSE 'Inactive' END AS Status,
        COALESCE(MAX(CASE WHEN field.name = 'Enagic Rank' THEN data.value END), '---') AS Rank,
        COALESCE(MAX(CASE WHEN field.name = 'Country' THEN data.value END), '---') AS Country,
        COALESCE(MAX(CASE WHEN field.name = 'Select Primary Language' THEN data.value END), '---') AS Language
    FROM
        wp_users AS u
    LEFT JOIN
        wp_bp_xprofile_data AS data ON u.ID = data.user_id
    LEFT JOIN
        wp_bp_xprofile_fields AS field ON field.id = data.field_id
    LEFT JOIN (
        SELECT
            user_id,
            MAX(date_recorded) AS date_recorded
        FROM
            wp_bp_activity
        WHERE
            type = 'last_activity'
        GROUP BY
            user_id
    ) AS last_activity ON u.ID = last_activity.user_id
    WHERE
        u.user_status = 0
        AND field.name IN (
            'Country',
            'Enagic Rank',
            'First Name',
            'Last Name',
            'Select Primary Language'
        )
    GROUP BY
        u.ID
    ";

        if ($fromDate && $toDate) {
            $query .= " HAVING Start_Date BETWEEN %s AND %s";
            $results = $wpdb->get_results($wpdb->prepare($query, $fromDate, $toDate));
        } else {
            $results = $wpdb->get_results($query);
        }

        return $results;
    }


    public function getTableData($selectedYear = null, $fromDate = null, $toDate = null)
    {
        // Fetch data using the getData method
        $data = $this->getData($selectedYear, $fromDate, $toDate);

        // Define an empty HTML table
        $tableHtml = '<table class="display compact">';
        $tableHtml .= '<thead><tr><th>Start Date</th><th>First Name</th><th>Last Name</th><th>Status</th><th>Rank</th><th>Country</th><th>Language</th></tr></thead>';
        $tableHtml .= '<tbody>';

        // Loop through the data and add rows to the table
        foreach ($data as $row) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . esc_html($row->Start_Date) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->First_Name) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Last_Name) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Status) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Rank) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Country) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->Language) . '</td>';
            $tableHtml .= '</tr>';
        }

        // Close the table body and table
        $tableHtml .= '</tbody>';
        $tableHtml .= '</table>';

        return $tableHtml;

    }


    protected function getWidgetContent($selectedYear = null, $fromDate = null, $toDate = null)
    {
        // Get data using the getData method
        $data = $this->getData($selectedYear, $fromDate, $toDate);

        // Initialize an associative array to store the count of users by country
        $languageCounts = [];

        // Loop through the data and count users by country, excluding '---'
        foreach ($data as $row) {
            $language = $row->Language;

            // Exclude '---'
            if ($language !== '---') {
                // Check if the country already exists in the array, if not, initialize it to 0
                if (!isset($languageCounts[$language])) {
                    $languageCounts[$language] = 0;
                }

                // Increment the count for the current country
                $languageCounts[$language]++;
            }
        }

        // Sort the countries by count in descending order
        arsort($languageCounts);

        // Take the top 5 countries
        $top5Languages = array_slice($languageCounts, 0, 5, true);

        // Prepare data in the requested format
        $data = [
            'widget_id' => $this->id_base,
            'html_content' => $this->getLanguagesWidgetHtml($top5Languages),
            'top_5_countries' => $top5Languages,
        ];

        echo json_encode($data);
    }

    // Helper function to generate HTML content for the country widget
    protected function getLanguagesWidgetHtml($top5Languages)
    {
        // Prepare an HTML table to display the top 5 countries
        $html = '<table>';
        $html .= '<thead><tr><th>Language</th><th>Members</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($top5Languages as $language => $count) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($language) . '</td>';
            $html .= '<td>' . esc_html($count) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}