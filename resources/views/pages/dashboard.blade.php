<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Health Status -->
        <x-filament::section>
            <x-slot name="heading">Health Status</x-slot>
            
            @if(isset($health['status']) && $health['status'] === 'available')
                <div class="flex items-center gap-2 text-success-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Healthy</span>
                </div>
            @elseif(isset($health['status']) && $health['status'] === 'error')
                <div class="flex items-center gap-2 text-danger-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Error: {{ $health['message'] ?? 'Unknown error' }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 text-warning-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Checking...</span>
                </div>
            @endif
        </x-filament::section>

        <!-- Version -->
        @if($version)
        <x-filament::section>
            <x-slot name="heading">Version</x-slot>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Meilisearch:</span>
                    <span class="font-mono">{{ $version['pkgVersion'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Commit:</span>
                    <span class="font-mono text-sm">{{ $version['commitSha'] ?? 'Unknown' }}</span>
                </div>
            </div>
        </x-filament::section>
        @endif

        <!-- Stats -->
        @if($stats)
        <x-filament::section>
            <x-slot name="heading">Statistics</x-slot>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Database Size:</span>
                    <span class="font-mono">{{ isset($stats['databaseSize']) ? number_format($stats['databaseSize'] / 1024 / 1024, 2) . ' MB' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Indexes:</span>
                    <span class="font-mono">{{ count($stats['indexes'] ?? []) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Last Update:</span>
                    <span class="font-mono">{{ isset($stats['lastUpdate']) ? \Carbon\Carbon::parse($stats['lastUpdate'])->diffForHumans() : 'N/A' }}</span>
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>

    <!-- Indexes List -->
    @if($stats && isset($stats['indexes']))
    <x-filament::section class="mt-6">
        <x-slot name="heading">Indexes Overview</x-slot>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Index</th>
                        <th class="px-6 py-3">Documents</th>
                        <th class="px-6 py-3">Size</th>
                        <th class="px-6 py-3">Is Indexing</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['indexes'] as $uid => $indexStats)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium">{{ $uid }}</td>
                        <td class="px-6 py-4">{{ $indexStats['numberOfDocuments'] ?? 0 }}</td>
                        <td class="px-6 py-4">{{ isset($indexStats['size']) ? number_format($indexStats['size'] / 1024, 2) . ' KB' : 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($indexStats['isIndexing'] ?? false)
                                <span class="px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded">Yes</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ \Lancodev\FilamentMeilisearch\Pages\DocumentsPage::getUrl(['index' => $uid]) }}" class="text-primary-600 hover:underline">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif
</x-filament-panels::page>
