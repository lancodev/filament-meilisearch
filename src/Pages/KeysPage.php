<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class KeysPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected string $view = 'filament-meilisearch::pages.keys';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'meilisearch/keys';

    public array $keys = [];

    public function mount(): void
    {
        $this->loadKeys();
    }

    public function loadKeys(): void
    {
        try {
            $this->keys = app(MeilisearchService::class)->getKeys();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load keys')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteKey(string $keyOrUid): void
    {
        try {
            app(MeilisearchService::class)->deleteKey($keyOrUid);
            $this->loadKeys();
            Notification::make()
                ->title('Key deleted')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete key')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->form([
                    TextInput::make('name')
                        ->placeholder('Production search key'),
                    TextInput::make('description')
                        ->placeholder('Key description'),
                    Select::make('actions')
                        ->multiple()
                        ->options([
                            'search' => 'Search',
                            'documents.add' => 'Add Documents',
                            'documents.get' => 'Get Documents',
                            'documents.delete' => 'Delete Documents',
                            'indexes.create' => 'Create Indexes',
                            'indexes.get' => 'Get Indexes',
                            'indexes.update' => 'Update Indexes',
                            'indexes.delete' => 'Delete Indexes',
                            'tasks.get' => 'Get Tasks',
                            'settings.get' => 'Get Settings',
                            'settings.update' => 'Update Settings',
                            'stats.get' => 'Get Stats',
                            'dumps.create' => 'Create Dumps',
                            'snapshots.create' => 'Create Snapshots',
                        ])
                        ->required(),
                    Select::make('indexes')
                        ->multiple()
                        ->options(function () {
                            $indexes = app(MeilisearchService::class)->getIndexes();

                            return collect($indexes)->pluck('uid', 'uid')->toArray();
                        })
                        ->placeholder('All indexes'),
                    DateTimePicker::make('expiresAt')
                        ->label('Expires At')
                        ->placeholder('Never expires'),
                ])
                ->action(function (array $data) {
                    try {
                        app(MeilisearchService::class)->createKey($data);
                        $this->loadKeys();
                        Notification::make()
                            ->title('Key created')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to create key')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return 'API Keys';
    }
}
