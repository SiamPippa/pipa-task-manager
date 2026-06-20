@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-2">
        @if ($paginator->firstItem())
            <p class="small text-muted mb-0">
                {!! __('Showing') !!}
                <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="fw-semibold">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        @endif

        <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item first disabled">
                    <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                </li>
                <li class="page-item prev disabled">
                    <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                </li>
            @else
                <li class="page-item first">
                    <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="@lang('pagination.first')">
                        <i class="tf-icon bx bx-chevrons-left"></i>
                    </a>
                </li>
                <li class="page-item prev">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="tf-icon bx bx-chevron-left"></i>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="tf-icon bx bx-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item last">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="@lang('pagination.last')">
                        <i class="tf-icon bx bx-chevrons-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item next disabled">
                    <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                </li>
                <li class="page-item last disabled">
                    <span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
