document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const statusDiv = document.getElementById('status');
    
    try {
        const response = await fetch('/your-upload-endpoint', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        statusDiv.textContent = 'Conversion successful!';
        
    } catch (error) {
        console.error('Error:', error);
        statusDiv.textContent = 'Error occurred during conversion. Please try again.';
    }
});
