<?php
class Member_Growth extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'member-growth',
            'Member Growth',
            'Display Profiles Created, Terminated monthly'
        );
    }
    protected function initial_widget_content()
    {
        echo '<label for="year">Select Year:</label>';
        echo '<select id="year" name="year">';
        $currentYear = date("Y");

        // Loop to create options for years
        for ($year = $currentYear; $year >= 2020; $year--) {
            echo '<option value="' . $year . '"' . ($year == $currentYear ? ' selected' : '') . '>' . $year . '</option>';
        }
        echo '</select>';
    }

    protected function getWidgetContent($selectedYear)
    { // Define an array to store data for each month
        $dataByMonth = array_fill(1, 12, ['ProfilesCreated' => 0, 'ProfilesTerminated' => 0]);

        $results = $this->getData($selectedYear);

        // Fill in the data for each month
        foreach ($results as $result) {
            $dataByMonth[$result->Month]['ProfilesCreated'] = $result->ProfilesCreated;
            $dataByMonth[$result->Month]['ProfilesTerminated'] = $result->ProfilesTerminated;
        }

        // Prepare the chart data as an associative array
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Profiles Created',
                    'data' => [],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Profiles Terminated',
                    'data' => [],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];

        // Populate chartData with data from $dataByMonth
        foreach ($dataByMonth as $month => $data) {
            $chartData['labels'][] = date('F', mktime(0, 0, 0, $month, 1, 1));
            $chartData['datasets'][0]['data'][] = $data['ProfilesCreated'];
            $chartData['datasets'][1]['data'][] = $data['ProfilesTerminated'];
        }

        // Encode the chart data as a JSON string
        $chartDataJson = json_encode($chartData);

        // Prepare the HTML representation of the chart container with data and script
        $htmlChartContainer = '
    <div id="myChartContainer">
        <canvas id="myBarChart"></canvas>
    </div>
    <script>
        var chartData = ' . $chartDataJson . ';
        var ctx = document.getElementById("myBarChart").getContext("2d");
        var myBarChart = new Chart(ctx, {
            type: "bar",
            data: chartData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    </script>
';

        // Create an array object to store the HTML content and widget ID
        $responseArray = array(
            'widget_id' => $this->id_base,
            'html_content' => $htmlChartContainer
        );

        // Output the array as JSON
        echo json_encode($responseArray);
        exit; // Terminate the script


    }



    protected function getData($selectedYear)
    {
        global $wpdb;
        // Fetch data for the selected year and return it as JSON
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                MONTH(date) AS Month,
                SUM(CASE WHEN action = 'created' THEN 1 ELSE 0 END) AS ProfilesCreated,
                SUM(CASE WHEN action = 'terminated' THEN 1 ELSE 0 END) AS ProfilesTerminated
            FROM (
                SELECT user_registered AS date, 'created' AS action
                FROM {$wpdb->prefix}users
                UNION ALL
                SELECT last_updated AS date, 'terminated' AS action
                FROM {$wpdb->prefix}bp_suspend
                WHERE item_type = 'user' 
                AND user_suspended = 1 
                AND YEAR(last_updated) = %d
            ) AS combined_data
            WHERE YEAR(date) = %d
            GROUP BY Month",
                $selectedYear,
                $selectedYear
            )
        );

        return $results;
    }


    protected function getTableData($selectedYear)
    {
        global $wpdb;

        // Fetch data for the selected year
        $currentYearResults = $this->getData($selectedYear);

        // Fetch data for the previous year
        $previousYearResults = $this->getData($selectedYear - 1);

        // Define an empty HTML table
        $tableHtml = '<table class="display compact">';
        $tableHtml .= '<thead><tr><th>Month</th><th>Profiles Created</th><th>Profiles Terminated</th></tr></thead>';
        $tableHtml .= '<tbody>';

        // Create an array to store the results by month
        $currentYearResultsByMonth = [];
        $previousYearResultsByMonth = [];

        // Initialize the results arrays with zeros for all months
        for ($month = 1; $month <= 12; $month++) {
            $currentYearResultsByMonth[$month] = ['ProfilesCreated' => 0, 'ProfilesTerminated' => 0];
            $previousYearResultsByMonth[$month] = ['ProfilesCreated' => 0, 'ProfilesTerminated' => 0];
        }

        // Fill in the data for the selected year
        foreach ($currentYearResults as $result) {
            $currentYearResultsByMonth[$result->Month] = [
                'ProfilesCreated' => $result->ProfilesCreated,
                'ProfilesTerminated' => $result->ProfilesTerminated,
            ];
        }

        // Fill in the data for the previous year
        foreach ($previousYearResults as $result) {
            $previousYearResultsByMonth[$result->Month] = [
                'ProfilesCreated' => $result->ProfilesCreated,
                'ProfilesTerminated' => $result->ProfilesTerminated,
            ];
        }

        // Initialize variables for total profiles created and terminated for both years
        $currentYearTotalCreated = 0;
        $currentYearTotalTerminated = 0;
        $previousYearTotalCreated = 0;
        $previousYearTotalTerminated = 0;

        // Loop through all months and add rows to the table
        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1, 1));
            $currentYearProfilesCreated = $currentYearResultsByMonth[$month]['ProfilesCreated'];
            $currentYearProfilesTerminated = $currentYearResultsByMonth[$month]['ProfilesTerminated'];
            $previousYearProfilesCreated = $previousYearResultsByMonth[$month]['ProfilesCreated'];
            $previousYearProfilesTerminated = $previousYearResultsByMonth[$month]['ProfilesTerminated'];

            // Update the total profiles created and terminated for both years
            $currentYearTotalCreated += $currentYearProfilesCreated;
            $currentYearTotalTerminated += $currentYearProfilesTerminated;
            $previousYearTotalCreated += $previousYearProfilesCreated;
            $previousYearTotalTerminated += $previousYearProfilesTerminated;

            // Calculate the annual growth in percentage compared to the previous year
            $currentYearGrowth = ($currentYearProfilesCreated - $currentYearProfilesTerminated);
            $previousYearGrowth = ($previousYearProfilesCreated - $previousYearProfilesTerminated);
            $annualGrowthPercentage = ($currentYearGrowth - $previousYearGrowth) / ($previousYearGrowth != 0 ? $previousYearGrowth : 1) * 100;

            // Create a row in the table for each month
            $tableHtml .= '<tr>';
            $tableHtml .= '<td>' . esc_html($monthName) . '</td>';
            $tableHtml .= '<td>' . esc_html($currentYearProfilesCreated) . '</td>';
            $tableHtml .= '<td>' . esc_html($currentYearProfilesTerminated) . '</td>';
            $tableHtml .= '</tr>';
        }

        // Calculate annual growth in percentage compared to the previous year
        $annualGrowthPercentage = ($currentYearTotalCreated - $currentYearTotalTerminated - $previousYearTotalCreated + $previousYearTotalTerminated) / ($previousYearTotalCreated - $previousYearTotalTerminated + 1) * 100;

        // Close the table body
        $tableHtml .= '</tbody>';

        // Add the totals for the current year and previous year to the tfoot section
        $tableHtml .= '<tfoot><tr><td>Total (' . $selectedYear . ')</td>';
        $tableHtml .= '<td>' . esc_html($currentYearTotalCreated) . '</td>';
        $tableHtml .= '<td>' . esc_html($currentYearTotalTerminated) . '</td></tr>';
        $tableHtml .= '<tr><td>Total (' . ($selectedYear - 1) . ')</td>';
        $tableHtml .= '<td>' . esc_html($previousYearTotalCreated) . '</td>';
        $tableHtml .= '<td>' . esc_html($previousYearTotalTerminated) . '</td></tr>';
        $tableHtml .= '<tr><td>Annual Growth (%)</td>';
        $tableHtml .= '<td colspan="2">' . esc_html($annualGrowthPercentage) . '%</td></tr></tfoot></table>';

        return $tableHtml;
    }




}