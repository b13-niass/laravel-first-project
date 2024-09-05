<?php
namespace App\Services;

use App\Services\Interfaces\UploadService;

class UploadToS3ServiceImpl implements UploadService
{
    public function upload($file)
    {
        try {
            $file_name = $file->store('public/profiles');
            $basePath = env('BASE_URL_IMAGE');
            $filePath = 'https://laravelboutique.s3.amazonaws.com/'.$file_name;
            return $filePath;
        }catch (\Exception $e){
            return false;
        }
    }
}
