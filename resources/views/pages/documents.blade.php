<x-filament-panels::page>
    @if($searchResults)
    <x-filament::section class="mb-4">
        <x-slot name="heading">Search Results</x-slot>
        <pre class="text-sm bg-gray-50 dark:bg-gray-800 p-4 rounded-lg overflow-auto max-h-96">{{ json_encode($searchResults, JSON_PRETTY_PRINT) }}</pre>
        <button wire:click="$set('searchResults', null)" class="mt-2 text-primary-600 hover:underline">Clear</button>
    </x-filament::section>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
