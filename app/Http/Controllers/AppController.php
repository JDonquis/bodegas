<?php

namespace App\Http\Controllers;

use App\Models\CasePatient;
use App\Models\EntryGeneral;
use App\Models\InventoryGeneral;
use App\Models\OutputGeneral;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function login(){
        return view('welcome');
    }

    public function home(){

        $entries = EntryGeneral::count();
        $outputs = OutputGeneral::count();
        $inventories = InventoryGeneral::count();
        $casePatients = CasePatient::count();

        return view('home.index')->with(compact('entries','outputs','inventories','casePatients'));
    }
}
