<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\CasePatient;
use App\Models\OutputGeneral;
use App\Models\Pathology;
use App\Models\Patient;
use App\Models\TypePatient;
use App\Services\OutputService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $casePatients = CasePatient::with('patient.career','patient.typePatient','pathologies','user','outputGeneral.outputs.product')->orderBy('id','desc')->paginate(10);
        return view('home.patients')->with(compact('casePatients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $careers = Career::get();
        $typePatients = TypePatient::orderBy('id','desc')->get();

        return view('home.patients.create')->with(compact('careers','typePatients'));

    }

    public function search($search){
        $patient = Patient::where('ci',$search)->with('typePatient','career')->first();
        
        return response()->json(['patient' => $patient]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        DB::beginTransaction();
        
        try{
            $patient = Patient::updateOrCreate(
                [
                    'ci' => $request->patient['ci']
                ],
                [
                    'name' => $request->patient['name'],
                    'last_name' => $request->patient['last_name'],
                    'career_id' => $request->patient['career_id'],
                    'type_patient_id' => $request->patient['type_patient_id'],
                    'age' => $request->patient['age'],
                    'sex' => $request->patient['sex'],

                    
                ]
            );

            DB::commit();

            return response()->json(['success' => 'Bien.', 'patient' => $patient]);

        }catch(\Exception $error){
        
            DB::rollBack();

            Log::info('ERROR AL CREAR PACIENTE');
            Log::error($error->getMessage());
            
            return back()->withErrors(['error' => $error->getMessage()]);
        }

    }

    public function searchPathology($search){

        $pathologies = Pathology::whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
        ->orderBy('name','asc')
        ->get();
        
        return response()->json(['pathologies' => $pathologies]);
    }

    public function createPathology(Request $request){
        
        $pathology = Pathology::create(['name' => ucwords($request->pathologyName)]);

        return response()->json(['message' => 'OK', 'pathology' => $pathology, 'success' => 'Patologia creada exitosamente']);
    }

    public function storeCase(Request $request){

        DB::beginTransaction();
        
        try{

            $casePatient = CasePatient::create([
                'patient_id' => $request->patientID,
                'user_id' => auth()->user()->id,
                'output_general_id' => $request->outputGeneralID,
                'observation' => $request->observation ?? 'Sin observación',
            
            ]);
            
            $casePatient->pathologies()->sync($request->pathologies);
            DB::commit();

            return redirect()->route('patients')->with(['success' => 'Caso creado con exito']);

        }catch(\Exception $error){
        
                DB::rollBack();
    
                Log::info('ERROR AL CREAR CASO');
                Log::error($error->getMessage());
                
                return back()->withErrors(['error' => $error->getMessage()]);
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CasePatient $case)
    {   
        DB::beginTransaction();
        try {
            
            if($case->output_general_id != 0){
                
                $OutputGeneral = OutputGeneral::find($case->output_general_id);
                $outputService = new OutputService();
                $outputService->delete($OutputGeneral);
            }


            $case->load('pathologies');
            $case->pathologies()->delete();
            $case->delete();

            DB::commit();

            return redirect()->route('patients')->with(['success' => 'Caso eliminado exitosamente']);

        } catch (\Exception $error) {
            
            DB::rollback();

            Log::info('ERROR AL ELIMINAR CASO');
            Log::error($error->getMessage() . '-- Linea: ' . $error->getLine() . ' -- Archivo:' . $error->getFile());
            
            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
