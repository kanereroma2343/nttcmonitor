const express = require('express');
const multer = require('multer');
const cors = require('cors');
const ExcelJS = require('exceljs');
const fs = require('fs');
const path = require('path');

const app = express();
const port = 3000;

// Middleware
app.use(cors());

// Configure multer for file upload
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const uploadDir = 'uploads';
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir);
        }
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        cb(null, Date.now() + '-' + file.originalname);
    }
});

const upload = multer({ storage: storage });

// Routes
app.post('/upload', upload.single('excelFile'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({
                status: 'error',
                message: 'No file uploaded'
            });
        }

        const workbook = new ExcelJS.Workbook();
        await workbook.xlsx.readFile(req.file.path);
        const worksheet = workbook.getWorksheet(1);

        // Define the columns we want to extract
        const columns = {
            'D': 3,
            'E': 4,
            'F': 5,
            'G': 6,
            'S': 18,
            'V': 21,
            'AD': 29,
            'AE': 30,
            'AG': 32
        };

        const data = [];

        // Extract data from specified columns
        worksheet.eachRow((row, rowNumber) => {
            if (rowNumber >= 7) { // Start from row 7 to skip headers
                const rowData = {};
                Object.entries(columns).forEach(([column, index]) => {
                    const cell = row.getCell(index + 1);
                    let cellValue = cell.value;

                    // Handle dates
                    if (cell.type === ExcelJS.ValueType.Date) {
                        const date = new Date(cell.value);
                        cellValue = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    }

                    rowData[column] = cellValue;
                });
                data.push(rowData);
            }
        });

        // Save as JSON
        const jsonFile = 'output.json';
        fs.writeFileSync(jsonFile, JSON.stringify(data, null, 2));

        // Clean up uploaded file
        fs.unlinkSync(req.file.path);

        res.json({
            status: 'success',
            message: 'Conversion successful!',
            file: jsonFile
        });

    } catch (error) {
        console.error(error);
        res.status(500).json({
            status: 'error',
            message: error.message
        });
    }
});

// Serve the JSON file
app.get('/output.json', (req, res) => {
    res.download('output.json');
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});