<?php

namespace PK;

use PK\Database\File\File;

class FileUploader
{
    private $files_path;
    private $thumbnails_path;

    public function __construct(string $files_path = '../files/', string $thumbnails_path = '../thumbs/')
    {
        $this->files_path = $files_path;
        $this->thumbnails_path = $thumbnails_path;
    }

    public function upload(string $tmp_name, File $file): bool
    {
        if (!is_uploaded_file($tmp_name)) {
            throw new \Exception();
        }
        
        $file_path = sprintf('%s%s.%s', $this->files_path, $file->getId(), $file->getType());

        try {
            $is_moved = move_uploaded_file($tmp_name, $file_path);
        } catch (\Throwable $e) {
            throw new \Exception();
        }
        
        try {
            $is_chmoded = chmod($file_path, 0666);
        } catch (\Throwable $e) {
            throw new \Exception();
        }

        if (!$is_moved || !$is_chmoded) {
            throw new \Exception();
        }

        $file = new \Imagick();
        $file->readImage($file_path);

        $geometry = $file->getImageGeometry();

        $width_new = $geometry['width'] * (20 / 100);
        $height_new = $geometry['height'] * (20 / 100);

        $file->cropImage($width_new, $height_new);

        $thumb_path = sprintf('%s%s.%s', $this->thumbnails_path, $file->getId(), $file->getType());
        
        $file->writeImage($thumb_path);

        return [$file_path, $thumb_path];
    }
}
