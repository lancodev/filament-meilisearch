<x-filament-panels::page>
    <div class="mb-4">
        <x-filament::badge size="lg">
            Index: {{ $index }}
        </x-filament::badge>
    </div>

    @if($searchResults)
    <x-filament::section class="mb-4">
        <x-slot name="heading">Search Results</x-slot>
        <pre class="text-sm bg-gray-50 dark:bg-gray-800 p-4 rounded-lg overflow-auto max-h-96">{{ json_encode($searchResults, JSON_PRETTY_PRINT) }}</pre>
        <button wire:click="$set('searchResults', null)" class="mt-2 text-primary-600 hover:underline">Clear</button>
    </x-filament::section>
    @endif

    <div class="space-y-4">
        @if(count($documents) > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">ID</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Data</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($documents as $doc)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-mono text-xs">{{ $doc['id'] ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded overflow-auto max-h-32">{{ json_encode($doc, JSON_PRETTY_PRINT) }}</pre>
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    wire:click="deleteDocument('{{ $doc['id'] ?? '' }}')"
                                    wire:confirm="Are you sure?"
                                    class="text-danger-600 hover:text-danger-500 hover:underline"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <p class="text-gray-500">No documents found.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
