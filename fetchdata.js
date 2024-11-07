let currentPage = 1;
const rowsPerPage = 10;
let allData = [];
let filteredData = [];

// Fetch data from output.json
async function fetchData() {
    try {
        const response = await fetch('https://raw.githubusercontent.com/kanereroma2343/nttcmonitor/refs/heads/main/data.json');
        if (!response.ok) {
            throw new Error('Failed to fetch data');
        }
        allData = await response.json();
        filteredData = [...allData];
        
        // Populate province dropdown
        populateProvinceDropdown();
        // Display initial data
        displayData();
        // Setup pagination
        setupPagination();
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="9">Error loading data</td></tr>';
    }
}

// Populate province dropdown with unique values
function populateProvinceDropdown() {
    const provinces = [...new Set(allData.map(item => item.province))].sort();
    const provinceSelect = document.getElementById('provinceSelect');
    provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        provinceSelect.appendChild(option);
    });
}

// Display data with pagination
function displayData() {
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const tableBody = document.getElementById('tableBody');
    
    tableBody.innerHTML = '';
    
    filteredData.slice(startIndex, endIndex).forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.lastName || ''}</td>
            <td>${item.firstName || ''}</td>
            <td>${item.middleName || ''}</td>
            <td>${item.extension || ''}</td>
            <td>${item.qualification || ''}</td>
            <td>${item.certificateNumber || ''}</td>
            <td>${item.controlNumber || ''}</td>
            <td>${item.dateOfIssuance || ''}</td>
            <td>${item.validity || ''}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Setup pagination
function setupPagination() {
    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    const paginationElement = document.getElementById('pagination');
    paginationElement.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.classList.add('pagination-button');
        if (i === currentPage) {
            button.classList.add('active');
        }
        button.textContent = i;
        button.addEventListener('click', () => {
            currentPage = i;
            displayData();
            setupPagination();
        });
        paginationElement.appendChild(button);
    }
}

// Search functionality
document.getElementById('searchForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedProvince = document.getElementById('provinceSelect').value;

    filteredData = allData.filter(item => {
        const matchesSearch = Object.values(item).some(value => 
            String(value).toLowerCase().includes(searchTerm)
        );
        const matchesProvince = !selectedProvince || item.province === selectedProvince;
        return matchesSearch && matchesProvince;
    });

    currentPage = 1;
    displayData();
    setupPagination();
});

// Initialize the data fetch when the page loads
document.addEventListener('DOMContentLoaded', fetchData);
