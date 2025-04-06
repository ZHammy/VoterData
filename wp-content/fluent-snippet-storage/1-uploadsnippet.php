<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: UploadSnippet
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-06 00:03:51
* @is_valid: 
* @updated_by: 
* @priority: 10
* @run_at: all
* @load_as_file: 
* @condition: {"status":"yes","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
<?php
add_action('init', function () {
    // Only run this once to prevent duplicate inserts
    if (!isset($_GET['import_csv']) || $_GET['import_csv'] !== '1') return;

    global $wpdb;

    // CHANGE THIS URL to your uploaded CSV
    $csv_url = 'https://yourdomain.com/wp-content/uploads/2025/04/yourfile.csv';

    // Fetch and open the CSV
    $csv = file_get_contents($csv_url);
    $lines = array_map('str_getcsv', explode("\n", $csv));
    $header = array_map('trim', array_shift($lines)); // Remove and parse header row

    // OPTIONAL: Create a custom table name
    $table_name = $wpdb->prefix . 'my_csv_data';

    // Create table if not exists (very basic schema)
    $columns_sql = implode(', ', array_map(function ($col) {
        return "`" . sanitize_key($col) . "` TEXT";
    }, $header));

    $wpdb->query("CREATE TABLE IF NOT EXISTS `$table_name` (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        $columns_sql
    ) DEFAULT CHARSET=utf8mb4;");

    // Insert rows
    foreach ($lines as $row) {
        if (count($row) !== count($header)) continue;
        $data = array_combine($header, $row);
        $wpdb->insert($table_name, $data);
    }

    echo "CSV imported into $table_name!";
    exit;
});