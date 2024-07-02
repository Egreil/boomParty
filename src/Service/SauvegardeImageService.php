<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class SauvegardeImageService
{
    public function RenomerImage(UploadedFile $file, string $path,string $name){
        $newFilename=($name ? $name.'-' : '') . uniqid() . '.' . $file->guessExtension();
        $file->move($path, $newFilename);
        return $newFilename;
    }
}