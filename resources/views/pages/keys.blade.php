<x-filament-panels::page>
    <div class="space-y-4">
        @if(count($keys) > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Name</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Description</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Key</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Expires</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($keys as $key)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3">{{ $key['name'] ?? 'No name' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $key['description'] ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $key['key'] ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if(isset($key['actions']))
                                    <span class="text-xs">{{ implode(', ', (array) $key['actions']) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $key['expiresAt'] ?? 'Never' }}</td>
                            <td class="px-4 py-3">
                                <button
                                    wire:click="deleteKey('{{ $key['uid'] ?? $key['key'] }}')"
                                    wire:confirm="Are you sure you want to delete this key?"
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
                <p class="text-gray-500">No API keys found.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
