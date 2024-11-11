const express = require('express');
const multer = require('multer');
const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

const app = express();
const upload = multer({ dest: 'uploads/' });

// Serve static files from the current directory
app.use(express.static('./'));

// Handle Excel file upload and conversion
app.post('/api/update-json', upload.single('excelFile'), (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }

        // Read the Excel file
        const workbook = XLSX.readFile(req.file.path);
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        // Convert to JSON
        const jsonData = XLSX.utils.sheet_to_json(worksheet);

        // Write to data.json
        fs.writeFileSync(
            path.join(__dirname, 'data.json'),
            JSON.stringify(jsonData, null, 2)
        );

        // Clean up: delete the uploaded file
        fs.unlinkSync(req.file.path);

        res.json({ 
            success: true, 
            message: 'Data successfully converted and saved to data.json',
            recordCount: jsonData.length
        });

    } catch (error) {
        console.error('Server error:', error);
        res.status(500).json({ 
            error: 'Server error during file processing',
            details: error.message
        });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});
