<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait FileTrait
{
    protected function uploadFileLocally(UploadedFile $file, string $folder): array
    {
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid('file_', true) . '.' . $extension;

        $relativePath = $file->storeAs($folder, $filename, 'public');
        $fileUrl = Storage::disk('public')->url($relativePath);
        $filePath = Storage::disk('public')->path($relativePath);

        return [$filePath, $fileUrl, $filename];
    }


    public function uploadFile($folder, $file)
    {
        $mimeType = $file->getMimeType();
        $randomName = time() . "_". Str::random(10);
        $extension = $file->getClientOriginalExtension();
        $fileName = $randomName;

        if (strpos($mimeType, 'image') !== false) {
            return $this->uploadImage($folder, $file, $fileName);
        }

        if (strpos($mimeType, 'video') !== false) {
            return $this->uploadVideo($folder, $file, $fileName);
        }

        if ($extension == 'pdf') {
            return cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'raw',
                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.pdf'
            ])->getSecurePath();
        }

        return cloudinary()
            ->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'auto',
                'public_id' => $fileName
            ])
            ->getSecurePath();
    }



    public function uploadMultipleFile($folder, $files)
    {
        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = $this->uploadFile($folder, $file);
        }
        return $filePaths;
    }

    public function uploadImage($folder, $file, $fileName)
    {
        return cloudinary()
            ->upload($file->getRealPath(), [
                'folder' => $folder,
                'public_id' => $fileName, // Use random name
                'transformation' => [
                    'quality' => 'auto',
                    'width' => 800,
                    'height' => 600,
                    'crop' => 'limit',
                ],
            ])
            ->getSecurePath();
    }

    public function uploadVideo($folder, $file, $fileName)
    {
        return cloudinary()
            ->uploadVideo($file->getRealPath(), [
                'folder' => $folder,
                'public_id' => $fileName, // Use random name
                'transformation' => [
                    'width' => 640,
                    'height' => 360,
                    'quality' => 'auto',
                ],
            ])
            ->getSecurePath();
    }
}
