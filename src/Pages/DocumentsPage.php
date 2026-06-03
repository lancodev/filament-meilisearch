<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class DocumentsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament-meilisearch::pages.documents';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'meilisearch/documents/{index}';

    public string $index = '';

    public ?array $searchResults = null;

    public function mount(string $index): void
    {
        $this->index = $index;
    }

    public function addDocuments(string $json): void
    {
        try {
            $documents = json_decode($json, true);
            app(MeilisearchService::class)->addDocuments($this->index, $documents);
            Notification::make()
                ->title('Documents added')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to add documents')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function searchDocuments(string $query): void
    {
        try {
            $this->searchResults = app(MeilisearchService::class)->search($this->index, $query);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Search failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                try {
                    $result = app(MeilisearchService::class)->getDocuments($this->index, [
                        'limit' => 100,
                    ]);

                    return array_map(function (array $document): array {
                        $document['__key'] = (string) ($document['id'] ?? '');

                        return $document;
                    }, $result['results'] ?? []);
                } catch (\Exception $e) {
                    return [];
                }
            })
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('data')
                    ->label('Data')
                    ->state(function (array $record): string {
                        $clean = $record;
                        unset($clean['__key']);

                        return json_encode($clean, JSON_PRETTY_PRINT);
                    })
                    ->limit(100)
                    ->wrap()
                    ->fontFamily('mono')
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->hidden(false)
                    ->requiresConfirmation()
                    ->modalHeading('Delete document')
                    ->modalDescription('Are you sure you want to delete this document?')
                    ->action(function (array $record) {
                        try {
                            app(MeilisearchService::class)->deleteDocument($this->index, $record['id'] ?? '');
                            $this->resetTable();
                            Notification::make()
                                ->title('Document deleted')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to delete document')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add')
                ->form([
                    Textarea::make('documents')
                        ->required()
                        ->rows(10)
                        ->placeholder('Enter JSON documents array')
                        ->rules(['json']),
                ])
                ->action(function (array $data) {
                    $this->addDocuments($data['documents']);
                }),
            Action::make('search')
                ->form([
                    TextInput::make('query')
                        ->required()
                        ->placeholder('Search query...'),
                ])
                ->action(function (array $data) {
                    $this->searchDocuments($data['query']);
                }),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return "Documents: {$this->index}";
    }
}
