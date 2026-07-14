<?php
require '/var/www/html/vendor/autoload.php';

// Save HEAD template to temp file
$headTemp = tempnam(sys_get_temp_dir(), 'excel_head');
$currentPath = '/var/www/html/public/tamplate-xlsx/Internal_Audit_Export_Tamplate.xlsx';

shell_exec("git -C /var/www/html show HEAD:public/tamplate-xlsx/Internal_Audit_Export_Tamplate.xlsx > " . escapeshellarg($headTemp));

if (file_exists($headTemp) && file_exists($currentPath)) {
    $ssHead = \PhpOffice\PhpSpreadsheet\IOFactory::load($headTemp);
    $ssCurr = \PhpOffice\PhpSpreadsheet\IOFactory::load($currentPath);
    
    $sheetHead = $ssHead->getActiveSheet();
    $sheetCurr = $ssCurr->getActiveSheet();
    
    echo "--- HEAD Headers (Row 8) ---" . PHP_EOL;
    for ($c = 1; $c <= 26; $c++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $val = $sheetHead->getCell($colLetter . 8)->getValue();
        if ($val !== null) echo "$colLetter: $val" . PHP_EOL;
    }
    
    echo "--- Current Headers (Row 8) ---" . PHP_EOL;
    for ($c = 1; $c <= 26; $c++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $val = $sheetCurr->getCell($colLetter . 8)->getValue();
        if ($val !== null) echo "$colLetter: $val" . PHP_EOL;
    }
}
@unlink($headTemp);
unlink(__FILE__);
