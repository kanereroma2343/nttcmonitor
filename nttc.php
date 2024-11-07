<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="icons/nttcmis.png">
    <title>Monitoring Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #000000, #0000FF, #FFFFFF);
            margin: 0;
            padding: 0;
        }
        .container {
    width: 80%;
    margin: 20px auto;
    background-color: #FFFFFF;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    opacity: 0; /* Start with opacity 0 to prepare for animation */
    animation: fadeIn 1s ease-in-out forwards; /* Animation to fade in */
}
.name-link {
    color: black; /* Default text color */
    text-decoration: none; /* No underline by default */
}

.name-link:hover {
    color: blue; /* Blue text color on hover */
    text-decoration: underline; /* Underline text on hover */
}

@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}
        h2, h3 {
            text-align: center;
            color: #000000;
        }
        .header-buttons {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .header-buttons a {
            background-color: #0000FF; /* Blue */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .header-buttons a:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        .expired {
            color: red;
            font-weight: bold;
        }
        .expiring-soon {
            color: orange;
            font-weight: bold;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #000000;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .lock-container button {
            background-color: #0000FF; /* Blue */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 10px;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-container input {
            padding: 10px;
            width: 50%;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .search-container button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            margin-left: 5px;
        }

        /* Loading Spinner */
        .loader {
            display: none;
            text-align: center;
            margin: 20px auto;
        }
        .loader .spinner {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #007bff;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            display: inline-block;
        }
        .loader .message {
            display: block;
            margin-top: 10px;
            font-size: 16px;
            color: #000000;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            animation: modalopen 0.3s ease;
        }
        @keyframes modalopen {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .modal button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body onload="addAnimation()">
<div class="container">
    <div style="text-align: center;">
        <img src="icons/both.png" alt="Logo" style="width: 220px; height: 100px; margin-right: 10px; display: inline-block;">
        <h2>Current NTTC Holders Monitoring System</h2>
        <h3 id="currentDate">As of: </h3>
    </div>

    <div class="header-buttons">
        <a href="../index.php">Home</a>
        <a href="#" id="uploadButton">Registry Update</a>
        <a href="#" id="infoButton">Info</a>
        <a href="statistics.php" id="statisticsButton">Statistics</a>
        <a href="delete.php" id="deleteButton">Delete</a>
        <a href="dfr.php" id="dfrButton">Due for Renewal</a>
    </div>
    

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search for Names, Qualifications, Certificate Number, Date of Issuance (mm-dd-yyyy), Date of Expiry (mm-dd-yyyy)..." onkeypress="checkEnterKey(event)">
        <button onclick="searchTable()">Search</button>
    </div>

    <div class="loader" id="loader">
        <div class="spinner"></div>
        <div class="message" id="loaderMessage">Finding the data<span id="dots">...</span></div>
    </div>

    <table id="dataTable">
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
        <tbody id="dataBody">
        <?php
// PHP code starts here
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "excel_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current date
$currentDate = new DateTime();

// Query to fetch all data from the excel_data table
$sql = "SELECT * FROM excel_data";
$result = $conn->query($sql);

// Output data of each row
while($row = $result->fetch_assoc()) {
    $validityDate = new DateTime($row['validity']);
    $interval = $currentDate->diff($validityDate);
    $class = '';

    if ($validityDate < $currentDate) {
        $class = 'expired';
    } elseif ($interval->m <= 3 && $interval->invert == 0 && $currentDate->format('Y') == $validityDate->format('Y')) {
        $class = 'expiring-soon';
    } else {
        $class = 'active';
    }

    // Create URL parameters
    $params = http_build_query([
        'last_name' => $row['last_name'],
        'first_name' => $row['first_name'],
        'middle_name' => $row['middle_name'],
        'extension' => $row['extension'],
        'qualification' => $row['qualification'],
        'certificate_number' => $row['certificate_number'],
        'control_number' => $row['control_number'],
        'date_of_issuance' => $row['date_of_issuance'],
        'validity' => $row['validity']
    ]);
    
    echo "<tr>";
    echo "<td><a href='view_record.php?$params' class='name-link'>" . htmlspecialchars($row['last_name']) . "</a></td>";
    echo "<td><a href='view_record.php?$params' class='name-link'>" . htmlspecialchars($row['first_name']) . "</a></td>";
    echo "<td><a href='view_record.php?$params' class='name-link'>" . htmlspecialchars($row['middle_name']) . "</a></td>";
    echo "<td><a href='view_record.php?$params' class='name-link'>" . htmlspecialchars($row['extension']) . "</a></td>";
    echo "<td>" . htmlspecialchars($row['qualification']) . "</td>";
    echo "<td>" . htmlspecialchars($row['certificate_number']) . "</td>";
    echo "<td>" . htmlspecialchars($row['control_number']) . "</td>";
    echo "<td>" . htmlspecialchars($row['date_of_issuance']) . "</td>";
    echo "<td class='" . $class . "'>" . htmlspecialchars($row['validity']) . "</td>";
    echo "</tr>";
}

$conn->close();
?>

        </tbody>
    </table>
    <div class="pagination" id="pagination"></div>
</div>

<!-- Modal for Password -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><img src="icons/locked.jpg" alt="Lock Icon" style="width: 20px; height: 20px; margin-right: 5px;">Enter Password </h2>
        <input type="password" id="passwordInput" placeholder="Password">
        <button onclick="checkPassword()">Submit</button>
    </div>
</div>

<!-- Modal for Info -->
<div id="infoModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Legend:</h2>
        <p><span style="color: red; font-weight: bold;">Red:</span> Expired</p>
        <p><span style="color: orange; font-weight: bold;">Orange:</span> Due For Renewal (within 3 months)</p>
        <p><span style="color: green; font-weight: bold;">Green:</span> Active</p>
        <a href="https://privacy.gov.ph/data-privacy-act/">Republic Act: 10173 - Data Privacy Act</a>
    </div>
</div>

<script>
     function addAnimation() {
        const container = document.querySelector('.container');
        container.classList.add('animate-container');
    }

    // Event listener for when the page finishes loading
    window.addEventListener('load', function() {
        addAnimation();
    });

    var currentDateElement = document.getElementById('currentDate');

    // Create a new Date object to get the current date
    var currentDate = new Date();

    // Format the date to 'Month Day, Year' format
    var formattedDate = currentDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

    // Update the content of the currentDateElement with the formatted date
    currentDateElement.textContent += formattedDate;

    // Get modal elements
    var modal = document.getElementById("myModal");
    var infoModal = document.getElementById("infoModal");
    var btn = document.getElementById("uploadButton");
    var infoBtn = document.getElementById("infoButton");
    var span = document.querySelectorAll(".modal-content .close");

    // Open password modal on button click
    btn.onclick = function() {
        modal.style.display = "block";
        modal.style.opacity = "1";
    }

    // Open info modal on button click
    infoBtn.onclick = function() {
        infoModal.style.display = "block";
        infoModal.style.opacity = "1";
    }

    // Close modal on <span> click
    span.forEach(function(element) {
        element.onclick = function() {
            modal.style.opacity = "0";
            setTimeout(() => { modal.style.display = "none"; }, 300);
            infoModal.style.opacity = "0";
            setTimeout(() => { infoModal.style.display = "none"; }, 300);
        }
    });

    // Close modal on outside click
    window.onclick = function(event) {
        if (event.target == modal || event.target == infoModal) {
            modal.style.opacity = "0";
            setTimeout(() => { modal.style.display = "none"; }, 300);
            infoModal.style.opacity = "0";
            setTimeout(() => { infoModal.style.display = "none"; }, 300);
        }
    }

    // Check password
    function checkPassword() {
        var password = document.getElementById("passwordInput").value;
        if (password === "tesda@29") {
            window.location.href = "uploadxls.php";
        } else {
            alert("Incorrect password. Please try again.");
        }
    }

    // Pagination and search logic
    const rowsPerPage = 200;
    let currentPage = 1;
    const table = document.getElementById('dataTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');

    function displayPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = i >= start && i < end ? '' : 'none';
        }
    }

    function setupPagination() {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const link = document.createElement('a');
            link.href = '#';
            link.textContent = i;
            link.onclick = (function(page) {
                return function() {
                    currentPage = page;
                    displayPage(currentPage);
                    updatePagination();
                }
            })(i);
            pagination.appendChild(link);
        }

        updatePagination();
    }

    function updatePagination() {
        const links = document.querySelectorAll('.pagination a');
        links.forEach((link, index) => {
            link.classList.toggle('active', index + 1 === currentPage);
        });
    }

    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const tr = table.getElementsByTagName('tr');

        document.getElementById('loader').style.display = 'block';

        const dots = document.getElementById('dots');
        let dotCount = 0;
        const interval = setInterval(() => {
            dotCount = (dotCount + 1) % 4;
            dots.textContent = '.'.repeat(dotCount);
        }, 500);

        setTimeout(() => {
            document.getElementById('loaderMessage').textContent = "Searching registry...";
            for (let i = 1; i < tr.length; i++) { // Skip the header row
                const td = tr[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = match ? '' : 'none';
            }

            document.getElementById('loader').style.display = 'none';
            clearInterval(interval);
        }, 500); // Adjust the delay as necessary
    }

    function checkEnterKey(event) {
        if (event.key === 'Enter') {
            searchTable();
        }
    }

    displayPage(currentPage);
    setupPagination();
</script>
<?php include 'footer.php'; ?>
</body>
</html>