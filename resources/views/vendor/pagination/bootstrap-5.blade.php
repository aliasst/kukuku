@if ($paginator->hasPages())
    <div class="d-flex flex-column align-items-center mt-4">
        <ul class="pagination m-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">← Назад</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Назад</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Вперед →</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Вперед →</span>
                </li>
            @endif
        </ul>

        <div class="text-muted mt-2" style="font-size: 12px;">
            Показано с {{ $paginator->firstItem() }} по {{ $paginator->lastItem() }} из {{ $paginator->total() }} записей
        </div>
    </div>
@endif
