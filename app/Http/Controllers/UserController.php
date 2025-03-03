<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    


    public function login(LoginRequest $request){

        $loginService = new LoginService;

        Log::info(['ci' => $request->ci, 'password' => $request->password]);
        if(!$loginService->tryLoginOrFail(['ci' => $request->ci, 'password' => $request->password]))
                return redirect('/')->withErrors(['error' => 'Creedenciales incorrectas']);

        return redirect()->intended('/home')->with(['success' => 'Bienvenido '. auth()->user()->name . '!']);

    }

    public function logout(Request $request)
    {
     	Auth::logout();

        return redirect()->route('login');
    }

    public function profile(){
        return view('home.myprofile');
    }

    public function updateProfile(Request $request){
        
        $user = auth()->user();
        $user->name =  $request->input('name');
        $user->last_name =  $request->input('lastName');
        $user->ci =  $request->input('ci');

        $user->save();

        return redirect()->back()->with(['success' => 'Perfil actualizado exitosamente']);

    }
}
