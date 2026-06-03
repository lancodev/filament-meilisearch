<?php


namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use UnitEnum;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class SnapshotsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-camera';

    protected string $view = 'filament-meilisearch::pages.snapshots';

    protected static ?string $navigationLabel = 'Snapshots';

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'meilisearch/snapshots';

    public function createSnapshot(): void
    {
        try {
            $result = app(MeilisearchService::class)->createSnapshot();
            Notification::make()
                ->title('Snapshot created')
                ->body("Task UID: {$result['taskUid']}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create snapshot')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return 'Snapshots';
    }
}
