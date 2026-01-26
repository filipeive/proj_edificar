<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'Relatório Trimestral LC Quelimane - IV Trimestre 2025 (1-Outub á 31-Dezembro) (Respostas).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

foreach (array_slice($rows, 0, 5) as $i => $row) {
    echo "Row $i:\n";
    foreach ($row as $col => $val) {
        echo "  $col: $val\n";
    }
}
