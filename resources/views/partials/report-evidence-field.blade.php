{{-- Paste-to-upload evidence (shared by customer + hub-owner report forms). $prefix = hidden textarea id, e.g. report-evidence or report-customer-evidence --}}
@php
    $prefix = $prefix ?? 'report-evidence';
@endphp
<div>
    <label for="{{ $prefix }}-dropzone" class="block text-sm font-medium text-gray-700 mb-1">Evidence <span class="text-red-500">*</span></label>
    <div id="{{ $prefix }}-dropzone"
         tabindex="0"
         role="region"
         aria-label="Paste evidence screenshot here"
         class="relative min-h-[140px] rounded-md border-2 border-dashed border-gray-300 bg-gray-50 p-4 text-center outline-none transition-colors hover:border-orange-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
        <p id="{{ $prefix }}-placeholder" class="text-sm text-gray-500 pointer-events-none select-none">Paste Evidence here</p>
        <div id="{{ $prefix }}-loading" class="hidden py-6 text-sm text-orange-700">
            <span class="inline-block h-5 w-5 animate-spin rounded-full border-2 border-orange-300 border-t-orange-600 align-middle mr-2"></span>
            Processing image…
        </div>
        <img id="{{ $prefix }}-preview" src="" alt="Evidence preview" class="mx-auto hidden max-h-48 max-w-full rounded-lg border border-gray-200 object-contain shadow-sm">
        <button type="button" id="{{ $prefix }}-clear" class="mt-3 hidden text-sm font-medium text-orange-600 hover:text-orange-800 underline">
            Remove image
        </button>
    </div>
    <textarea name="evidence" id="{{ $prefix }}" minlength="10" maxlength="400000" tabindex="-1" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;overflow:hidden" aria-label="Evidence data for submit"></textarea>
    <p id="{{ $prefix }}-error" class="mt-1 hidden text-sm text-red-600">Please paste a screenshot as evidence (click the box and press Ctrl+V).</p>
    @isset($showServerErrors)
        @error('evidence')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    @endisset
</div>
