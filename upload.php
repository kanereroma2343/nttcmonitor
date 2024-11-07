<?php
require 'vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed via composer

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["excelFile"])) {
        try {
            $targetFile = "uploads/" . basename($_FILES["excelFile"]["name"]);
            
            // Create uploads directory if it doesn't exist
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // Move uploaded file
            move_uploaded_file($_FILES["excelFile"]["tmp_name"], $targetFile);

            // Load the Excel file
            $spreadsheet = IOFactory::load($targetFile);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Define the columns we want to extract
            $columns = [
                'D' => 3,  // Column index starts from 0
                'E' => 4,
                'F' => 5,
                'G' => 6,
                'S' => 18,
                'V' => 21,
                'AD' => 29,
                'AE' => 30,
                'AG' => 32
            ];

            $data = [];
            
            // Get highest row number
            $highestRow = $worksheet->getHighestRow();

            // Extract data from specified columns
            for ($row = 7; $row <= $highestRow; $row++) { // Start from row 7 to skip headers
                $rowData = [];
                foreach ($columns as $column => $index) {
                    $cell = $worksheet->getCell([$index + 1, $row]);
                    
                    // Get calculated value instead of formula
                    $cellValue = $cell->getCalculatedValue();
                    
                    // Check if the cell contains a date
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        // Convert Excel date to PHP DateTime
                        $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                        // Format date as "June 5, 1999"
                        $cellValue = $dateValue->format('F j, Y');
                    }
                    
                    $rowData[$column] = $cellValue;
                }
                $data[] = $rowData;
            }

            // Convert to JSON
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
            
            // Save JSON file
            $jsonFile = 'output.json';
            file_put_contents($jsonFile, $jsonData);

            echo "Conversion successful! <a href='{$jsonFile}'>Download JSON</a>";

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Excel to JSON Converter</title>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="excelFile" accept=".xlsx,.xls" required>
        <input type="submit" value="Convert to JSON">
    </form>
</body>
</html>