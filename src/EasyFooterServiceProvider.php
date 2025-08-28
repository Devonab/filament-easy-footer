<?php

namespace Devonab\FilamentEasyFooter;

use Devonab\FilamentEasyFooter\DTO\UpdateInfo;
use Devonab\FilamentEasyFooter\Livewire\GitHubVersion;
use Devonab\FilamentEasyFooter\Services\Contracts\VersionComparatorInterface;
use Devonab\FilamentEasyFooter\Services\GitHubService;
use Devonab\FilamentEasyFooter\Services\LocalVersionService;
use Devonab\FilamentEasyFooter\Services\RemoteGithubVersionService;
use Devonab\FilamentEasyFooter\Services\SemverVersionComparator;
use Devonab\FilamentEasyFooter\Services\VersionComparisonService;
use Devonab\FilamentEasyFooter\Testing\TestsEasyFooter;
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

        $this->registerVersionClasses();

        Livewire::component('devonab.filament-easy-footer.github-version', GitHubVersion::class);

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


    protected function registerVersionClasses():void
    {
        $this->app->bind(VersionComparatorInterface::class, SemverVersionComparator::class);

        // Bind LocalVersionService with configurable fallback strategy
        $this->app->bind(LocalVersionService::class, function ($app) {
            $cfg  = $app['config']->get('filament-easy-footer.versioning', []);
            $mode = $cfg['local_fallback'] ?? 'config';
            $ckey = $cfg['local_config_key'] ?? 'app.version';
            $ekey = $cfg['local_env_key'] ?? 'APP_VERSION';

            $fallback = match ($mode) {
                'config' => fn (): ?string => (string) ($app['config']->get($ckey) ?? '') ?: null,
                'env'    => fn (): ?string => (string) (env($ekey) ?? '') ?: null,
                default  => null,
            };

            return new LocalVersionService($fallback);
        });

        // Remote version service
        $this->app->bind(RemoteGithubVersionService::class, function ($app) {
            /** @var GitHubService $github */
            $github = $app->make(GitHubService::class);
            $repo = (string) $app['config']->get('filament-easy-footer.repo'); // z. B. 'org/repo'
            return new RemoteGithubVersionService($github, $repo);
        });

        //Compare service
        $this->app->bind(VersionComparisonService::class, function ($app) {
            return new VersionComparisonService(
                $app->make(LocalVersionService::class),
                $app->make(RemoteGithubVersionService::class),
                $app->make(VersionComparatorInterface::class),
            );
        });

        //updateInfo Factory
        $this->app->scoped(UpdateInfo::class, function ($app) {
            /** @var VersionComparisonService $svc */
            $svc = $app->make(VersionComparisonService::class);
            return $svc->getUpdateInfo();
        });

        view()->composer('filament-easy-footer::footer', function ($view) {
            $view->with('info', app(UpdateInfo::class));
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'devonab/filament-easy-footer';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }
}
