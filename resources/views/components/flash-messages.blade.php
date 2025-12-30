@php
    $ok     = session('ok');
    $hint   = session('hint');
    $error  = session('error');
    $status = session('status');
@endphp

@if($ok || $hint || $error || $status)
    <div class="mb-4 space-y-2">
        @if($ok)
            <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <span class="mt-0.5 text-lg">✅</span>
                <div class="flex-1">
                    {{ $ok }}
                </div>
                <button type="button" onclick="this.closest('div').remove()"
                        class="text-emerald-500 hover:text-emerald-700">
                    ✕
                </button>
            </div>
        @endif

        @if($hint || $status)
            <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <span class="mt-0.5 text-lg">💡</span>
                <div class="flex-1">
                    {{ $hint ?? $status }}
                </div>
                <button type="button" onclick="this.closest('div').remove()"
                        class="text-amber-500 hover:text-amber-700">
                    ✕
                </button>
            </div>
        @endif

        @if($error)
            <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <span class="mt-0.5 text-lg">⚠️</span>
                <div class="flex-1">
                    {{ $error }}
                </div>
                <button type="button" onclick="this.closest('div').remove()"
                        class="text-rose-500 hover:text-rose-700">
                    ✕
                </button>
            </div>
        @endif
    </div>
@endif
