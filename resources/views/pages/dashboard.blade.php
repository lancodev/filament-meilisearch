<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Health Status -->
        <x-filament::section>
            <x-slot name="heading">Health Status</x-slot>
            
            @if(isset($health['status']) && $health['status'] === 'available')
                <div class="flex items-center gap-2 text-success-600 dark:text-success-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Healthy</span>
                </div>
            @elseif(isset($health['status']) && $health['status'] === 'error')
                <div class="flex items-center gap-2 text-danger-600 dark:text-danger-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Error: {{ $health['message'] ?? 'Unknown error' }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400">
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
                    <span class="text-gray-500 dark:text-gray-400">Meilisearch:</span>
                    <span class="font-mono">{{ $version['pkgVersion'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Commit:</span>
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
                    <span class="text-gray-500 dark:text-gray-400">Database Size:</span>
                    <span class="font-mono">{{ isset($stats['databaseSize']) ? number_format($stats['databaseSize'] / 1024 / 1024, 2) . ' MB' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Indexes:</span>
                    <span class="font-mono">{{ count($stats['indexes'] ?? []) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Last Update:</span>
                    <span class="font-mono">{{ isset($stats['lastUpdate']) ? \Carbon\Carbon::parse($stats['lastUpdate'])->diffForHumans() : 'N/A' }}</span>
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>

    <!-- Indexes Table -->
    @if($stats && isset($stats['indexes']))
    <div class="mt-6">
        {{ $this->table }}
    </div>
    @endif
</x-filament-panels::page>
