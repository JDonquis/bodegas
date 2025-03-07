<?php  

namespace App\Services;

use App\Models\OutputGeneral;
use Codedge\Fpdf\Fpdf\Fpdf;
use Exception;

class PDFService
{   
    private $accents = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ'];
    private $noAccents = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'n', 'N'];

    public function create($outputGeneral)
{   


    // Cargar datos desde el archivo .env
    $bodegaName = env('BODEGA_NAME');
    $bodegaAddress = env('BODEGA_ADDRESS');
    $bodegaRif = env('BODEGA_RIF');
    $bodegaPhone1 = env('BODEGA_PHONE_NUMBER_1');
    $bodegaPhone2 = env('BODEGA_PHONE_NUMBER_2');

    $pdf = new Fpdf('P', 'mm', array(80, 200)); // 'P' para vertical, 'mm' para milímetros, tamaño 80mm x 200mm
    $pdf->AddPage();

    // Configuración de fuente
    $pdf->SetFont('Arial', 'B', 12);
    
    // Título de la bodega en mayúsculas y centrado
    $pdf->Cell(0, 7 , strtoupper($bodegaName), 0, 1, 'C');

    // RIF centrado
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5 , $bodegaRif, 0, 1, 'C');

    // Dirección centrada
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5 , $bodegaAddress, 0, 1, 'C');

    // Números de teléfono alineados a la izquierda
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5 , 'Telefono 1: ' . $bodegaPhone1, 0, 1);
    $pdf->Cell(0, 5 , 'Telefono 2: ' . $bodegaPhone2, 0, 1);

    // Cliente
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5 , 'Cliente: Contado', 0, 1,);

    // Fecha de la factura
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5 , 'Fecha: ' . date('d/m/Y'), 0, 1,);

    
    // Título de factura
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 7 , 'FACTURA', 0, 1, 'C');

    // Productos
    $totalAmount = 0;
    $pdf->SetFont('Arial', '', 9);
    foreach ($outputGeneral->outputs as $output) {
        $productName = $output->product->name; 

        $productName = str_replace($this->accents, $this->noAccents, $productName);

        $quantity = $output->quantity; 
        $price = $output->product->sell_price; 
        $lineTotal = $quantity * $price; 
        $totalAmount += $lineTotal; 

        // Imprimir línea de producto
        $pdf->Cell(0, 5, $productName . ' x ' . $quantity, 0, 0); // Izquierda
        $pdf->Cell(0, 5, number_format($lineTotal, 2), 0, 1, 'R'); // Derecha
    }
    

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 7, 'TOTAL: ' . number_format($totalAmount, 2), 0, 1, 'L');


    // Salida del PDF
    $pdf->Output('I', 'factura.pdf'); // 'I' para mostrar en el navegador
    exit;
}

}
