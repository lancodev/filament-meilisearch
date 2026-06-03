<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

class TasksPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-queue-list';

    protected string $view = 'filament-meilisearch::pages.tasks';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'meilisearch/tasks';

    public array $tasks = [];

    public function mount(): void
    {
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        try {
            $this->tasks = app(MeilisearchService::class)->getTasks([
                'limit' => 100,
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load tasks')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteCompletedTasks(): void
    {
        try {
            app(MeilisearchService::class)->deleteTasks([
                'statuses' => ['succeeded', 'failed'],
            ]);
            $this->loadTasks();
            Notification::make()
                ->title('Completed tasks deleted')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to delete tasks')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete_completed')
                ->requiresConfirmation()
                ->action(function () {
                    $this->deleteCompletedTasks();
                }),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return 'Tasks';
    }
}
