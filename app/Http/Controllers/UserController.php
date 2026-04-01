<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function validateMasterPassword(Request $request)
    {
        $request->validate([
            'ci' => 'required',
            'master_password' => 'required',
        ]);

        $masterPassword = env('MASTER_PASSWORD', 'admin123');


        if (! $masterPassword) {
            return back()->withErrors(['error' => 'No hay una Master Password configurada en el sistema.']);
        }

        if ($request->master_password !== $masterPassword) {
            return back()->withErrors(['error' => 'La Master Password es incorrecta.']);
        }

        // Check if user exists
        $userExists = \App\Models\User::where('ci', $request->ci)->exists();
        if (! $userExists) {
            return back()->withErrors(['error' => 'El usuario con esa cédula no existe.']);
        }

        return redirect()->route('password.reset', ['ci' => $request->ci])->with(['master_validated' => true]);
    }

    public function showResetPassword($ci)
    {
        // For security, you might want to check if they came from the validation step
        // But for this simplified request, we will just show the view
        return view('auth.reset-password', compact('ci'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'ci' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = \App\Models\User::where('ci', $request->ci)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('login')->with(['success' => 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.']);
    }

    public function login(LoginRequest $request)
    {

        $loginService = new LoginService;

        if (! $loginService->tryLoginOrFail(['ci' => $request->ci, 'password' => $request->password])) {
            return redirect('/')->withErrors(['error' => 'Creedenciales incorrectas']);
        }

        return redirect()->intended('/home')->with(['success' => 'Bienvenido ' . auth()->user()->name . '!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function profile()
    {
        return view('home.myprofile');
    }

    public function updateProfile(Request $request)
    {

        $user = auth()->user();
        $user->name = $request->input('name');
        $user->last_name = $request->input('lastName');
        $user->ci = $request->input('ci');

        $user->save();

        return redirect()->back()->with(['success' => 'Perfil actualizado exitosamente']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with(['success' => 'Contraseña actualizada exitosamente']);
    }
}
