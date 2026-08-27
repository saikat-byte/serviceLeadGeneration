@props(['links'])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 sm:space-x-4">
        <li>
            <a href="/" class="text-gray-400 hover:text-amber-600 transition">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
            </a>
        </li>
        
        @foreach($links as $label => $url)
            <li>
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    @if($loop->last || !$url)
                        <span class="ml-2 sm:ml-4 text-sm font-medium text-gray-800">{{ $label }}</span>
                    @else
                        <a href="{{ $url }}" class="ml-2 sm:ml-4 text-sm font-medium text-gray-500 hover:text-amber-600 transition">{{ $label }}</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>