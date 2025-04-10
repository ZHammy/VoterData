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
    $bills = $wpdb->get_results("SELECT * FROM wp_Bills ORDER BY BillID DESC", ARRAY_A);
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
<div class="page-container">
    <input type="text" id="search-input" class="search-input" placeholder="Search by Candidate Name..." onkeyup="filterTable()">
    <div class="filter-container">
    <select id="precinct-filter" class="filter-select" onchange="filterTable()">
        <option value="">All Precincts</option>
        <?php foreach ($precincts as $precinct): ?>
            <option value="<?php echo esc_attr($precinct); ?>"><?php echo esc_html($precinct); ?></option>
        <?php endforeach; ?>
    </select>
    <div class="custom-multi-select" id="bills-filter-container">
        <div class="select-trigger" onclick="toggleDropdown('bills-filter-dropdown')">
            <span id="bills-filter-placeholder">Select bills to filter</span>
            <span class="arrow">&#9662;</span>
        </div>
        <div class="dropdown" id="bills-filter-dropdown">
            <?php foreach ($bills as $bill): ?>
                <label class="dropdown-item">
                    <input type="checkbox" value="<?php echo esc_attr($bill['BillID']); ?>" onchange="filterTable()">
                    <?php echo esc_html($bill['Bill Name']); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="hide-inactive-filter">
        <label>
            <input type="checkbox" id="hide-inactive-filter" checked onchange="filterTable()"> Hide Inactive Town Meeting Members
        </label>
    </div>
</div>
<div class="table-container">
    <table id="searchable-table">
        <thead>
            <tr>
                <th class="sticky-column">Candidate Name</th>
                <th class="sticky-column">Candidate Precinct</th>
                <?php foreach ($bills as $bill): ?>
                    <th data-bill-id="<?php echo esc_attr($bill['BillID']); ?>"><?php echo esc_html($bill['Bill Name']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($candidates as $candidate): ?>
            <tr data-active-status="<?php echo esc_attr($candidate['Active?']); ?>">
                <td class="sticky-column"><?php echo esc_html($candidate['Name']); ?></td>
                <td class="sticky-column"><?php echo esc_html(ltrim($candidate['Precinct'], '0')); ?></td>
                <?php foreach ($bills as $bill): ?>
                    <td data-bill-id="<?php echo esc_attr($bill['BillID']); ?>" 
                    <?php
                    $vote = $votes_map[$candidate['Candidate ID']][$bill['BillID']] ?? '';
                    ?>
                        style="
                            <?php 
                            if ($vote === 'ABSTAIN') {
                                echo 'background-color: #C0C0C0; color: black;';
                            } elseif (in_array($vote, ['Absent', '' ])) {
                                echo 'background-color: white;';
                            } elseif ($vote === $bill['Bill Endorsement']) {
                                echo 'background-color: green; color: white;';
                            } else {
                                echo 'background-color: red; color: white;';
                            }
                            ?>
                        ">
                        <?php
                        echo esc_html($vote);
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>
    <script>
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function filterTable() {
        const searchInput = document.getElementById('search-input').value.toLowerCase();
        const precinctFilter = document.getElementById('precinct-filter').value.toLowerCase();
        const billsFilter = Array.from(document.querySelectorAll('#bills-filter-dropdown input:checked')).map(input => input.value);
        const hideInactive = document.getElementById('hide-inactive-filter').checked;
        const table = document.getElementById('searchable-table');
        const rows = table.getElementsByTagName('tr');
        const billHeaders = table.querySelectorAll('thead th[data-bill-id]');
        const billColumns = Array.from(billHeaders).map((header, index) => ({
            billID: header.getAttribute('data-bill-id'),
            index: index + 2 // Adjust for Candidate Name and Precinct columns
        }));

        // Update placeholder text
        const placeholder = document.getElementById('bills-filter-placeholder');
        placeholder.textContent = billsFilter.length > 0 ? `${billsFilter.length} bill(s) selected` : 'Select bills to filter';

        // Show/hide columns based on selected bills
        billHeaders.forEach(header => {
            const billID = header.getAttribute('data-bill-id');
            header.style.display = billsFilter.length === 0 || billsFilter.includes(billID) ? '' : 'none';
        });

        Array.from(rows).forEach((row, rowIndex) => {
            if (rowIndex === 0) return; // Skip header row
            const cells = row.getElementsByTagName('td');
            const candidateName = cells[0].textContent.toLowerCase();
            const candidatePrecinct = cells[1].textContent.toLowerCase();
            const isActive = row.getAttribute('data-active-status') === 'Active';

            // Show/hide cells based on selected bills
            billColumns.forEach(column => {
                cells[column.index].style.display = billsFilter.length === 0 || billsFilter.includes(column.billID) ? '' : 'none';
            });

            const matchesSearch = candidateName.includes(searchInput);
            const matchesPrecinct = precinctFilter === '' || candidatePrecinct === precinctFilter;
            const matchesActive = !hideInactive || isActive;

            row.style.display = matchesSearch && matchesPrecinct && matchesActive ? '' : 'none';
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('bills-filter-dropdown');
        const trigger = document.querySelector('.select-trigger');
        if (!dropdown.contains(event.target) && !trigger.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Run filterTable on page load
    document.addEventListener('DOMContentLoaded', function () {
        filterTable();
    });
</script>
    <?php
    return ob_get_clean();
}
// Call the function to render the table
echo render_searchable_table();
?>

<?php get_footer(); ?>