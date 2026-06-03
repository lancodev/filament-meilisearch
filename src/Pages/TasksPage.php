<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use BackedEnum;
use Filament\Actions\Action;
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

class TasksPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected string $view = 'filament-meilisearch::pages.tasks';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'meilisearch/tasks';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete_completed')
                ->label('Delete Completed')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        app(MeilisearchService::class)->deleteTasks([
                            'statuses' => ['succeeded', 'failed'],
                        ]);
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
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                try {
                    $tasks = app(MeilisearchService::class)->getTasks([
                        'limit' => 100,
                    ]);

                    return array_map(function (array $task): array {
                        $task['__key'] = $task['uid'];

                        return $task;
                    }, $tasks);
                } catch (\Exception $e) {
                    return [];
                }
            })
            ->columns([
                TextColumn::make('uid')
                    ->label('Task ID')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'succeeded' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'enqueued' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('type')
                    ->label('Type'),
                TextColumn::make('indexUid')
                    ->label('Index')
                    ->default('-')
                    ->placeholder('-'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state || ! is_string($state)) {
                            return '-';
                        }

                        try {
                            $interval = new \DateInterval($state);
                            $seconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s + $interval->f;

                            return round($seconds, 2).'s';
                        } catch (\Exception $e) {
                            return $state;
                        }
                    }),
                TextColumn::make('enqueuedAt')
                    ->label('Enqueued')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('enqueuedAt', 'desc');
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
