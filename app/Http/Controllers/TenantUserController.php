<?php

namespace App\Http\Controllers;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class TenantUserController extends Controller
{
    public function index()
    {
        $users = User::with('typeUser')
            ->orderBy('name')
            ->paginate(15);

        return view('home.users.index', compact('users'));
    }

    public function create()
    {
        return view('home.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'charge' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:50', Rule::unique('users', 'ci')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::beginTransaction();

        try {
            $typeUserId = auth()->user()->type_user_id
                ?? TypeUser::firstOrCreate(['name' => 'admin'])->id;

            $user = User::create([
                'name' => $validated['name'],
                'last_name' => $validated['last_name'],
                'charge' => $validated['charge'],
                'ci' => $validated['ci'],
                'type_user_id' => $typeUserId,
                'password' => $validated['password'],
            ]);

            $user->assignRole('admin');

            DB::commit();

            return redirect()->route('users')->with(['success' => 'Usuario creado exitosamente']);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL CREAR USUARIO DE BODEGA: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()])->withInput();
        }
    }

    public function edit(User $user)
    {
        return view('home.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'charge' => ['required', 'string', 'max:255'],
            'ci' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'ci')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users')->with(['success' => 'Usuario actualizado exitosamente']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propio usuario.']);
        }

        DB::beginTransaction();

        try {
            $user->roles()->detach();
            $user->permissions()->detach();
            $user->delete();

            DB::commit();

            return redirect()->route('users')->with(['success' => 'Usuario eliminado exitosamente']);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL ELIMINAR USUARIO DE BODEGA: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
