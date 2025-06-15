<?php

namespace Devonab\FilamentEasyFooter\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected bool $enabled = false;

    protected ?string $token = null;

    protected ?string $repository = null;

    protected bool $showLogo = false;

    protected bool $showUrl = false;

    protected int $cacheTtl;

    protected string $defaultVersion;

    public function __construct(
        ?string $repository = null,
        ?string $token = null,
        int $cacheTtl = 3600,
        string $defaultVersion = '0.0'
    ) {
        $this->repository = $repository ?? config('filament-easy-footer.github.repository');
        $this->token = $token ?? config('filament-easy-footer.github.token');
        $this->cacheTtl = $cacheTtl;
        $this->defaultVersion = $defaultVersion;
    }

    public function enable(bool $showLogo = true, bool $showUrl = true): self
    {
        $this->enabled = true;
        $this->showLogo = $showLogo;
        $this->showUrl = $showUrl;

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function shouldShowLogo(): bool
    {
        return $this->showLogo;
    }

    public function shouldShowUrl(): bool
    {
        return $this->showUrl;
    }

    public function getLatestTag(?string $repository = null): string
    {
        if (! $this->enabled) {
            return $this->defaultVersion;
        }

        $repository = $repository ?? $this->repository;
        $cacheKey = "filament-easy-footer.github.{$repository}.latest-tag";

        $cachedTag = $this->getCacheWithoutTags($cacheKey);
        if ($cachedTag !== null) {
            return $cachedTag;
        }

        $tag = $this->fetchLatestTag($repository) ?? $this->defaultVersion;

        $this->setCacheWithoutTags($cacheKey, $tag, $this->cacheTtl);

        return $tag;
    }

    protected function fetchLatestTag(string $repository): ?string
    {
        try {
            $response = Http::when(config('filament-easy-footer.github.token'), fn ($request) => $request->withToken($this->token))
                ->get("https://api.github.com/repos/{$repository}/releases/latest");

            if ($response->successful()) {
                return $response->json('tag_name');
            }

            $tagsResponse = Http::when(config('filament-easy-footer.github.token'), fn ($request) => $request->withToken($this->token))
                ->get("https://api.github.com/repos/{$repository}/tags");

            if ($tagsResponse->successful() && ! empty($tagsResponse->json())) {
                return $tagsResponse->json()[0]['name'];
            }

        } catch (Exception $e) {
            report($e);
        }

        return null;
    }

    protected function getCacheWithoutTags(string $key)
    {
        try {
            return Cache::store(config('cache.default'))->get($key);
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    protected function setCacheWithoutTags(string $key, $value, int $ttl): void
    {
        try {
            Cache::store(config('cache.default'))->put($key, $value, $ttl);
        } catch (Exception $e) {
            report($e);
        }
    }
}
