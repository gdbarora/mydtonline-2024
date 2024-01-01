<?php
class Groups extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'groups',
            'Groups',
            'Display Group Stats'
        );
    }

    protected function initial_widget_content()
    {
    }

    protected function getData()
    {
        global $wpdb;
        // Fetch data for the selected year and return it as JSON
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                g.id AS group_id,
                g.name AS group_name,
                COUNT(DISTINCT m.user_id) AS total_member_count,
                (
                    SELECT COUNT(DISTINCT a.user_id)
                    FROM {$wpdb->prefix}bp_activity AS a
                    WHERE a.component = 'groups'
                    AND a.item_id = g.id
                    AND a.date_recorded >= DATE_SUB(NOW(), INTERVAL 72 HOUR)
                ) AS active_member_count,
                (
                    SELECT COALESCE(MAX(DATE(a.date_recorded)), '---') AS last_post_date
                    FROM {$wpdb->prefix}bp_activity AS a
                    WHERE a.component = 'groups'
                    AND a.item_id = g.id
                ) AS last_post_date,
                (
                    SELECT COUNT(user_id) 
                    FROM {$wpdb->prefix}bp_activity 
                    WHERE component = 'groups' 
                    AND type = 'activity_update' 
                    AND item_id = g.id 
                    AND date_recorded >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
                ) AS activity_update_count_this_week,
                (
                    SELECT COUNT(user_id) 
                    FROM {$wpdb->prefix}bp_activity 
                    WHERE component = 'groups' 
                    AND type = 'activity_update' 
                    AND item_id = g.id 
                    AND date_recorded >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
                ) AS activity_update_count_this_year
            FROM
                {$wpdb->prefix}bp_groups AS g
            LEFT JOIN
                {$wpdb->prefix}bp_groups_members AS m
            ON
                g.id = m.group_id
            GROUP BY
                g.id, g.name
            HAVING
                total_member_count > 0
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
        $tableHtml .= '<thead><tr><th>Group ID</th><th>Group Name</th><th>Population</th><th>Active Members</th><th>Inactive Members</th><th>Most Recent Post</th><th>Posts This Week</th><th>Posts This Year</th></tr></thead>';
        $tableHtml .= '<tbody>';

        // Loop through the data and add rows to the table
        foreach ($data as $row) {
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . esc_html($row->group_id) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->group_name) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->total_member_count) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->active_member_count) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->total_member_count - $row->active_member_count) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->last_post_date) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->activity_update_count_this_week) . '</td>';
            $tableHtml .= '<td>' . esc_html($row->activity_update_count_this_year) . '</td>';
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

        // Initialize arrays to store group data
        $largestGroups = [];
        $smallestGroups = [];
        $busiestGroups = [];
        $quietestGroups = [];

        // Sort the data by the total_member_count
        usort($data, function ($a, $b) {
            return $b->total_member_count - $a->total_member_count;
        });

        // Extract the top 5 largest and smallest groups
        $largestGroups = array_slice($data, 0, 5);
        $smallestGroups = array_slice($data, -5);

        // Sort the data by the active_member_count
        usort($data, function ($a, $b) {
            return $b->active_member_count - $a->active_member_count;
        });

        // Extract the top 5 busiest and quietest groups
        $busiestGroups = array_slice($data, 0, 5);
        $quietestGroups = array_slice($data, -5);

        // Generate HTML for the groups
        $htmlContent = $this->generateGroupHtml($largestGroups, 'Largest Groups');
        $htmlContent .= $this->generateGroupHtml($smallestGroups, 'Smallest Groups');
        $htmlContent .= $this->generateGroupHtml($busiestGroups, 'Busiest Groups');
        $htmlContent .= $this->generateGroupHtml($quietestGroups, 'Quietest Groups');


        // Prepare data in the requested format
        $response = [
            'widget_id' => $this->id_base,
            'html_content' => $htmlContent,
        ];

        echo json_encode($response);
    }

    // Helper function to generate HTML for groups
    protected function generateGroupHtml($groups, $title)
    {
        $html = '<div><h3 class="group-type-title">' . $title . '</h3>';
        $html .= '<table>';
        $html .= '<thead><tr><th>Group ID</th><th>Group Name</th><th>Population</th><th>Active Members</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($groups as $group) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($group->group_id) . '</td>';
            $html .= '<td>' . esc_html($group->group_name) . '</td>';
            $html .= '<td>' . esc_html($group->total_member_count) . '</td>';
            $html .= '<td>' . esc_html($group->active_member_count) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table></div>';

        return $html;
    }



}