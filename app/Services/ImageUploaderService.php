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
    public function upload($primaryImage, $images)
    {
        $fileNamePrimaryImage = generateFileName($primaryImage->getClientOriginalName());

        $primaryImage->move(public_path(env('PRODUCT_IMAGES_UPLOADER_PATH')), $fileNamePrimaryImage);

        $fileNameImages = [];
        foreach ($images as $image) {
            $fileNameImage = generateFileName($image->getClientOriginalName());

            $image->move(public_path(env('PRODUCT_IMAGES_UPLOADER_PATH')), $fileNameImage);

            array_push($fileNameImages ,  $fileNameImage );
        }

        return [ 'fileNamePrimaryImage' => $fileNamePrimaryImage , 'fileNameImages' => $fileNameImages];
    }

    public static function getInstance(): self
    {
        return new self();
    }
}
