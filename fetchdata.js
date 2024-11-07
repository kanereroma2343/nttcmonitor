let currentPage = 1;
const rowsPerPage = 10;
let allData = [];
let filteredData = [];

// Fetch data from output.json
async function fetchData() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="9">Loading data...</td></tr>';
    
    try {
        const response = await fetch('data.json', {
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        allData = await response.json();
        
        // Validate data is an array
        if (!Array.isArray(allData)) {
            throw new Error('Invalid data format');
        }
        
        filteredData = [...allData];
        
        // Initialize the UI components
        populateProvinceDropdown();
        displayData();
        setupPagination();
    } catch (error) {
        console.error('Error:', error);
        tableBody.innerHTML = `<tr><td colspan="9">Error loading data: ${error.message}</td></tr>`;
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
    
    if (filteredData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="9">No results found</td></tr>';
        return;
    }
    
    const fragment = document.createDocumentFragment();
    
    filteredData.slice(startIndex, endIndex).forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(item.lastName || '')}</td>
            <td>${escapeHtml(item.firstName || '')}</td>
            <td>${escapeHtml(item.middleName || '')}</td>
            <td>${escapeHtml(item.extension || '')}</td>
            <td>${escapeHtml(item.qualification || '')}</td>
            <td>${escapeHtml(item.certificateNumber || '')}</td>
            <td>${escapeHtml(item.controlNumber || '')}</td>
            <td>${escapeHtml(item.dateOfIssuance || '')}</td>
            <td>${escapeHtml(item.validity || '')}</td>
        `;
        fragment.appendChild(row);
    });
    
    tableBody.appendChild(fragment);
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

// Add this helper function for XSS prevention
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
