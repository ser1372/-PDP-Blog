<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageService
{
    private string $baseUploadDir;

    public function __construct(string $uploadDir = 'public/uploads')
    {
        $this->baseUploadDir = $uploadDir;
    }

    public function downloadImage(string $url, string $subDirectory = ''): string
    {
        $directory = rtrim($this->baseUploadDir, '/') . ($subDirectory ? '/' . trim($subDirectory, '/') : '');

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException('Failed to create directory: ' . $directory);
            }
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = uniqid() . '.' . $extension;
        $filePath = $directory . '/' . $fileName;

        try {
            file_put_contents($filePath, $this->downloadResource($url));
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to save image to: ' . $filePath);
        }

        return $fileName;
    }

    private function downloadResource(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $imageContent = curl_exec($ch);

        if ($imageContent === false) {
            throw new \RuntimeException('Failed to download image from URL: ' . $url . ' - ' . curl_error($ch));
        }

        curl_close($ch);

        return $imageContent;
    }
}
