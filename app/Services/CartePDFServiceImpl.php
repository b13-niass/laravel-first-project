<?php
namespace App\Services;

use App\Services\Interfaces\CarteService;
use Mpdf\Mpdf;

class CartePDFServiceImpl implements  CarteService
{
    public function format($data)
    {
        $client = $data['client'];
        $qrcode = $data['qrcode'];
        $html = view('mails.carte_fidelite', compact('client', 'qrcode'))->render();
//
//        $pdf = new \TCPDF();
//
//        $pdf->SetCreator('Boutiquier');
//        $pdf->SetAuthor('Alpha');
//        $pdf->SetTitle('Carte de Fidélité');
//        $pdf->SetSubject('');
//
//        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//
//        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
//
//        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//
//        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//
//        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//
//        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
//        $pdf->SetFont('helvetica', '', 10);
//
//        $pdf->AddPage();
//
//        $pdf->writeHTML($html, true, false, true, false, '');
//        $filePath = storage_path('app/public/carte/my_pdf_file.pdf');
//        $pdf->Output($filePath, 'F');
        $mpdf = new Mpdf();
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);
        $filePath = storage_path('app/public/carte/my_pdf_file.pdf');
        $mpdf->Output($filePath, 'F');
        return $filePath;
    }

}
