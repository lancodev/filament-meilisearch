<?php

namespace Lancodev\FilamentMeilisearch\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Lancodev\FilamentMeilisearch\MeilisearchPlugin;
use Lancodev\FilamentMeilisearch\Services\MeilisearchService;

/**
 * @property-read Schema $form
 */
class SettingsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament-meilisearch::pages.settings';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'meilisearch/settings/{index}';

    public string $index = '';

    public ?array $data = [];

    public function mount(string $index): void
    {
        $this->index = $index;
        $this->fillFormFromSettings();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Ranking & Relevancy')
                        ->schema([
                            Repeater::make('rankingRules')
                                ->simple(TextInput::make('rule')->required())
                                ->default($this->defaultSettings()['rankingRules'])
                                ->addActionLabel('Add ranking rule'),
                            TextInput::make('distinctAttribute')
                                ->nullable(),
                            Select::make('proximityPrecision')
                                ->options([
                                    'byWord' => 'By word',
                                    'byAttribute' => 'By attribute',
                                ])
                                ->default('byWord')
                                ->required(),
                            TextInput::make('searchCutoffMs')
                                ->numeric()
                                ->minValue(0)
                                ->nullable(),
                        ])
                        ->columns(2),

                    Section::make('Searchable & Displayed Attributes')
                        ->schema([
                            Repeater::make('searchableAttributes')
                                ->simple(TextInput::make('attribute')->required())
                                ->default($this->defaultSettings()['searchableAttributes'])
                                ->addActionLabel('Add searchable attribute'),
                            Repeater::make('displayedAttributes')
                                ->simple(TextInput::make('attribute')->required())
                                ->default($this->defaultSettings()['displayedAttributes'])
                                ->addActionLabel('Add displayed attribute'),
                        ])
                        ->columns(2),

                    Section::make('Filtering & Sorting')
                        ->schema([
                            Repeater::make('filterableAttributes')
                                ->simple(TextInput::make('attribute')->required())
                                ->default([])
                                ->addActionLabel('Add filterable attribute'),
                            Repeater::make('sortableAttributes')
                                ->simple(TextInput::make('attribute')->required())
                                ->default([])
                                ->addActionLabel('Add sortable attribute'),
                        ])
                        ->columns(2),

                    Section::make('Typo Tolerance')
                        ->schema([
                            Toggle::make('typoTolerance.enabled')
                                ->default(true),
                            TextInput::make('typoTolerance.minWordSizeForTypos.oneTypo')
                                ->numeric()
                                ->minValue(0)
                                ->default(5)
                                ->required(),
                            TextInput::make('typoTolerance.minWordSizeForTypos.twoTypos')
                                ->numeric()
                                ->minValue(0)
                                ->default(9)
                                ->required(),
                            Repeater::make('typoTolerance.disableOnWords')
                                ->simple(TextInput::make('word')->required())
                                ->default([])
                                ->addActionLabel('Add word'),
                            Repeater::make('typoTolerance.disableOnAttributes')
                                ->simple(TextInput::make('attribute')->required())
                                ->default([])
                                ->addActionLabel('Add attribute'),
                            Toggle::make('typoTolerance.disableOnNumbers')
                                ->default(false),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Faceting')
                        ->schema([
                            TextInput::make('faceting.maxValuesPerFacet')
                                ->numeric()
                                ->minValue(0)
                                ->default(100)
                                ->required(),
                            KeyValue::make('faceting.sortFacetValuesBy')
                                ->keyLabel('Attribute')
                                ->valueLabel('Sort by alpha or count')
                                ->default(['*' => 'alpha']),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Pagination')
                        ->schema([
                            TextInput::make('pagination.maxTotalHits')
                                ->numeric()
                                ->minValue(0)
                                ->default(1000)
                                ->required(),
                        ])
                        ->collapsible(),

                    Section::make('Dictionaries & Tokens')
                        ->schema([
                            Repeater::make('stopWords')
                                ->simple(TextInput::make('word')->required())
                                ->default([])
                                ->addActionLabel('Add stop word'),
                            KeyValue::make('synonyms')
                                ->keyLabel('Word')
                                ->valueLabel('Comma-separated synonyms')
                                ->default([]),
                            Repeater::make('separatorTokens')
                                ->simple(TextInput::make('token')->required())
                                ->default([])
                                ->addActionLabel('Add separator token'),
                            Repeater::make('nonSeparatorTokens')
                                ->simple(TextInput::make('token')->required())
                                ->default([])
                                ->addActionLabel('Add non-separator token'),
                            Repeater::make('dictionary')
                                ->simple(TextInput::make('word')->required())
                                ->default([])
                                ->addActionLabel('Add dictionary word'),
                        ])
                        ->columns(2)
                        ->collapsible(),

                    Section::make('Localization & Advanced')
                        ->schema([
                            Repeater::make('localizedAttributes')
                                ->schema([
                                    Repeater::make('locales')
                                        ->simple(TextInput::make('locale')->required())
                                        ->default([])
                                        ->addActionLabel('Add locale'),
                                    Repeater::make('attributePatterns')
                                        ->simple(TextInput::make('attributePattern')->required())
                                        ->default([])
                                        ->addActionLabel('Add attribute pattern'),
                                ])
                                ->default([])
                                ->columns(2)
                                ->addActionLabel('Add localized attribute'),
                            Toggle::make('facetSearch')
                                ->default(true),
                            Select::make('prefixSearch')
                                ->options([
                                    'indexingTime' => 'Indexing time',
                                    'disabled' => 'Disabled',
                                ])
                                ->default('indexingTime')
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save')
                                ->label('Save settings'),
                            Action::make('resetSettings')
                                ->label('Reset to Defaults')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action('resetSettings'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->prepareSettingsForSave($this->form->getState());

            \app(MeilisearchService::class)->updateIndexSettings($this->index, $data);

            Notification::make()
                ->title('Settings updated')
                ->success()
                ->send();

            $this->fillFormFromSettings();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetSettings(): void
    {
        try {
            \app(MeilisearchService::class)->resetIndexSettings($this->index);

            Notification::make()
                ->title('Settings reset to defaults')
                ->success()
                ->send();

            $this->fillFormFromSettings();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to reset settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function fillFormFromSettings(): void
    {
        try {
            $settings = \app(MeilisearchService::class)->getIndexSettings($this->index);

            $this->form->fill($this->prepareSettingsForFill($settings));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to load settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function prepareSettingsForFill(array $settings): array
    {
        $settings = array_replace_recursive($this->defaultSettings(), $settings);

        $settings['synonyms'] = \collect($settings['synonyms'] ?? [])
            ->map(fn (array|string $synonyms): string => is_array($synonyms) ? implode(', ', $synonyms) : $synonyms)
            ->all();

        return $settings;
    }

    protected function prepareSettingsForSave(array $settings): array
    {
        $settings['rankingRules'] = $this->cleanList($settings['rankingRules'] ?? []);
        $settings['searchableAttributes'] = $this->cleanList($settings['searchableAttributes'] ?? []);
        $settings['displayedAttributes'] = $this->cleanList($settings['displayedAttributes'] ?? []);
        $settings['filterableAttributes'] = $this->cleanList($settings['filterableAttributes'] ?? []);
        $settings['sortableAttributes'] = $this->cleanList($settings['sortableAttributes'] ?? []);
        $settings['stopWords'] = $this->cleanList($settings['stopWords'] ?? []);
        $settings['separatorTokens'] = $this->cleanList($settings['separatorTokens'] ?? []);
        $settings['nonSeparatorTokens'] = $this->cleanList($settings['nonSeparatorTokens'] ?? []);
        $settings['dictionary'] = $this->cleanList($settings['dictionary'] ?? []);

        $settings['distinctAttribute'] = $this->nullableString($settings['distinctAttribute'] ?? null);
        $settings['searchCutoffMs'] = $this->nullableInteger($settings['searchCutoffMs'] ?? null);

        $settings['typoTolerance']['enabled'] = (bool) ($settings['typoTolerance']['enabled'] ?? true);
        $settings['typoTolerance']['minWordSizeForTypos']['oneTypo'] = $this->integer($settings['typoTolerance']['minWordSizeForTypos']['oneTypo'] ?? 5);
        $settings['typoTolerance']['minWordSizeForTypos']['twoTypos'] = $this->integer($settings['typoTolerance']['minWordSizeForTypos']['twoTypos'] ?? 9);
        $settings['typoTolerance']['disableOnWords'] = $this->cleanList($settings['typoTolerance']['disableOnWords'] ?? []);
        $settings['typoTolerance']['disableOnAttributes'] = $this->cleanList($settings['typoTolerance']['disableOnAttributes'] ?? []);
        $settings['typoTolerance']['disableOnNumbers'] = (bool) ($settings['typoTolerance']['disableOnNumbers'] ?? false);

        $settings['faceting']['maxValuesPerFacet'] = $this->integer($settings['faceting']['maxValuesPerFacet'] ?? 100);
        $settings['faceting']['sortFacetValuesBy'] = (object) $this->cleanKeyValue($settings['faceting']['sortFacetValuesBy'] ?? []);

        $settings['pagination']['maxTotalHits'] = $this->integer($settings['pagination']['maxTotalHits'] ?? 1000);

        $settings['synonyms'] = (object) \collect($settings['synonyms'] ?? [])
            ->mapWithKeys(function (?string $synonyms, string $word): array {
                $word = trim($word);

                if ($word === '') {
                    return [];
                }

                return [$word => \collect(explode(',', $synonyms ?? ''))
                    ->map(fn (string $synonym): string => trim($synonym))
                    ->filter(fn (string $synonym): bool => $synonym !== '')
                    ->values()
                    ->all()];
            })
            ->all();

        $settings['localizedAttributes'] = \collect($settings['localizedAttributes'] ?? [])
            ->map(fn (array $localizedAttribute): array => [
                'locales' => $this->cleanList($localizedAttribute['locales'] ?? []),
                'attributePatterns' => $this->cleanList($localizedAttribute['attributePatterns'] ?? []),
            ])
            ->filter(fn (array $localizedAttribute): bool => $localizedAttribute['locales'] !== [] || $localizedAttribute['attributePatterns'] !== [])
            ->values()
            ->all();

        $settings['facetSearch'] = (bool) ($settings['facetSearch'] ?? true);

        return array_intersect_key($settings, $this->defaultSettings());
    }

    protected function defaultSettings(): array
    {
        return [
            'rankingRules' => ['words', 'typo', 'proximity', 'attributeRank', 'sort', 'wordPosition', 'exactness'],
            'distinctAttribute' => null,
            'proximityPrecision' => 'byWord',
            'searchCutoffMs' => null,
            'searchableAttributes' => ['*'],
            'displayedAttributes' => ['*'],
            'filterableAttributes' => [],
            'sortableAttributes' => [],
            'typoTolerance' => [
                'enabled' => true,
                'minWordSizeForTypos' => [
                    'oneTypo' => 5,
                    'twoTypos' => 9,
                ],
                'disableOnWords' => [],
                'disableOnAttributes' => [],
                'disableOnNumbers' => false,
            ],
            'faceting' => [
                'maxValuesPerFacet' => 100,
                'sortFacetValuesBy' => ['*' => 'alpha'],
            ],
            'pagination' => [
                'maxTotalHits' => 1000,
            ],
            'stopWords' => [],
            'synonyms' => [],
            'separatorTokens' => [],
            'nonSeparatorTokens' => [],
            'dictionary' => [],
            'localizedAttributes' => [],
            'facetSearch' => true,
            'prefixSearch' => 'indexingTime',
        ];
    }

    protected function cleanList(array $values): array
    {
        return \collect($values)
            ->map(fn (mixed $value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->values()
            ->all();
    }

    protected function cleanKeyValue(array $values): array
    {
        return \collect($values)
            ->mapWithKeys(function (mixed $value, string|int $key): array {
                $key = trim((string) $key);
                $value = trim((string) $value);

                return $key === '' ? [] : [$key => $value];
            })
            ->all();
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function nullableInteger(mixed $value): ?int
    {
        return $value === '' || $value === null ? null : (int) $value;
    }

    protected function integer(mixed $value): int
    {
        return (int) $value;
    }

    public static function getNavigationGroup(): ?string
    {
        return MeilisearchPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return "Settings: {$this->index}";
    }
}
