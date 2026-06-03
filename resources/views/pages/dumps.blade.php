<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Create Dump</x-slot>
        
        <p class="text-gray-500 mb-4">
            A dump is a compressed file containing all your Meilisearch data.
            This includes indexes, documents, settings, and API keys.
        </p>
        
        <x-filament::button wire:click="createDump">
            Create Dump
        </x-filament::button>
    </x-filament::section>
</x-filament-panels::page>
