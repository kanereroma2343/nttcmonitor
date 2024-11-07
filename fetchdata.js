// fetch.js

// Function to fetch data from the JSON URL
async function fetchData() {
    try {
        const response = await fetch('https://raw.githubusercontent.com/kanereroma2343/nttcmonitor/refs/heads/main/data.json');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        displayData(data);
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
}

// Function to display data in the table
function displayData(data) {
    const tableBody = document.querySelector('table tbody');
    tableBody.innerHTML = ''; // Clear previous data

    // Filter and iterate over the data to insert rows in the table
    data.slice(4).forEach(row => {
        if (row['D'] || row['E']) { // Ensure row is not empty
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Last Name">${row['D'] || ''}</td>
                <td data-label="First Name">${row['E'] || ''}</td>
                <td data-label="Middle Name">${row['F'] || ''}</td>
                <td data-label="Extension">${row['G'] || ''}</td>
                <td data-label="Qualification">${row['S'] || ''}</td>
                <td data-label="Certificate Number">${row['AD'] || ''}</td>
                <td data-label="Control Number">${row['AG'] || ''}</td>
                <td data-label="Date of Issuance">${row['AE'] || ''}</td>
                <td data-label="Validity">${row['V'] || ''}</td>
            `;
            tableBody.appendChild(tr);
        }
    });
}

// Call fetchData on page load
window.addEventListener('load', fetchData);
