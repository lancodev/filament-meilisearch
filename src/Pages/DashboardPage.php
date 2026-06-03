<?php


namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use UnitEnum;

use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;

class DashboardPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament-meilisearch::pages.dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'meilisearch';

    public ?array $health = null;

    public ?array $version = null;

    public ?array $stats = null;

    public function mount(): void
    {
        $service = app(MeilisearchService::class);

        try {
            $this->health = $service->getHealth();
            $this->version = $service->getVersion();
            $this->stats = $service->getStats();

            $this->filterStatsByAllowedIndexes();
        } catch (\Exception $e) {
            $this->health = ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function filterStatsByAllowedIndexes(): void
    {
        $allowedIndexes = MeilisearchPlugin::get()->getAllowedIndexes();

        if ($allowedIndexes === null || $allowedIndexes === []) {
            return;
        }

        if (isset($this->stats['indexes'])) {
            $this->stats['indexes'] = array_filter(
                $this->stats['indexes'],
                fn (string $uid) => in_array($uid, $allowedIndexes, true),
                ARRAY_FILTER_USE_KEY,
            );
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return MeilisearchPlugin::get()->getNavigationSort();
    }

    public function getTitle(): string
    {
        return 'Meilisearch Dashboard';
    }

    public function getViewData(): array
    {
        return [
            'health' => $this->health,
            'version' => $this->version,
            'stats' => $this->stats,
        ];
    }
}
