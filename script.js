const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const uploadForm = document.getElementById('uploadForm');
const progressBar = document.getElementById('progressBar');
const progress = document.getElementById('progress');
const status = document.getElementById('status');

// Drag and drop handlers
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    fileInput.files = e.dataTransfer.files;
});

// Form submission handler
uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(uploadForm);
    
    progressBar.style.display = 'block';
    status.innerHTML = 'Converting...';
    
    try {
        const response = await fetch('http://localhost:3000/upload', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            progress.style.width = '100%';
            status.innerHTML = `${data.message} <a href="${data.file}" class="success">Download JSON</a>`;
        } else {
            status.innerHTML = `<span class="error">Error: ${data.message}</span>`;
        }
    } catch (error) {
        status.innerHTML = `<span class="error">Error: ${error.message}</span>`;
    }
});

// File input change handler
fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        dropZone.querySelector('p').textContent = `Selected file: ${fileInput.files[0].name}`;
    }
});