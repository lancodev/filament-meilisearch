<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Pages\DocumentsPage;
use Lancodev\FilamentMeilisearch\Pages\SettingsPage;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class IndexesPage extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected string $view = 'filament-meilisearch::pages.indexes';

    protected static ?string $navigationLabel = 'Indexes';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'meilisearch/indexes';

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('create')
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
                        $this->resetTable();
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

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                try {
                    $indexes = app(MeilisearchService::class)->getIndexes([
                        'allowed_indexes' => MeilisearchPlugin::get()->getAllowedIndexes(),
                    ]);

                    return array_map(function (array $index): array {
                        $index['__key'] = $index['uid'];

                        return $index;
                    }, $indexes);
                } catch (\Exception $e) {
                    return [];
                }
            })
            ->columns([
                TextColumn::make('uid')
                    ->label('UID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryKey')
                    ->label('Primary Key')
                    ->default('Not set')
                    ->placeholder('Not set'),
                TextColumn::make('createdAt')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updatedAt')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                TableAction::make('documents')
                    ->label('Documents')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (array $record): string => DocumentsPage::getUrl(['index' => $record['uid']])),
                TableAction::make('settings')
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (array $record): string => SettingsPage::getUrl(['index' => $record['uid']])),
                TableAction::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete index')
                    ->modalDescription('Are you sure you want to delete this index? This action cannot be undone.')
                    ->action(function (array $record) {
                        try {
                            app(MeilisearchService::class)->deleteIndex($record['uid']);
                            $this->resetTable();
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
                    }),
            ]);
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
