<?php

namespace App\Services;

use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * This function is used to upload a file to the storage system.
 *
 * @param \Illuminate\Http\UploadedFile $image The uploaded file.
 * @param \Illuminate\Http\UploadedFile $images The uploaded file as a list.
 * @param string $customDirectory Custom directory for storing the file.
 * @param Model|null $model Model associated with the file (optional).
 * @param string $disk Storage disk name (default: 'public').
 * @return bool|string On success, the name of the stored file; otherwise, `false`.
 *
 */

class ImageUploaderService
{
    public function upload(UploadedFile $image , array $images, $customDirectory = '', $disk = 'public'): array
    {
        try {
            $imageDirectory = Storage::disk($disk)->putFile($customDirectory, $image);

            foreach ($images as $img) {
                $name = Storage::disk($disk)->putFile($customDirectory, $img);
                $secondaryImagesName[] = $name;
            }

            return ['primary_image'=> $imageDirectory,'images' => $secondaryImagesName];

        } catch (\Exception $e) {
            throw  new RuntimeException($e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        return new self();
    }
}
