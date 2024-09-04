<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;

trait MyImageTrait
{
    public function getImageAsBase64($imagePath)
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }
        $fileContents = Storage::disk('public')->get($imagePath);
        $mimeType = Storage::disk('public')->mimeType($imagePath);
        $base64EncodedImage = base64_encode($fileContents);
        $dataUri = 'data:' . $mimeType . ';base64,' . $base64EncodedImage;

        return $dataUri;
    }
}
