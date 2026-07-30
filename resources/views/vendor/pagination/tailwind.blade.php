@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between w-full">
        
        {{-- Mobile View (Simple Prev/Next) --}}
        <div class="flex justify-between flex-1 sm:hidden gap-4">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center justify-center flex-1 px-4 py-3 text-sm font-bold text-slate-400 bg-white/50 border border-slate-100 cursor-not-allowed rounded-2xl shadow-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i> {!! __('Sebelumnya') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center justify-center flex-1 px-4 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl shadow-sm hover:text-primary-600 hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fa-solid fa-arrow-left mr-2"></i> {!! __('Sebelumnya') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center justify-center flex-1 px-4 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl shadow-sm hover:text-primary-600 hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {!! __('Selanjutnya') !!} <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            @else
                <span class="relative inline-flex items-center justify-center flex-1 px-4 py-3 text-sm font-bold text-slate-400 bg-white/50 border border-slate-100 cursor-not-allowed rounded-2xl shadow-sm">
                    {!! __('Selanjutnya') !!} <i class="fa-solid fa-arrow-right ml-2"></i>
                </span>
            @endif
        </div>

        {{-- Desktop View (Full Pagination) --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">
                    Menampilkan 
                    @if ($paginator->firstItem())
                        <span class="font-black text-slate-900">{{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-black text-slate-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari 
                    <span class="font-black text-slate-900">{{ $paginator->total() }}</span>
                    berita
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-medium text-slate-300 bg-slate-50 border border-slate-100 rounded-xl cursor-not-allowed" aria-hidden="true">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-300 shadow-sm hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" aria-label="{{ __('pagination.previous') }}">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                <div class="flex items-center gap-1.5">
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-bold text-slate-400 cursor-default tracking-widest">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-black text-white bg-primary-600 rounded-xl shadow-md shadow-primary-600/30 cursor-default transform scale-105">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-300 shadow-sm hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-primary-50 hover:text-primary-600 hover:border-primary-300 hover:-translate-y-0.5 transition-all duration-300 shadow-sm hover:shadow-md active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" aria-label="{{ __('pagination.next') }}">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="relative inline-flex items-center justify-center w-11 h-11 text-sm font-medium text-slate-300 bg-slate-50 border border-slate-100 rounded-xl cursor-not-allowed" aria-hidden="true">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
