<?php

namespace App\Http\Controllers;

use App\Models\OutputGeneral;
use App\Services\PDFService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index($outputID){
            $outputGeneral = OutputGeneral::where('id',$outputID)->with('outputs.product','outputs.inventory')->first();
            $pdfService = new PDFService();
            $pdfService->create($outputGeneral);
    }
}
