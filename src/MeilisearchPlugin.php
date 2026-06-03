<?php

namespace Lancodev\FilamentMeilisearch;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Lancodev\FilamentMeilisearch\Pages\DashboardPage;
use Lancodev\FilamentMeilisearch\Pages\IndexesPage;
use Lancodev\FilamentMeilisearch\Pages\DocumentsPage;
use Lancodev\FilamentMeilisearch\Pages\KeysPage;
use Lancodev\FilamentMeilisearch\Pages\TasksPage;
use Lancodev\FilamentMeilisearch\Pages\DumpsPage;
use Lancodev\FilamentMeilisearch\Pages\SnapshotsPage;
use Lancodev\FilamentMeilisearch\Pages\SettingsPage;

class MeilisearchPlugin implements Plugin
{
    protected array $features = [];

    protected ?string $navigationGroup = null;

    protected ?string $navigationIcon = null;

    protected ?int $navigationSort = null;

    public function getId(): string
    {
        return 'meilisearch';
    }

    public function register(Panel $panel): void
    {
        $pages = [
            DashboardPage::class,
        ];

        if ($this->isFeatureEnabled('indexes')) {
            $pages[] = IndexesPage::class;
            $pages[] = DocumentsPage::class;
        }

        if ($this->isFeatureEnabled('keys')) {
            $pages[] = KeysPage::class;
        }

        if ($this->isFeatureEnabled('tasks')) {
            $pages[] = TasksPage::class;
        }

        if ($this->isFeatureEnabled('dumps')) {
            $pages[] = DumpsPage::class;
        }

        if ($this->isFeatureEnabled('snapshots')) {
            $pages[] = SnapshotsPage::class;
        }

        if ($this->isFeatureEnabled('settings')) {
            $pages[] = SettingsPage::class;
        }

        $panel
            ->pages($pages);
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function features(array $features): static
    {
        $this->features = $features;

        return $this;
    }

    public function getFeatures(): array
    {
        return $this->features ?: config('filament-meilisearch.features', []);
    }

    public function isFeatureEnabled(string $feature): bool
    {
        $features = $this->getFeatures();

        return $features[$feature] ?? true;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-meilisearch.navigation.group', 'Meilisearch');
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon ?? config('filament-meilisearch.navigation.icon', 'heroicon-o-magnifying-glass');
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? config('filament-meilisearch.navigation.sort', 0);
    }
}
