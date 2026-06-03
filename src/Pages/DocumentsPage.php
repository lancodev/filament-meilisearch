<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class DocumentsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament-meilisearch::pages.documents';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'meilisearch/documents/{index}';

    public string $index = '';

    public array $documents = [];

    public ?array $searchResults = null;

    public function mount(string $index): void
    {
        $this->index = $index;
        $this->loadDocuments();
    }

    public function loadDocuments(): void
    {
        try {
            $result = app(MeilisearchService::class)->getDocuments($this->index, [
                'limit' => 100,
            ]);
            $this->documents = $result['results'] ?? [];
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load documents')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteDocument(string|int $documentId): void
    {
        try {
            app(MeilisearchService::class)->deleteDocument($this->index, $documentId);
            $this->loadDocuments();
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
    }

    public function addDocuments(string $json): void
    {
        try {
            $documents = json_decode($json, true);
            app(MeilisearchService::class)->addDocuments($this->index, $documents);
            $this->loadDocuments();
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
