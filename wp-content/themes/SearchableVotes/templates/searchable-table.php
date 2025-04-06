<?php
/**
 * Template for displaying a searchable and filterable table of bills, candidates, and votes.
 */

get_header(); ?>

<?php
function render_searchable_table() {
    global $wpdb;

    // Fetch data from the database
    $candidates = $wpdb->get_results("SELECT * FROM wp_Candidates", ARRAY_A);
    $bills = $wpdb->get_results("SELECT * FROM wp_Bills", ARRAY_A);
    $votes = $wpdb->get_results("SELECT * FROM wp_Votes", ARRAY_A);

    // Prepare data for rendering
    $bills_map = [];
    foreach ($bills as $bill) {
        $bills_map[$bill['BillID']] = $bill['Bill Name'];
    }

    $votes_map = [];
    foreach ($votes as $vote) {
        $votes_map[$vote['CandidateID']][$vote['BillID']] = $vote['Vote'];
    }

    ob_start();
    ?>
    <table id="searchable-table">
        <thead>
            <tr>
                <th>Candidate Name</th>
                <th>Candidate Precinct</th>
                <?php foreach ($bills as $bill): ?>
                    <th><?php echo esc_html($bill['Bill Name']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate): ?>
                <tr>
                    <td><?php echo esc_html($candidate['Name']); ?></td>
                    <td><?php echo esc_html($candidate['Precinct']); ?></td>
                    <?php foreach ($bills as $bill): ?>
                        <td>
                            <?php
                            $vote = $votes_map[$candidate['Candidate ID']][$bill['BillID']] ?? 'No Vote';
                            echo esc_html($vote);
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}
// Call the function to render the table
echo render_searchable_table();
?>

<?php get_footer(); ?>