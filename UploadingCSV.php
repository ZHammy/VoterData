function add_csv_import_page() {
    add_menu_page(
        'CSV Import', // Page title
        'CSV Import', // Menu title
        'edit_posts', // Capability
        'csv-import', // Menu slug
        'render_csv_import_page', // Callback function
        'dashicons-upload', // Icon
        20 // Position
    );
}
add_action('admin_menu', 'add_csv_import_page');

function render_csv_import_page() {
    global $wpdb;

    // Handle form submission
    if (isset($_POST['csv_data'])) {
        $csv_data = sanitize_textarea_field($_POST['csv_data']);
        $rows = explode("\n", $csv_data);
        $table_name = $wpdb->prefix . 'CanidateVotingHistory';

        // Create the table if it doesn't exist
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            candidate_name VARCHAR(255) NOT NULL,
            votes INT NOT NULL,
            election_date DATE NOT NULL
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Parse and insert CSV data
        foreach ($rows as $row) {
            $columns = str_getcsv($row);
            if (count($columns) < 3) {
                continue; // Skip invalid rows
            }

            $candidate_name = sanitize_text_field($columns[0]);
            $votes = intval($columns[1]);
            $election_date = sanitize_text_field($columns[2]);

            $wpdb->insert(
                $table_name,
                [
                    'candidate_name' => $candidate_name,
                    'votes' => $votes,
                    'election_date' => $election_date,
                ],
                [
                    '%s', // candidate_name
                    '%d', // votes
                    '%s', // election_date
                ]
            );
        }

        echo '<div class="notice notice-success"><p>CSV data imported successfully!</p></div>';
    }

    // Render the form
    ?>
    <div class="wrap">
        <h1>Import CSV Data</h1>
        <form method="post">
            <textarea name="csv_data" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
            <br>
            <button type="submit" class="button button-primary">Import</button>
        </form>
    </div>
    <?php
}

