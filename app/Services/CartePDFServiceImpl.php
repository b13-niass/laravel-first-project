<?php
namespace App\Services;

use App\Http\Resources\ClientResource;
use App\Services\Interfaces\CarteService;
use App\Trait\MyImageTrait;
use Mpdf\Mpdf;

class CartePDFServiceImpl implements  CarteService
{
    use MyImageTrait;
    public function format($data)
    {
        $client = new ClientResource($data['client']);
        $photo = $this->getImageLocalAsBase64($client->user->photo);
        $qrcode = $data['qrcode'];
        return $filePath;
    }

}
