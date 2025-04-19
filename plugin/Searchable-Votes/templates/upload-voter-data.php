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

function sv_create_table($table_name, $csv_data, $use_auto_increment = false) {
    global $wpdb;
    $table_name = $wpdb->prefix . $table_name;
    $rows = explode("\n", $csv_data);

    // Check if there are rows to process
    if (count($rows) === 0 || (count($rows) === 1 && trim($rows[0]) === '')) {
        echo '<div class="notice notice-error"><p>No data found for ' . esc_html($table_name) . '. Table creation skipped.</p></div>';
        return;
    }
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

function render_csv_import_page() {
    // Restrict access to administrators only
    if ( ! current_user_can( 'manage_options' ) ) {
        echo '<div class="notice notice-error"><p>You do not have permission to access this page.</p></div>';
        return;
    }
    global $wpdb;
    // Handle form submission
    if (isset($_POST['csv_data_bills'])) {
        $bills= sanitize_textarea_field($_POST['csv_data_bills']);
        $bills = clean_csv_data($bills);
        sv_create_table('Bills', $bills, false); 
    }
    if (isset($_POST['csv_data_candidates'])) {
        $candidates = sanitize_textarea_field($_POST['csv_data_candidates']);
        $candidates = clean_csv_data($candidates);
        sv_create_table('Candidates', $candidates, false); 
    }
    if (isset($_POST['csv_data_votes'])) {
        $votes = sanitize_textarea_field($_POST['csv_data_votes']);
        $votes = clean_csv_data($votes);
        sv_create_table('Votes', $votes, true); 
    }


    ?>
    <h1>Import CSV Data</h1>
    <h3>Make sure that none of your fields have commas in them.  This is a very lazy implementation and commas in any text field will break the import for that row.</h3>
    <h3>Make sure to paste the raw CSV file.  You can do this by opening in notepad or some such text editor and copying from there rather than excel.  It will use the comma seperators for fields.  </h3>
    <form method="post">
        <div>
            <label for="table_name_Bills">Table Name: Bills</label>
            <br><br>
            <textarea name="csv_data_bills" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
        </div>
        <div>
            <label for="table_name_Bills">Table Name: Candidates</label>
            <br><br>
            <textarea name="csv_data_candidates" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
        </div>
        <div>
            <label for="table_name_Bills">Table Name: Votes</label>
            <br><br>
            <textarea name="csv_data_votes" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
        </div>
        <button type="submit" class="button button-primary">Import</button>
    </form>
    <?php
}

render_csv_import_page();