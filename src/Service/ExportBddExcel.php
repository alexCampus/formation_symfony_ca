<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportBddExcel extends ExcelService
{
    private const GEN_ENTITY = ['Post', 'Comment', 'User', 'Tag'];
    private $postRepository;
    private $commentRepository;
    private $userRepository;
    private $tagRepository;

    private $em;

    public function __construct(
        PostRepository $postRepository,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        TagRepository $tagRepository,
        EntityManagerInterface $em
    ) {
        $this->postRepository    = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository    = $userRepository;
        $this->tagRepository     = $tagRepository;
        $this->em                = $em;
    }

    public function generate()
    {
        $spreadsheet = new Spreadsheet();
        foreach (self::GEN_ENTITY as $key => $item) {
            $item = strtolower($item);
            if ($key > 0) {
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($key);
            }
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($item . ' Liste');
            $repositoryName = $item . 'Repository';
            $data           = $this->getFormalizedData($this->$repositoryName);
            $this->setColumns($sheet, $data);
            $this->setSheet($data, $sheet);

        }


        $this->generateFile($spreadsheet);
    }

    private function getFormalizedData($repository): array
    {
        $bddData = $repository->findAll();
        $data    = [];
        foreach ($bddData as $row) {
            if ($row instanceof Post) {
                $data[] = [
                    'Nom'              => $row->getName(),
                    'Content'          => $row->getContent(),
                    'Date Mise à jour' => $row->getUpdatedAt()
                ];
            }
            if ($row instanceof Comment) {
                $data[] = [
                    'Content'          => $row->getContent(),
                    'User'             => $row->getUser()->getEmail() ?? '',
                    'Date Mise à jour' => $row->getUpdatedAt(),
                    'TEST'             => $row
                ];
            }
            if ($row instanceof User) {
                $data[] = [
                    'Nom Prénom' => $row,
                    'Email'      => $row->getEmail(),
                    'Roles'      => implode(',', $row->getRoles()),
                ];
            }
            if ($row instanceof Tag) {
                $data[] = [
                    'Nom'              => $row->getName(),
                    'Date Mise à jour' => $row->getUpdatedAt()
                ];
            }
        }

        return $data;

    }

}
