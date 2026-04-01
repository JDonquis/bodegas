@if(session('success') || $errors->any())
<div id="toast-notification" class="fixed top-24 right-8 z-[60] flex items-center w-full max-w-xs p-4 space-x-4 text-white {{ session('success') ? 'bg-secondary' : 'bg-error' }} rounded-xl shadow-lg transform transition-all duration-300 translate-x-0" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg bg-white/20">
        <span class="material-symbols-outlined text-xl">
            {{ session('success') ? 'check_circle' : 'error' }}
        </span>
    </div>
    <div class="text-sm font-medium">
        {{ session('success') ? session('success') : $errors->first() }}
    </div>
    <button type="button" onclick="document.getElementById('toast-notification').remove()" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white/80 hover:text-white rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-white/10 inline-flex h-8 w-8" aria-label="Close">
        <span class="material-symbols-outlined text-xl">close</span>
    </button>
</div>

<script>
    setTimeout(() => {
        const toast = document.getElementById('toast-notification');
        if (toast) {
            toast.style.transform = 'translateX(150%)';
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
</script>
@endif
