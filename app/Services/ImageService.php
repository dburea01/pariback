<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function uploadImage(string $imageName, $imageFile)
    {
        Storage::disk('public')->putFileAs('/', $imageFile, $imageName);
    }

    public function deleteImage(string $imageName)
    {
        Storage::disk('public')->delete($imageName);
    }
}
