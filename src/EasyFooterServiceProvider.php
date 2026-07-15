<?php

namespace Devonab\FilamentEasyFooter;

use Devonab\FilamentEasyFooter\Livewire\GitHubVersion;
use Devonab\FilamentEasyFooter\Livewire\ProjectVersion;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\ProjectVersionService;
use Devonab\FilamentEasyFooter\Testing\TestsEasyFooter;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyFooterServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-easy-footer';

    public static string $viewNamespace = 'filament-easy-footer';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('devonab/filament-easy-footer');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/dists/'))) {
            $package->hasAssets();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        $this->app->singleton(GitHubService::class, function ($app) {
            return new GitHubService(
                repository: config('filament-easy-footer.github.repository'),
                token: config('filament-easy-footer.github.token'),
                cacheTtl: config('filament-easy-footer.github.cache_ttl', 3600),
            );
        });

        $this->app->singleton(ProjectVersionService::class, function ($app) {
            return new ProjectVersionService(
                github: $app->make(GitHubService::class),
                localFallbackConfigKey: config('filament-easy-footer.versioning.local_fallback_config_key'),
            );
        });

        Livewire::component('devonab.filament-easy-footer.github-version', GitHubVersion::class);
        Livewire::component('devonab.filament-easy-footer.project-version', ProjectVersion::class);

        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-easy-footer/{$file->getFilename()}"),
                ], 'filament-easy-footer-stubs');
            }
        }

        Testable::mixin(new TestsEasyFooter);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'devonab/filament-easy-footer';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-easy-footer', __DIR__ . '/../resources/dist/components/filament-easy-footer.js'),
            Css::make('filament-easy-footer-styles', __DIR__ . '/../resources/dist/filament-easy-footer.css'),
            Js::make('filament-easy-footer-scripts', __DIR__ . '/../resources/dist/filament-easy-footer.js'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }
}
