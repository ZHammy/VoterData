
<?php
function render_searchable_table() {
    global $wpdb;

    // Fetch data from the database
    $candidates = $wpdb->get_results("
        SELECT * 
        FROM wp_Candidates 
        ORDER BY 
            CASE 
                WHEN Precinct REGEXP '^[0-9]+$' THEN 0 -- Numeric precincts first
                ELSE 1 -- Non-numeric precincts last
            END, 
            CAST(Precinct AS UNSIGNED) ASC, -- Sort numeric precincts numerically
            Name ASC -- Sort alphabetically by name
    ", ARRAY_A);
    $bills = $wpdb->get_results("SELECT * FROM wp_Bills WHERE Priority = 'X' ORDER BY CAST(BillID AS UNSIGNED) DESC", ARRAY_A);
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
                    <?php echo esc_attr(preg_replace('/\\\\(.)/', '$1', esc_html($bill['Session'] . ': ' . $bill['Short Description']))); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="hide-inactive-filter">
        <label>
            <input type="checkbox" id="hide-inactive-filter" checked onchange="filterTable()"> Hide Inactive Town Meeting Members
        </label>
    </div>
<!-- Update the color legend container -->
<div class="color-legend" style="display: flex; flex-wrap: wrap; justify-content: flex-start; margin-top: 20px; width: 100%; margin-bottom: 20px;">
    <strong style="width: 100%; margin-bottom: 10px;">Color Legend:</strong>
    <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 20px;">
        <li style="display: flex; align-items: center;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: green; margin-right: 10px; border: 1px solid #000;"></span>
            Voted in B4E endorsed way
        </li>
        <li style="display: flex; align-items: center;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: red; margin-right: 10px; border: 1px solid #000;"></span>
            Voted against B4E endorsement
        </li>
        <li style="display: flex; align-items: center;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: white; margin-right: 10px; border: 1px solid #ddd;"></span>
            Absent
        </li>
        <li style="display: flex; align-items: center;">
            <span style="display: inline-block; width: 20px; height: 20px; background-color: #C0C0C0; margin-right: 10px; border: 1px solid #000;"></span>
            Abstain
        </li>
    </ul>
</div>
    <div id="modal-overlay" class="modal-overlay">
    <div id="bill-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeBillModal()">&times;</span>
            <h2 id="modal-title"></h2>
            <p id="modal-description"></p>
        </div>
    </div>
</div>
</div>
<div class="table-container">
    <table id="searchable-table">
        <thead>
        <tr>
            <th class="sticky-column candidate-name">Candidate Name</th>
            <th class="sticky-column">Candidate Precinct</th>
            <?php foreach ($bills as $bill): ?>
                <th 
                    data-bill-id="<?php echo esc_attr($bill['BillID']); ?>" 
                    data-bill-description="<?php echo esc_attr(preg_replace('/\\\\(.)/', '$1', $bill['Bill Description'])); ?>" 
                    onclick="showBillModal(this)">
                    <a href="javascript:void(0);" style="text-decoration: underline; color: blue;">
                        <?php echo esc_attr(preg_replace('/\\\\(.)/', '$1', esc_html($bill['Session'] . ': ' . $bill['Short Description']))); ?>
                    </a>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
        <tbody>
        <?php foreach ($candidates as $candidate): ?>
            <tr data-active-status="<?php echo esc_attr($candidate['Active?']); ?>">
                <td class="sticky-column candidate-name"><?php echo esc_html($candidate['Name']); ?></td>
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

    function showBillModal(element) {
    const modal = document.getElementById('bill-modal');
    const overlay = document.getElementById('modal-overlay');
    const title = document.getElementById('modal-title');
    const description = document.getElementById('modal-description');

    // Get the bill name and description from the clicked element
    const billName = element.textContent;
    const billDescription = element.getAttribute('data-bill-description');

    // Populate the modal
    title.textContent = billName;
    description.textContent = billDescription;

    // Show the modal and overlay
    overlay.style.display = 'block';
    modal.style.display = 'block';
}

function closeBillModal() {
    const modal = document.getElementById('bill-modal');
    const overlay = document.getElementById('modal-overlay');

    // Hide the modal and overlay
    modal.style.display = 'none';
    overlay.style.display = 'none';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const overlay = document.getElementById('modal-overlay');
    if (event.target === overlay) {
        closeBillModal();
    }
};

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
