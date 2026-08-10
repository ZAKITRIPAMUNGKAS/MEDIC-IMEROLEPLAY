@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-sky-200/50 bg-white/5 border border-white/10 rounded-xl cursor-default select-none">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition-all shadow-sm">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition-all shadow-sm">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-xs font-semibold text-sky-200/50 bg-white/5 border border-white/10 rounded-xl cursor-default select-none">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-sky-200/90 font-medium">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-bold text-white">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-bold text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-bold text-white">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1.5 p-1.5 bg-slate-950/70 backdrop-blur-md border border-white/15 rounded-2xl shadow-xl">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-sky-200/30 bg-white/5 border border-white/5 rounded-xl cursor-default select-none" aria-hidden="true">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-white bg-white/10 hover:bg-sky-500/30 hover:border-sky-400/50 border border-white/15 rounded-xl transition-all duration-200 shadow-sm" aria-label="{{ __('pagination.previous') }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-sky-300/50 cursor-default select-none">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-black text-white bg-gradient-to-r from-sky-500 to-cyan-500 border border-sky-300/40 rounded-xl shadow-[0_0_12px_rgba(14,165,233,0.4)] cursor-default select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-sky-100/90 bg-white/5 hover:bg-white/15 hover:text-white hover:border-sky-400/40 border border-white/10 rounded-xl transition-all duration-200" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-white bg-white/10 hover:bg-sky-500/30 hover:border-sky-400/50 border border-white/15 rounded-xl transition-all duration-200 shadow-sm" aria-label="{{ __('pagination.next') }}">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-sky-200/30 bg-white/5 border border-white/5 rounded-xl cursor-default select-none" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
