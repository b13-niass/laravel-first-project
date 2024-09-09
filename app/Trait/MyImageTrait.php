<?php

namespace App\Trait;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

trait MyImageTrait
{
    public function getImageLocalAsBase64($imagePath)
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

    public function getImageCloudAsBase64($imagePath)
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

    public function generateQrcode($id){
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($id);
    }

    public function svgToBase64($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }
        $svgContent = file_get_contents($filePath);
        $base64 = base64_encode($svgContent);
        return "data:image/svg+xml;base64," . $base64;
    }

}
