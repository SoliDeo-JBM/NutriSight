@props(['paginator'])

@if($paginator->hasPages())
@php
$lastPage = $paginator->lastPage();
$currentPage = $paginator->currentPage();
$windowSize = 10;
$startPage = max(1, min($currentPage - intdiv($windowSize, 2), $lastPage - $windowSize + 1));
$endPage = min($lastPage, $startPage + $windowSize - 1);
@endphp

<nav class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
    <p class="text-sm text-gray-600">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </p>

    <div class="flex items-center justify-center gap-1" role="navigation">
        @if($paginator->onFirstPage())
        <span class="inline-flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-gray-400" aria-hidden="true">
            <i class="fas fa-chevron-left text-xs"></i>
        </span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="inline-flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        @endif

        @foreach(range($startPage, $endPage) as $page)
        @if($page === $currentPage)
        <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded bg-slate-800 px-3 text-sm font-semibold text-white">{{ $page }}</span>
        @else
        <a href="{{ $paginator->url($page) }}" aria-label="Go to page {{ $page }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-300 px-3 text-sm text-gray-700 hover:bg-gray-100">{{ $page }}</a>
        @endif
        @endforeach

        @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chevron-right text-xs"></i>
        </a>
        @else
        <span class="inline-flex h-9 w-9 items-center justify-center rounded border border-gray-300 text-gray-400" aria-hidden="true">
            <i class="fas fa-chevron-right text-xs"></i>
        </span>
        @endif
    </div>
</nav>
@endif