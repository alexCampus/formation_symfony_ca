<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;
    private $directory;

    public function __construct(string $directory, SluggerInterface $slugger)
    {
        $this->slugger   = $slugger;
        $this->directory = $directory;
    }

    public function upload(File $img)
    {
        $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename  = $safeFilename . '-' . uniqid('', true) . '.' . $img->guessExtension();
        try {
            $img->move(
                $this->directory,
                $newFilename
            );
        } catch (FileException $e) {

            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }
}