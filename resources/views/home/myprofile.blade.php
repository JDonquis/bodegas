@extends('layout.app')

@section('content')
<!-- Page Header -->
<section class="mb-8">
    <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2">Configuración</h1>
    <p class="text-on-surface-variant font-body">Administra tu información personal y seguridad.</p>
</section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Profile Card -->
    <div class="bg-surface-container-lowest rounded-[2.5rem] overflow-hidden shadow-sm border border-outline-variant/30">
        <div class="px-8 py-6 bg-surface-container-lowest border-b border-outline-variant/20">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-3xl">person</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-primary font-headline">Mi Perfil</h2>
                    <p class="text-xs text-outline uppercase tracking-widest">Información personal</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mx-8 mt-6 p-4 bg-secondary/10 border border-secondary/20 rounded-2xl flex items-center gap-3">
            <span class="material-symbols-outlined text-secondary">check_circle</span>
            <span class="text-sm font-bold text-secondary">{{ session('success') }}</span>
        </div>
        @endif

        <div class="p-8">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-5">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-xs font-black uppercase tracking-widest text-outline">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ auth()->user()->name }}"
                               class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="lastName" class="text-xs font-black uppercase tracking-widest text-outline">Apellido</label>
                        <input type="text" id="lastName" name="lastName" value="{{ auth()->user()->last_name }}"
                               class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="ci" class="text-xs font-black uppercase tracking-widest text-outline">Cédula</label>
                        <input type="text" id="ci" name="ci" value="{{ auth()->user()->ci }}"
                               class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-2xl font-black text-sm hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('home') }}" class="px-6 py-3 bg-surface-container text-on-surface-variant rounded-2xl font-black text-sm hover:bg-surface-container-high transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Card -->
    <div class="bg-surface-container-lowest rounded-[2.5rem] overflow-hidden shadow-sm border border-outline-variant/30">
        <div class="px-8 py-6 bg-surface-container-lowest border-b border-outline-variant/20">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-3xl">lock</span>
                </div>
                <div>
                    <h2 class="text-xl font-black text-secondary font-headline">Seguridad</h2>
                    <p class="text-xs text-outline uppercase tracking-widest">Cambiar contraseña</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('profile.password') }}" id="password-form">
                @method('PUT')
                @csrf
                
                <div class="space-y-5">
                    <div class="flex flex-col gap-2">
                        <label for="current_password" class="text-xs font-black uppercase tracking-widest text-outline">Contraseña Actual</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-secondary transition-all pr-12">
                            <button type="button" onclick="togglePassword('current_password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined text-xl" id="icon-current_password">visibility</span>
                            </button>
                        </div>
                        @error('current_password')
                        <p class="text-xs text-error font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-xs font-black uppercase tracking-widest text-outline">Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-secondary transition-all pr-12">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined text-xl" id="icon-password">visibility</span>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-xs text-error font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password_confirmation" class="text-xs font-black uppercase tracking-widest text-outline">Confirmar Nueva Contraseña</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-4 py-3 bg-surface rounded-2xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-secondary transition-all pr-12">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                                <span class="material-symbols-outlined text-xl" id="icon-password_confirmation">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full px-6 py-3 bg-secondary text-white rounded-2xl font-black text-sm hover:bg-secondary/90 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">key</span>
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('icon-' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection

