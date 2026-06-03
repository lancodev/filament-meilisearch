<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class IndexesPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected string $view = 'filament-meilisearch::pages.indexes';

    protected static ?string $navigationLabel = 'Indexes';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'meilisearch/indexes';

    public array $indexes = [];

    public function mount(): void
    {
        $this->loadIndexes();
    }

    public function loadIndexes(): void
    {
        try {
            $this->indexes = app(MeilisearchService::class)->getIndexes();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load indexes')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteIndex(string $uid): void
    {
        try {
            app(MeilisearchService::class)->deleteIndex($uid);
            $this->loadIndexes();
            Notification::make()
                ->title('Index deleted')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete index')
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
                    TextInput::make('uid')
                        ->required()
                        ->placeholder('e.g., products'),
                    TextInput::make('primaryKey')
                        ->placeholder('e.g., id'),
                ])
                ->action(function (array $data) {
                    try {
                        app(MeilisearchService::class)->createIndex(
                            $data['uid'],
                            $data['primaryKey'] ?? null,
                        );
                        $this->loadIndexes();
                        Notification::make()
                            ->title('Index created')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to create index')
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
        return 'Indexes';
    }
}
