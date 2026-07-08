<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = 'public/tamplate-xlsx/Internal_Audit_Export_Tamplate.xlsx';
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getActiveSheet();

// We want to write data starting from row 11.
$styleB11 = $sheet->getStyle('B11');

// Clear row 11
for ($col = 2; $col <= 19; $col++) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
    $sheet->setCellValue($colLetter . 11, null);
}

// Write cell
$sheet->duplicateStyle($styleB11, 'B11');
$sheet->setCellValue('B11', 1);

// Save to temp file
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('public/test_output.xlsx');

// Load back and inspect B11
$savedSpreadsheet = IOFactory::load('public/test_output.xlsx');
$savedSheet = $savedSpreadsheet->getActiveSheet();
$cell = $savedSheet->getCell('B11');
$style = $savedSheet->getStyle('B11');

echo "Saved B11 Value: " . json_encode($cell->getValue()) . "\n";
echo "Saved B11 Formatted Value: " . $cell->getFormattedValue() . "\n";
echo "Saved B11 Format Code: " . $style->getNumberFormat()->getFormatCode() . "\n";
echo "Saved Column B Width: " . $savedSheet->getColumnDimension('B')->getWidth() . "\n";
@unlink('public/test_output.xlsx');
