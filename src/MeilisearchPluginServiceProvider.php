<?php

namespace Lancodev\FilamentMeilisearch;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MeilisearchPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-meilisearch';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            Services\MeilisearchService::class,
            function ($app) {
                return new Services\MeilisearchService(
                    config('filament-meilisearch.host'),
                    config('filament-meilisearch.key'),
                );
            }
        );
    }

    public function packageBooted(): void
    {
        if (class_exists(FilamentAsset::class)) {
            $cssPath = __DIR__ . '/../resources/dist/filament-meilisearch.css';
            $jsPath = __DIR__ . '/../resources/dist/filament-meilisearch.js';

            $assets = [];

            if (file_exists($cssPath)) {
                $assets[] = Css::make('filament-meilisearch', $cssPath);
            }

            if (file_exists($jsPath)) {
                $assets[] = Js::make('filament-meilisearch', $jsPath);
            }

            if (! empty($assets)) {
                FilamentAsset::register($assets, 'lancodev/filament-meilisearch');
            }
        }
    }
}
