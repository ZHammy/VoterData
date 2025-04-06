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

// Get unique precincts for the filter dropdown
$precincts = array_unique(array_map(function($precinct) {
    return ltrim($precinct, '0'); // Trim leading zeros
}, array_column($candidates, 'Precinct')));
sort($precincts); // Sort precincts


    ob_start();
    ?>
    <div class="table-container">
        <input type="text" id="search-input" class="search-input" placeholder="Search by Candidate Name..." onkeyup="filterTable()">
        <select id="precinct-filter" class="filter-select" onchange="filterTable()">
            <option value="">All Precincts</option>
            <?php foreach ($precincts as $precinct): ?>
                <option value="<?php echo esc_attr($precinct); ?>"><?php echo esc_html($precinct); ?></option>
            <?php endforeach; ?>
        </select>
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
                        <td><?php echo esc_html(ltrim($candidate['Precinct'], '0')); ?></td>
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
    </div>
    <script>
        function filterTable() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const precinctFilter = document.getElementById('precinct-filter').value.toLowerCase();
            const table = document.getElementById('searchable-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const candidateName = cells[0].textContent.toLowerCase();
                const candidatePrecinct = cells[1].textContent.toLowerCase();

                const matchesSearch = candidateName.includes(searchInput);
                const matchesPrecinct = precinctFilter === '' || candidatePrecinct === precinctFilter;

                rows[i].style.display = matchesSearch && matchesPrecinct ? '' : 'none';
            }
        }
    </script>
    <?php
    return ob_get_clean();
}
// Call the function to render the table
echo render_searchable_table();
?>

<?php get_footer(); ?>