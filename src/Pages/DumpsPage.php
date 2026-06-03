<?php


namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use UnitEnum;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class DumpsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';

    protected string $view = 'filament-meilisearch::pages.dumps';

    protected static ?string $navigationLabel = 'Dumps';

    protected static ?int $navigationSort = 7;

    protected static ?string $slug = 'meilisearch/dumps';

    public function createDump(): void
    {
        try {
            $result = app(MeilisearchService::class)->createDump();
            Notification::make()
                ->title('Dump created')
                ->body("Task UID: {$result['taskUid']}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to create dump')
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
        return 'Dumps';
    }
}
