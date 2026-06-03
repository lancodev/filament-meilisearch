<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class KeysPage extends Page implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-key';

    protected string $view = 'filament-meilisearch::pages.keys';

    protected static ?string $navigationLabel = 'API Keys';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'meilisearch/keys';

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
                            $indexes = app(MeilisearchService::class)->getIndexes([
                                'allowed_indexes' => MeilisearchPlugin::get()->getAllowedIndexes(),
                            ]);

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
                        $this->resetTable();
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

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                try {
                    $keys = app(MeilisearchService::class)->getKeys();

                    return array_map(function (array $key): array {
                        $key['__key'] = $key['uid'];

                        return $key;
                    }, $keys);
                } catch (\Exception $e) {
                    return [];
                }
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->default('No name')
                    ->placeholder('No name'),
                TextColumn::make('description')
                    ->label('Description')
                    ->default('-')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('key')
                    ->label('Key')
                    ->fontFamily('mono')
                    ->limit(12)
                    ->tooltip(fn (array $record): string => $record['key'] ?? '')
                    ->copyable()
                    ->copyMessage('Key copied!'),
                TextColumn::make('actions')
                    ->label('Actions')
                    ->formatStateUsing(fn (array $record): string => implode(', ', (array) ($record['actions'] ?? [])))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('indexes')
                    ->label('Indexes')
                    ->formatStateUsing(fn (array $record): string => implode(', ', (array) ($record['indexes'] ?? [])))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expiresAt')
                    ->label('Expires')
                    ->state(function (array $record): ?string {
                        $value = $record['expiresAt'] ?? null;

                        return filled($value) ? $value : null;
                    })
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete API key')
                    ->modalDescription('Are you sure you want to delete this key? This action cannot be undone.')
                    ->action(function (array $record) {
                        try {
                            app(MeilisearchService::class)->deleteKey($record['uid'] ?? $record['key']);
                            $this->resetTable();
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
                    }),
            ]);
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
