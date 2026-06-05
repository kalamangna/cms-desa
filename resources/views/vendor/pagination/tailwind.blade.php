@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-slate-400 bg-white border border-slate-100 cursor-default rounded-2xl shadow-sm">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-100 rounded-2xl shadow-sm hover:text-emerald-600 transition">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-slate-700 bg-white border border-slate-100 rounded-2xl shadow-sm hover:text-emerald-600 transition">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-6 py-3 text-sm font-bold text-slate-400 bg-white border border-slate-100 cursor-default rounded-2xl shadow-sm">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">
                    {!! __('Menampilkan') !!}
                    @if ($paginator->firstItem())
                        <span class="font-black text-slate-900">{{ $paginator->firstItem() }}</span>
                        {!! __('sampai') !!}
                        <span class="font-black text-slate-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('dari') !!}
                    <span class="font-black text-slate-900">{{ $paginator->total() }}</span>
                    {!! __('hasil') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-2xl overflow-hidden border border-slate-100 bg-white">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-4 py-4 text-sm font-medium text-slate-300 bg-white cursor-default" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left w-5 h-5 flex items-center justify-center"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-4 text-sm font-medium text-slate-500 bg-white hover:bg-emerald-50 hover:text-emerald-600 transition" aria-label="{{ __('pagination.previous') }}">
                            <i class="fa-solid fa-chevron-left w-5 h-5 flex items-center justify-center"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-6 py-4 -ml-px text-sm font-bold text-slate-400 bg-white cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-6 py-4 -ml-px text-sm font-black bg-emerald-600 text-white cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-6 py-4 -ml-px text-sm font-bold text-slate-600 bg-white hover:bg-emerald-50 hover:text-emerald-600 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-4 -ml-px text-sm font-medium text-slate-500 bg-white hover:bg-emerald-50 hover:text-emerald-600 transition" aria-label="{{ __('pagination.next') }}">
                            <i class="fa-solid fa-chevron-right w-5 h-5 flex items-center justify-center"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-4 py-4 -ml-px text-sm font-medium text-slate-300 bg-white cursor-default" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right w-5 h-5 flex items-center justify-center"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
