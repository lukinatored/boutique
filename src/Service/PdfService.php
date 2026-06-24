<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Facture;
use Twig\Environment;

class PdfService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generateFacturePdf(Facture $facture): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        
        $html = $this->twig->render('facture/pdf.html.twig', [
            'facture' => $facture,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function saveFacturePdf(Facture $facture, string $outputPath): void
    {
        $pdfContent = $this->generateFacturePdf($facture);
        file_put_contents($outputPath, $pdfContent);
    }
}
