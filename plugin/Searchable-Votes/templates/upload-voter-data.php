<?php
function clean_csv_data($csv_data) {
    // Create a temporary file to handle CSV parsing
    $temp_input = tmpfile();
    $temp_output = tmpfile();

    // Write the input CSV data to the temporary input file
    fwrite($temp_input, $csv_data);
    rewind($temp_input);

    $cleaned_data = '';

    // Open the input and output streams
    while (($row = fgetcsv($temp_input)) !== false) {
        // Remove commas from each field
        $cleaned_row = array_map(function ($field) {
            return str_replace(',', '', $field);
        }, $row);

        // Write the cleaned row to the output stream
        fputcsv($temp_output, $cleaned_row);
    }

    rewind($temp_output);

    // Read the cleaned data from the output stream
    while (($line = fgets($temp_output)) !== false) {
        $cleaned_data .= $line;
    }

    // Close the temporary files
    fclose($temp_input);
    fclose($temp_output);

    return $cleaned_data;
}

function render_csv_import_page() {
    global $wpdb;

    // Handle form submission
    if (isset($_POST['csv_data']) && isset($_POST['table_name'])) {
        $csv_data = sanitize_textarea_field($_POST['csv_data']);
        $table_name = sanitize_text_field($_POST['table_name']);
        $use_auto_increment = isset($_POST['use_auto_increment']); // Check if the checkbox is checked

        // Clean the CSV data
        $csv_data = clean_csv_data($csv_data);

        $rows = explode("\n", $csv_data);

        if (empty($table_name)) {
            echo '<div class="notice notice-error"><p>Table name cannot be empty.</p></div>';
            return;
        }

        if (count($rows) < 2) {
            echo '<div class="notice notice-error"><p>Invalid CSV data. Please provide at least one header row and one data row.</p></div>';
            return;
        }

        $table_name = $wpdb->prefix . $table_name;

        // Drop the table if it already exists
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Extract column names from the first row
        $columns = str_getcsv(array_shift($rows));
        $columns = array_map(function ($column) {
            return sanitize_text_field($column); 
        }, $columns);

        // Create the table dynamically
        $charset_collate = $wpdb->get_charset_collate();
        $columns_sql = array_map(function ($column) {
            return "`$column` TEXT";
        }, $columns);

        if ($use_auto_increment) {
            // Use an auto-increment primary key
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                " . implode(", ", $columns_sql) . "
            ) $charset_collate;";
        } else {
            // Use the first column as the primary key
            $primary_key = array_shift($columns); // Use the first column as the primary key
            $columns_sql = array_slice($columns_sql, 1); // Skip the first entry in columns_sql
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                `$primary_key` VARCHAR(255) NOT NULL PRIMARY KEY,
                " . implode(", ", $columns_sql) . "
            ) $charset_collate;";
        }

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Insert data into the table
        foreach ($rows as $row) {
            $values = str_getcsv($row);

            // Ensure the row has the same number of columns as the header
            if (count($values) !== count($columns) + ($use_auto_increment ? 0 : 1)) { // Adjust for primary key
                continue; // Skip rows with mismatched column counts
            }

            $data = $use_auto_increment
                ? array_combine($columns, array_map('sanitize_text_field', $values))
                : array_combine(array_merge([$primary_key], $columns), array_map('sanitize_text_field', $values));
            $wpdb->insert($table_name, $data);
        }

        echo '<div class="notice notice-success"><p>CSV data imported successfully into table: ' . esc_html($table_name) . '.</p></div>';
    }

    // Render the form
    ?>
    <h1>Import CSV Data</h1>
    <form method="post">
        <label for="table_name">Table Name:</label>
        <input type="text" name="table_name" id="table_name" placeholder="Enter table name" required>
        <br><br>
        <textarea name="csv_data" rows="10" cols="50" placeholder="Paste CSV data here" required></textarea>
        <br><br>
        <label>
            <input type="checkbox" name="use_auto_increment" id="use_auto_increment">
            Use Auto-Increment Primary Key
        </label>
        <br><br>
        <button type="submit" class="button button-primary">Import</button>
    </form>
    <?php
}

render_csv_import_page();