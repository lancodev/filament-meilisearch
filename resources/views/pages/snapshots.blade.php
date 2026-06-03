<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Create Snapshot</x-slot>
        
        <p class="text-gray-500 mb-4">
            A snapshot is an exact copy of the Meilisearch database at a specific point in time.
            Snapshots are useful for backups and can be used to restore your instance.
        </p>
        
        <x-filament::button wire:click="createSnapshot">
            Create Snapshot
        </x-filament::button>
    </x-filament::section>
</x-filament-panels::page>
