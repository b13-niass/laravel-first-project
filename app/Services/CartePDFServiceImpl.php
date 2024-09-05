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
//        $svg1 = $this->svgToBase64(public_path('carte/flat_1.svg'));
//        dd($svg1);
//        $svg2 = $this->svgToBase64(public_path('carte/pattern-waves.svg'));
        $photo = $this->getImageLocalAsBase64($client->user->photo);
        $qrcode = $data['qrcode'];
        $html = view('mails.carte_fidelite', compact('client', 'qrcode','photo'))->render();
        $mpdf = new Mpdf();
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);
        $filePath = storage_path('app/public/carte/my_pdf_file.pdf');
        $mpdf->Output($filePath, 'F');
        return $filePath;
    }

}
