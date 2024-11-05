<?php
require 'vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$data = $_GET['data'] ?? '';
if ($data) {
    list($codigo_qr, $total) = explode('|', $data);
   
    $qrCode = new QrCode($codigo_qr);
    
    // Cambia setSize() por setWidth() y setHeight()
   $qrCode->getSize();
  // $qrCode->setsize(256)
   //    ->setMargin(10);

   // $result = $writer->write($qrCode, ['size' => 256]);
    // O alternativamente
    //$qrCode->setWidth(256);
   // $qrCode->setHeight(256);
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
} else {
    http_response_code(400);
    echo "Error: Datos del código QR no proporcionados.";
}
?>

