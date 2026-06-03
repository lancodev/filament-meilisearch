<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Pages\DocumentsPage;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class DashboardPage extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

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

    public function table(Table $table): Table
    {
        return $table
            ->heading('Indexes Overview')
            ->records(function () {
                if (! $this->stats || ! isset($this->stats['indexes'])) {
                    return [];
                }

                return collect($this->stats['indexes'])->map(function (array $stats, string $uid): array {
                    return [
                        '__key' => $uid,
                        'uid' => $uid,
                        'numberOfDocuments' => $stats['numberOfDocuments'] ?? 0,
                        'size' => $stats['size'] ?? null,
                        'isIndexing' => $stats['isIndexing'] ?? false,
                    ];
                })->values()->all();
            })
            ->columns([
                TextColumn::make('uid')
                    ->label('Index')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('numberOfDocuments')
                    ->label('Documents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? number_format($state / 1024, 2).' KB' : 'N/A')
                    ->placeholder('N/A'),
                TextColumn::make('isIndexing')
                    ->label('Is Indexing')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (array $record): string => DocumentsPage::getUrl(['index' => $record['uid']])),
            ]);
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
