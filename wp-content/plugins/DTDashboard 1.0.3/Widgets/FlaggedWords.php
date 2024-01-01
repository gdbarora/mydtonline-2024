<?php
class Flagged_Words extends Base_DT_Widget
{
    public function __construct()
    {
        parent::__construct(
            'flagged-words',
            'Flagged Words',
            'Display FlaggedWords Stats Stats'
        );
    }
    protected function initial_widget_content()
    {
    }

    protected function getWidgetContent()
    {
        // Calculate the top keywords for both month and week
        $top_keywords = $this->calculateTopKeywords();

        ob_start(); // Start output buffering

        echo '<h3>Top 5 Keywords This Month</h3><ol>';

        // Display only the top 5 keywords for the month and their counts
        $count = 0;
        foreach ($top_keywords['month'] as $keyword => $kw_count) {
            if ($count >= 5 || $kw_count === 0) {
                break; // Exit the loop after displaying the top 5 keywords
            }
            echo "<li>$keyword ($kw_count)</li>";
            $count++;
        }

        echo '</ol>';

        echo '<h3>Top 5 Keywords This Week</h3><ol>';

        // Display only the top 5 keywords for the week and their counts
        $count = 0;
        foreach ($top_keywords['week'] as $keyword => $kw_count) {
            if ($count >= 5 || $kw_count === 0) {
                break; // Exit the loop after displaying the top 5 keywords
            }
            echo "<li>$keyword ($kw_count)</li>";
            $count++;
        }

        echo '</ol>';

        // Get the buffered HTML content
        $html_content = ob_get_clean();

        // Prepare data in the requested format
        $response = [
            'widget_id' => $this->id_base,
            'html_content' => $html_content,
            // Include HTML content in the same key
        ];

        echo json_encode($response);
    }

    protected function calculateTopKeywords()
    {
        $keywords = $this->get_wbbprof_settings();

        // Create an array to store keyword counts for both month and week
        $top_keywords = [
            'month' => [],
            'week' => [],
        ];

        // Calculate and store the counts for each keyword for both month and week
        foreach ($keywords as $keyword) {
            $month_count = $this->count_keyword_occurrences($keyword, 'month');
            $week_count = $this->count_keyword_occurrences($keyword, 'week');

            // Store counts for both month and week
            $top_keywords['month'][$keyword] = $month_count;
            $top_keywords['week'][$keyword] = $week_count;
        }

        // Sort the keywords by count in descending order for both month and week
        arsort($top_keywords['month']);
        arsort($top_keywords['week']);

        return $top_keywords;
    }



    // Helper function to process flagged words data
    protected function processFlaggedWordsData($data)
    {
        $keywords = $this->get_wbbprof_settings();
        $flaggedWordsData = [];

        foreach ($keywords as $keyword) {
            $occurrencesThisMonth = $this->count_keyword_occurrences($keyword, 'month', $data);
            $occurrencesThisYear = $this->count_keyword_occurrences($keyword, 'year', $data);

            // Add keyword and occurrences to the result array
            $flaggedWordsData[] = [
                'keyword' => esc_html($keyword),
                'occurrences_this_month' => $occurrencesThisMonth,
                'occurrences_this_year' => $occurrencesThisYear,
            ];
        }

        // Sort the flagged words by occurrences for this month
        usort($flaggedWordsData, function ($a, $b) {
            return $b['occurrences_this_month'] - $a['occurrences_this_month'];
        });

        // Extract the top flagged words for this month
        $topFlaggedWordsThisMonth = array_slice($flaggedWordsData, 0, 5);

        // Sort the flagged words by occurrences for this year
        usort($flaggedWordsData, function ($a, $b) {
            return $b['occurrences_this_year'] - $a['occurrences_this_year'];
        });

        // Extract the top flagged words for this year
        $topFlaggedWordsThisYear = array_slice($flaggedWordsData, 0, 5);

        // Generate HTML for the top flagged words
        $htmlContent = $this->generateFlaggedWordsHtml($topFlaggedWordsThisMonth, 'Top Flagged Words This Month');
        $htmlContent .= $this->generateFlaggedWordsHtml($topFlaggedWordsThisYear, 'Top Flagged Words This Year');

        return $htmlContent;
    }

    // Helper function to generate HTML for flagged words
    protected function generateFlaggedWordsHtml($flaggedWords, $title)
    {
        $html = '<h3>' . esc_html($title) . '</h3>';
        $html .= '<table >';
        $html .= '<thead><tr><th>Flagged Word</th><th>Occurrences This Month</th><th>Occurrences This Year</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($flaggedWords as $flaggedWord) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($flaggedWord['keyword']) . '</td>';
            $html .= '<td>' . esc_html($flaggedWord['occurrences_this_month']) . '</td>';
            $html .= '<td>' . esc_html($flaggedWord['occurrences_this_year']) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }


    // Helper function to generate HTML for top posts
    protected function getTableData()
    {
        $keywords = $this->get_wbbprof_settings();
        $flaggedTableHtml = '<table class="display compact">';
        $flaggedTableHtml .= '<thead><tr><th>Flagged Word</th><th>Post Link</th><th>Author</th><th>Location</th><th>Post Date</th></tr></thead>';
        $flaggedTableHtml .= '<tbody>';

        foreach ($keywords as $keyword) {
            $flaggedActivities = $this->search_activity_by_keywords($keyword);
            $flaggedMsgs = $this->search_messages_by_keyword($keyword);

            // Process flagged activities
            foreach ($flaggedActivities as $fActivity) {
                $flaggedTableHtml .= '<tr>';
                $flaggedTableHtml .= '<td>' . esc_html($keyword) . '</td>';
                $flaggedTableHtml .= '<td><a href="' . esc_url(bp_activity_get_permalink($fActivity->post_id)) . '">' . esc_html($fActivity->post_id) . '</a></td>';
                $flaggedTableHtml .= '<td>' . esc_html($fActivity->author) . '</td>';
                $flaggedTableHtml .= '<td>' . esc_html($fActivity->location) . '</td>';
                $flaggedTableHtml .= '<td>' . esc_html($fActivity->post_date) . '</td>';
                $flaggedTableHtml .= '</tr>';
            }

            // Process flagged messages
            foreach ($flaggedMsgs as $fMessage) {
                $flaggedTableHtml .= '<tr>';
                $flaggedTableHtml .= '<td>' . esc_html($keyword) . '</td>';
                $flaggedTableHtml .= '<td>' . esc_html($fMessage->post_id) . '</td>'; // No post link for messages
                $flaggedTableHtml .= '<td>' . esc_html($fMessage->author) . '</td>'; // Assuming you're using 'display_name' for sender's name
                $flaggedTableHtml .= '<td>' . esc_html($fMessage->recipient_name) . '</td>'; // Assuming all flagged messages are from chats
                $flaggedTableHtml .= '<td>' . esc_html($fMessage->post_date) . '</td>';
                $flaggedTableHtml .= '</tr>';
            }
        }

        $flaggedTableHtml .= '</tbody>';
        $flaggedTableHtml .= '</table>';

        return $flaggedTableHtml;
    }


    protected function get_wbbprof_settings()
    {
        global $wpdb;

        $option_name = 'wbbprof_settings';

        // Build and execute the SQL query
        $query = $wpdb->prepare("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s", $option_name);
        $result = $wpdb->get_var($query);
        $wbbprof_settings = unserialize($result);
        $keywordString = $wbbprof_settings['keywords'];
        $keywords = explode(',', $keywordString);

        return $keywords;
    }

    protected function search_activity_by_keywords($keyword)
    {
        global $wpdb;

        // Sanitize and prepare the keyword
        $keyword = sanitize_text_field($keyword);

        // Build and execute the SQL query
        $query = $wpdb->prepare(
            "SELECT %s AS keyword, a.id AS post_id, DATE(a.date_recorded) AS post_date, u.display_name AS author,
            CASE
                WHEN a.component = 'activity' THEN 'Community'
                WHEN a.component = 'groups' THEN (SELECT name FROM {$wpdb->prefix}bp_groups WHERE id = a.item_id)
                ELSE ''
            END as location
            FROM {$wpdb->prefix}bp_activity AS a
            LEFT JOIN {$wpdb->prefix}users AS u ON a.user_id = u.id
            WHERE content LIKE %s",
            $keyword,
            '%' . $wpdb->esc_like($keyword) . '%'
        );

        $results = $wpdb->get_results($query);

        return $results;
    }

    protected function search_messages_by_keyword($keyword)
    {
        global $wpdb;

        // Sanitize and prepare the keyword
        $keyword = sanitize_text_field($keyword);

        // Build and execute the SQL query
        $query = $wpdb->prepare(
            "
    SELECT %s as keyword, m.thread_id AS post_id, m.message, DATE(m.date_sent) AS post_date, u.display_name AS author, ur.display_name as recipient_name, m.subject
    FROM {$wpdb->prefix}bp_messages_messages AS m
    LEFT JOIN {$wpdb->prefix}users AS u ON m.sender_id = u.ID
    LEFT JOIN {$wpdb->prefix}bp_messages_recipients AS r ON m.thread_id = r.thread_id AND r.user_id != m.sender_id
    LEFT JOIN {$wpdb->prefix}users AS ur ON r.user_id = ur.ID
    WHERE m.message LIKE %s",
            $keyword,
            '%' . $wpdb->esc_like($keyword) . '%'
        );

        $results = $wpdb->get_results($query);

        return $results;
    }

    protected function count_keyword_occurrences($keyword, $time_period = 'month')
    {
        global $wpdb;

        // Sanitize and prepare the keyword
        $keyword = sanitize_text_field($keyword);

        // Determine the interval based on the time period
        if ($time_period === 'week') {
            $interval = "INTERVAL 7 DAY";
        } else {
            $interval = "INTERVAL 30 DAY";
        }

        // Build and execute the SQL queries with date conditions using the determined interval
        $activity_query = $wpdb->prepare(
            "SELECT COUNT(a.id)
            FROM {$wpdb->prefix}bp_activity AS a
            WHERE content LIKE %s
            AND a.date_recorded >= DATE_SUB(NOW(), $interval)",
            '%' . $wpdb->esc_like($keyword) . '%'
        );

        $messages_query = $wpdb->prepare(
            "SELECT COUNT(m.thread_id)
            FROM {$wpdb->prefix}bp_messages_messages AS m
            WHERE m.message LIKE %s
            AND m.date_sent >= DATE_SUB(NOW(), $interval)",
            '%' . $wpdb->esc_like($keyword) . '%'
        );

        // Get counts from both queries and sum them
        $activity_count = intval($wpdb->get_var($activity_query));
        $messages_count = intval($wpdb->get_var($messages_query));

        return $activity_count + $messages_count;
    }


}