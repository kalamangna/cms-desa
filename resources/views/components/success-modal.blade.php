@props([
    'title',
    'description' => null,
    'ticketNumber' => null,
])

<div x-data="{
        showSuccessModal: true,
        copied: false,
        async copyTicket(text) {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const input = document.createElement('textarea');
                    input.value = text;
                    input.setAttribute('readonly', '');
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                }
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            } catch (e) {}
        }
     }"
     x-effect="document.body.classList.toggle('overflow-hidden', showSuccessModal)"
     x-show="showSuccessModal"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
     role="dialog"
     aria-modal="true"
     aria-labelledby="success-modal-title"
     @keydown.escape.window="showSuccessModal = false"
     @click.self="showSuccessModal = false">

    <div @click.stop class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-6 sm:p-8 md:px-10 w-full max-w-md mx-auto border border-slate-100 dark:border-slate-800 relative text-center cursor-default">
        <button
            type="button"
            @click.stop="showSuccessModal = false"
            aria-label="Tutup dialog"
            class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 w-10 h-10 rounded-full flex items-center justify-center transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>

        <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-5 shadow-xs">
            <i class="fa-solid fa-check"></i>
        </div>

        <h3 id="success-modal-title" class="font-heading font-extrabold tracking-tight text-slate-900 dark:text-slate-100 text-xl sm:text-2xl {{ $description ? 'mb-2' : 'mb-6' }}">{{ $title }}</h3>
        @if($description)
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 font-medium">{{ $description }}</p>
        @endif

        @if($ticketNumber)
            <div class="mb-6 sm:mb-8">
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-1">Nomor Tiket</span>
                <div class="flex items-center justify-center gap-2">
                    <h4 class="text-lg sm:text-xl font-mono font-black text-slate-800 dark:text-slate-100 select-all break-all">{{ $ticketNumber }}</h4>
                    <button type="button"
                            @click="copyTicket(@js($ticketNumber))"
                            aria-label="Salin nomor tiket"
                            class="shrink-0 w-10 h-10 inline-flex items-center justify-center rounded-xl text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                    </button>
                </div>
            </div>
        @endif

        <button type="button" @click="showSuccessModal = false" class="w-full h-11 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 text-white rounded-2xl font-bold text-sm transition-all duration-200 cursor-pointer">
            Tutup
        </button>
    </div>
</div>
