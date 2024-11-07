const DATA_URL = 'https://raw.githubusercontent.com/kanereroma2343/nttcmonitor/refs/heads/main/data.json';

let certificationData = [];

async function fetchData() {
    try {
        const response = await fetch(DATA_URL, {
            cache: 'no-store'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (!Array.isArray(data)) {
            throw new Error('Data is not in the expected format');
        }
        
        certificationData = data;
        handleSearch();
    } catch (error) {
        console.error('Error fetching data:', error);
        document.getElementById('resultsTable').innerHTML = `
            <tr><td colspan="5" class="text-center text-danger">
                Error loading data. Please try again later.
            </td></tr>
        `;
    }
}

function formatName(record) {
    const parts = [record.D, record.E];
    if (record.F) parts.push(record.F);
    if (record.G) parts.push(record.G);
    return parts.filter(Boolean).join(' ').replace(/,\s*$/, '');
}

function highlightText(text, searchTerm) {
    if (!searchTerm) return text;
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    return text.replace(regex, '<span class="highlight">$1</span>');
}

function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    
    const filteredData = certificationData.filter(record => {
        const name = formatName(record).toLowerCase();
        const cert = record.S.toLowerCase();
        const certNo = record.AG.toLowerCase();
        return name.includes(searchTerm) || 
               cert.includes(searchTerm) || 
               certNo.includes(searchTerm);
    });

    displayResults(filteredData, searchTerm);
}

function displayResults(results, searchTerm) {
    const tbody = document.getElementById('resultsTable');
    const countDiv = document.getElementById('resultsCount');
    
    countDiv.textContent = `Found ${results.length} record${results.length !== 1 ? 's' : ''}`;
    
    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No results found</td></tr>';
        return;
    }

    tbody.innerHTML = results.map(record => `
        <tr>
            <td>${highlightText(formatName(record), searchTerm)}</td>
            <td>${highlightText(record.S, searchTerm)}</td>
            <td>${highlightText(record.AG, searchTerm)}</td>
            <td>${record.V}</td>
            <td>${record.AE}</td>
        </tr>
    `).join('');
}

// Initialize
document.getElementById('searchInput').addEventListener('input', handleSearch);
fetchData();
