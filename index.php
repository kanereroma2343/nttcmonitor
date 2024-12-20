<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTTCMIS</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="header-container">
    <div class="header-top">
        <img src="./icons/tlogo.png" alt="Left Logo" class="logo">
        <h1 class="header-title">NATIONAL TVET TRAINER'S CERTIFICATE RECORDS</h1>
        <img src="./icons/blogo.png" alt="Right Logo" class="logo">
    </div>
    <div class="button-container">
        <button onclick="window.location.href='../dashboard.php'" class="action-button">Home</button>
        <button onclick="window.location.href='uploadxls.php'" class="action-button">Upload Registry</button>
        <button onclick="window.location.href='deletexls.php'" class="action-button">Delete Registry</button>
        <button class="action-button">Export Data</button>
        <button class="action-button">Settings</button>
    </div>
</div>

    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input 
            type="search" 
            id="searchInput" 
            placeholder="Search by name, qualification, or certificate number..."
            autocomplete="off"
        >
    </div>

    <div class="container">
        <?php
        // Pagination setup
        $entries_per_page = 200;
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $entries_per_page;
        
        // Read JSON data
        $jsonData = file_get_contents('https://raw.githubusercontent.com/kanereroma2343/nttcmonitor/refs/heads/main/data.json');
        $data = json_decode($jsonData, true);
        
        // Filter out empty rows and get total valid entries
        $valid_entries = array_filter(array_slice($data, 4), function($row) {
            return !empty($row['D']) || !empty($row['E']);
        });

        // Apply search filter if search term exists
        $search_term = isset($_GET['search']) ? strtolower($_GET['search']) : '';
        if (!empty($search_term)) {
            $valid_entries = array_filter($valid_entries, function($row) use ($search_term) {
                return strpos(strtolower($row['D'] ?? ''), $search_term) !== false || // Last Name
                       strpos(strtolower($row['E'] ?? ''), $search_term) !== false || // First Name
                       strpos(strtolower($row['F'] ?? ''), $search_term) !== false || // Middle Name
                       strpos(strtolower($row['S'] ?? ''), $search_term) !== false || // Qualification
                       strpos(strtolower($row['AD'] ?? ''), $search_term) !== false || // Certificate Number
                       strpos(strtolower($row['AG'] ?? ''), $search_term) !== false;   // Control Number
            });
        }
        
        $total_pages = ceil(count($valid_entries) / $entries_per_page);
        $entries_for_page = array_slice($valid_entries, $offset, $entries_per_page);
        ?>

        <table>
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Extension</th>
                    <th>Qualification</th>
                    <th>Certificate Number</th>
                    <th>Control Number</th>
                    <th>Date of Issuance</th>
                    <th>Validity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($entries_for_page as $row) {
                    echo "<tr>";
                    echo "<td data-label='Last Name'>" . htmlspecialchars($row['D'] ?? '') . "</td>";
                    echo "<td data-label='First Name'>" . htmlspecialchars($row['E'] ?? '') . "</td>";
                    echo "<td data-label='Middle Name'>" . htmlspecialchars($row['F'] ?? '') . "</td>";
                    echo "<td data-label='Extension'>" . htmlspecialchars($row['G'] ?? '') . "</td>";
                    echo "<td data-label='Qualification'>" . htmlspecialchars($row['S'] ?? '') . "</td>";
                    echo "<td data-label='Certificate Number'>" . htmlspecialchars($row['AD'] ?? '') . "</td>";
                    echo "<td data-label='Control Number'>" . htmlspecialchars($row['AG'] ?? '') . "</td>";
                    echo "<td data-label='Date of Issuance'>" . htmlspecialchars($row['AE'] ?? '') . "</td>";
                    echo "<td data-label='Validity'>" . htmlspecialchars($row['V'] ?? '') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Add pagination controls -->
        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="page-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Certificate Records Management System</p>
            <div class="footer-links">
                <a href="#"><i class="fas fa-home"></i> Home</a>
                <a href="#"><i class="fas fa-info-circle"></i> About</a>
                <a href="#"><i class="fas fa-envelope"></i> Contact</a>
            </div>
        </div>
    </footer>

    <script>
        // Updated search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            // Add small delay to prevent too many requests
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                let searchValue = this.value.trim();
                
                // Update URL with search parameter
                let url = new URL(window.location.href);
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                    url.searchParams.set('page', '1'); // Reset to first page on new search
                } else {
                    url.searchParams.delete('search');
                }
                
                window.location.href = url.toString();
            }, 500); // 500ms delay
        });

        // Set search input value from URL parameter
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search');
            if (searchTerm) {
                document.getElementById('searchInput').value = searchTerm;
            }
        });
    </script>
</body>
</html>
