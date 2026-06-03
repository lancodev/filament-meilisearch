<x-filament-panels::page>
    <div class="space-y-4">
        @if(count($indexes) > 0)
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">UID</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Primary Key</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Created</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Updated</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($indexes as $index)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium">{{ $index['uid'] }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $index['primaryKey'] ?? 'Not set' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $index['createdAt'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $index['updatedAt'] ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ url('/admin/meilisearch/documents/' . $index['uid']) }}" class="text-primary-600 hover:text-primary-500 hover:underline">
                                        Documents
                                    </a>
                                    <a href="{{ url('/admin/meilisearch/settings/' . $index['uid']) }}" class="text-primary-600 hover:text-primary-500 hover:underline">
                                        Settings
                                    </a>
                                    <button
                                        wire:click="deleteIndex('{{ $index['uid'] }}')"
                                        wire:confirm="Are you sure you want to delete this index?"
                                        class="text-danger-600 hover:text-danger-500 hover:underline"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                <p class="text-gray-500">No indexes found.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
