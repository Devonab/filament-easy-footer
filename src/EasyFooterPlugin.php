<?php

namespace Devonab\FilamentEasyFooter;

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\HtmlString;
use Throwable;

class EasyFooterPlugin implements Plugin
{
    private const MAX_LINKS = 3;

    private array $hiddenPaths = ['admin/login', 'admin/register', 'admin/forgot-password'];

    protected bool $githubEnabled = false;

    protected bool $borderTopEnabled = false;

    protected bool $showLogo = true;

    protected bool $showUrl = true;

    protected bool $hiddenFromPagesEnabled = false;

    protected bool $loadTimeEnabled = false;

    protected ?string $loadTimePrefix = null;

    protected ?string $sentence = null;

    protected bool $isHtmlSentence = false;

    protected string $footerPosition = 'footer';

    protected array $links = [];

    protected ?string $logoPath = null;

    protected ?string $logoUrl = null;

    protected ?string $logoText = null;

    protected int $logoHeight = 20;

    protected bool $isFooterEnabled = true;

    /**
     * Get the unique identifier for the plugin
     */
    public function getId(): string
    {
        return 'filament-easy-footer';
    }

    /**
     * Register the plugin with the panel
     *
     * @param  Panel  $panel  The Filament panel instance
     */
    public function register(Panel $panel): void
    {
        if ($this->shouldSkipRendering() || ! $this->isFooterEnabled) {
            return;
        }

        $githubService = app(GitHubService::class);

        if ($this->githubEnabled) {
            $githubService->enable($this->showLogo, $this->showUrl);
        } else {
            $githubService->disable();
        }

        $startTime = $this->loadTimeEnabled ? microtime(true) : 0;

        $panel->renderHook(
            $this->getRenderHook(),
            fn (): string => $this->renderFooter($startTime)
        );
    }

    /**
     * Enable or disable the footer completely
     * using ->footerEnabled(true or false)
     *
     * @param  bool  $enabled  Whether the footer should be displayed
     */
    public function footerEnabled(bool $enabled = true): static
    {
        $this->isFooterEnabled = $enabled;

        return $this;
    }

    /**
     * Check if the footer is enabled
     */
    public function isFooterEnabled(): bool
    {
        return $this->isFooterEnabled;
    }

    /**
     * Check if the footer rendering should be skipped
     */
    public function shouldSkipRendering(): bool
    {
        return $this->hiddenFromPagesEnabled && $this->isOnHiddenPage();
    }

    /**
     * Render the footer view
     *
     * @throws Throwable
     */
    protected function renderFooter(float $startTime): string
    {
        return view('filament-easy-footer::easy-footer', [
            'footerPosition' => $this->footerPosition,
            'githubEnabled' => $this->githubEnabled,
            'showLogo' => $this->showLogo,
            'showUrl' => $this->showUrl,
            'logoPath' => $this->logoPath,
            'logoUrl' => $this->logoUrl,
            'logoText' => $this->logoText,
            'logoHeight' => $this->logoHeight,
            'borderTopEnabled' => $this->borderTopEnabled,
            'loadTime' => $this->loadTimeEnabled ? $this->calculateLoadTime($startTime) : false,
            'loadTimePrefix' => $this->loadTimePrefix,
            'links' => $this->links,
            'sentence' => $this->sentence,
            'isHtmlSentence' => $this->isHtmlSentence,
        ])->render();
    }

    /**
     * Check if the current page is an auth page
     */
    protected function isOnHiddenPage(): bool
    {
        return in_array(request()->path(), $this->hiddenPaths, true);
    }

    /**
     * Calculate the page load time
     */
    protected function calculateLoadTime(float $startTime): string
    {
        return number_format(microtime(true) - $startTime, 3);
    }

    /**
     * Get the render hook based on footer position
     */
    public function getRenderHook(): string
    {
        return match ($this->footerPosition) {
            'sidebar' => 'panels::sidebar.nav.end',
            'sidebar.footer' => 'panels::sidebar.footer',
            default => 'panels::footer',
        };
    }

    /**
     * Configure whether to hide the footer from specific pages
     *
     * @return static EasyFooterPlugin
     */
    public function hiddenFromPagesEnabled(bool $enabled = true): static
    {
        $this->hiddenFromPagesEnabled = $enabled;

        return $this;
    }

    /**
     * Hide from these specific pages
     *
     * @param  array  $pages  Array of pages to hide the footer on
     * @return static EasyFooterPlugin
     */
    public function hiddenFromPages(array $pages): static
    {
        $this->hiddenPaths = $pages;

        return $this;
    }

    /**
     * Configure GitHub integration
     *
     * @param  bool  $showLogo  Whether to show the GitHub logo
     * @param  bool  $showUrl  Whether to show the GitHub URL
     * @return static EasyFooterPlugin
     */
    public function withGithub(bool $showLogo = true, bool $showUrl = true): static
    {
        $this->githubEnabled = true;
        $this->showLogo = $showLogo;
        $this->showUrl = $showUrl;

        return $this;
    }

    /**
     * Configure if the footer has a border
     *
     * @return static EasyFooterPlugin
     */
    public function withBorder(bool $enabled = true): static
    {
        $this->borderTopEnabled = $enabled;

        return $this;
    }

    /**
     * Configure the footer position
     *
     * @return static EasyFooterPlugin
     */
    public function withFooterPosition(string $position): static
    {
        $this->footerPosition = $position;

        return $this;
    }

    /**
     * Enable load time display with optional prefix text
     *
     * @return static EasyFooterPlugin
     */
    public function withLoadTime(?string $prefix = null, bool $enabled = true): static
    {
        $this->loadTimeEnabled = $enabled;
        $this->loadTimePrefix = $prefix;

        return $this;
    }

    /**
     * Add custom links to the footer
     *
     * @param  array  $links  Array of links with 'title' and 'url' keys
     * @return static EasyFooterPlugin
     */
    public function withLinks(array $links): static
    {
        $this->links = array_slice(
            array_filter($links, fn ($link) => isset($link['title'], $link['url'])),
            0,
            self::MAX_LINKS
        );

        return $this;
    }

    /**
     * Add a logo to the footer
     *
     * @param  string  $path  Path to the logo image
     * @param  string|null  $url  Optional URL for logo link
     * @param  string|null  $text  Optional text to display before the logo
     * @param  int  $height  Logo height in pixels (default: 20)
     */
    public function withLogo(string $path, ?string $url = null, ?string $text = null, int $height = 20): static
    {
        $this->logoPath = $path;
        $this->logoUrl = $url;
        $this->logoHeight = $height;
        $this->logoText = $text;

        return $this;
    }

    /**
     *  Set a custom sentence that replaces the app name in the copyright text
     *  Only allows basic HTML tags for text formatting
     *
     * @param  string|HtmlString  $sentence  Custom text or HTML to display
     */
    public function withSentence(string | HtmlString $sentence): static
    {
        if ($sentence instanceof HtmlString) {
            $sentence = $sentence->toHtml();
            $this->isHtmlSentence = true;
        }

        $this->sentence = strip_tags($sentence, '<strong><img><a><em><span><b><i><small>');

        return $this;
    }

    /**
     * Check if load time is enabled
     */
    public function isLoadTimeEnabled(): bool
    {
        return $this->loadTimeEnabled;
    }

    /**
     * Check if a prefix is set for the loadtime
     */
    public function getLoadTimePrefix(): ?string
    {
        return $this->loadTimePrefix;
    }

    /**
     * Get the current links
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Get the logo path
     */
    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    /**
     * Get the logo text
     */
    public function getLogoText(): ?string
    {
        return $this->logoText;
    }

    /**
     * Get the logo height
     */
    public function getLogoHeight(): int
    {
        return $this->logoHeight;
    }

    /**
     * Get the current sentence
     */
    public function getSentence(): ?string
    {
        return $this->sentence;
    }

    /**
     * Check if the sentence is HTML
     */
    public function isHtmlSentence(): bool
    {
        return $this->isHtmlSentence;
    }

    /**
     * Boot the plugin
     */
    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Create a new instance of the plugin
     *
     * @return static EasyFooterPlugin
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get the current instance of the plugin
     *
     * @return static EasyFooterPlugin
     */
    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
