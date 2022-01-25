<?php

namespace App\Service;

use App\Repository\PostRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportPostXls
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function generate(array $params)
    {
        $styleArray  = [
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
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Post Liste');
        $this->setColumns($sheet);
        $sheet->fromArray($this->getFormalizedData($params), null, 'A2')
            ->getStyle($sheet->calculateWorksheetDimension())
            ->applyFromArray($styleArray);

        //TAB 2
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('POst Liste 2');
        $this->setColumns($sheet2);
        $sheet2->fromArray($this->getFormalizedData($params), null, 'A2')
            ->getStyle($sheet2->calculateWorksheetDimension())
            ->applyFromArray($styleArray);

        //FIN TAB 2


        $this->generateFile($spreadsheet);
    }

    private function getFormalizedData(array $params): array
    {
        $posts = $this->postRepository->search($params);

        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                $post->getName(),
                $post->getContent(),
                $post->getUpdatedAt()
            ];
        }

        return $data;

    }

    private function setColumns($sheet)
    {

        $sheet->getCell('A1')->setValue('Nom');
        $sheet->getCell('B1')->setValue('Content');
        $sheet->getCell('C1')->setValue('Date Mise à jour');
    }

    private function generateFile($spreadsheet)
    {
        $writer = new Xlsx($spreadsheet);
        $fileName = 'liste_' . md5(date('Y-m-d H:i:i')) . '.xlsx';
        $writer->save($fileName);
    }
}