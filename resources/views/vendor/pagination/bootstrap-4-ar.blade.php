@if ($paginator->hasPages())
    <!-- Check if there are multiple pages to paginate -->
    <nav>
        <ul class="pagination" style="direction: rtl;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <!-- If the current page is the first one, disable the "Previous" button -->
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo; السابق</span> <!-- Display a disabled "Previous" button -->
                </li>
            @else
                <!-- If not on the first page, enable the "Previous" button -->
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo; السابق</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                <!-- Loop through the pagination elements -->
                {{-- Dots for skipped pages --}}
                @if (is_string($element))
                    <!-- If the element is a string (e.g., "..."), show it as disabled -->
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array of Links --}}
                @if (is_array($element))
                    <!-- If the element is an array, loop through and create links -->
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <!-- Highlight the current page -->
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <!-- Other pages are clickable links -->
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <!-- If there are more pages, enable the "Next" button -->
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">التالي &rsaquo;</a>
                </li>
            @else
                <!-- If on the last page, disable the "Next" button -->
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">التالي &rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
