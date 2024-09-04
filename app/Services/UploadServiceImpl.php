<?php
namespace App\Services;

use App\Services\Interfaces\UploadService;
use Illuminate\Support\Facades\Facade;

class UploadServiceImpl implements UploadService
{
    public function upload($file)
    {
        try {
            $imageName = time() . '.' . $file->extension();
            $file->storeAs('images', $imageName, [
                'disk' => 'public'
            ]);
            return $imageName;
        }catch (\Exception $e){
            return false;
        }
    }
}
