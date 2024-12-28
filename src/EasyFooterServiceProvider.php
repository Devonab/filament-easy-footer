<?php

namespace Devonab\FilamentEasyFooter;

use Devonab\FilamentEasyFooter\Livewire\GitHubVersion;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Devonab\FilamentEasyFooter\Commands\FilamentEasyFooterCommand;
use Devonab\FilamentEasyFooter\Testing\TestsEasyFooter;

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
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {

        Livewire::component('devonab.filament-easy-footer.github-version', GitHubVersion::class);

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
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentEasyFooterCommand::class,
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