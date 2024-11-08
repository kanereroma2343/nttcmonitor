const express = require('express');
const multer = require('multer');
const XLSX = require('xlsx');
const cors = require('cors');
const path = require('path');

const app = express();
const port = 3000;

// Enable CORS
app.use(cors());

// Serve static files from the current directory
app.use(express.static('./'));

// Configure multer for file upload
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

// Handle the /convert endpoint
app.post('/convert', upload.single('excelFile'), (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }

        // Read the Excel file
        const workbook = XLSX.read(req.file.buffer, { type: 'buffer' });
        
        // Convert to JSON
        const result = {};
        workbook.SheetNames.forEach(sheetName => {
            const worksheet = workbook.Sheets[sheetName];
            result[sheetName] = XLSX.utils.sheet_to_json(worksheet);
        });

        res.json(result);
    } catch (error) {
        console.error('Conversion error:', error);
        res.status(500).json({ error: 'Error converting file' });
    }
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
