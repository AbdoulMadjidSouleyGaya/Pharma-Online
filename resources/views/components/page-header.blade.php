@props([
    'title',
    'subtitle' => null,
    'backRoute' => null,
    'backLabel' => 'Retour',
])

<div class="mb-6 border-b border-slate-200 pb-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-3">
                @if($backRoute)
                    <a href="{{ $backRoute }}"
                       class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ $backLabel }}
                    </a>
                @endif

                <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $title }}
                </h2>
            </div>

            @if($subtitle)
                <p class="mt-1 text-sm text-slate-500">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        @if(trim($slot) !== '')
            <div class="flex flex-wrap gap-2">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
