<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    protected function setSheet(array $data, Worksheet $sheet)
    {
        $styleArray = [
            'alignment' => [
                'vertical'   => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'font'      => [
                'bold'   => false,
                'italic' => false,
                'size'   => 16,
                'color'  => ['rgb' => '000000'],
                'name'   => 'Calibri'
            ],
        ];

        $sheet->fromArray($data, null, 'A2')
            ->getStyle($sheet->calculateWorksheetDimension())
            ->applyFromArray($styleArray);

    }

    protected function setColumns(Worksheet $sheet, array $data)
    {
        foreach ($data as $key => $item) {
            $colNames = array_keys($item);
        }

        foreach ($colNames as $key => $colName) {
            $cell = chr($key + 65) . '1';
            $sheet->getCell($cell)->setValue($colName);
        }
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }

    protected function generateFile(Spreadsheet $spreadsheet)
    {
        $writer   = new Xlsx($spreadsheet);
        $fileName = 'liste_' . md5(date('Y-m-d H:i:i')) . '.xlsx';
        $writer->save($fileName);
    }
}