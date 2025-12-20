@php
    use Filament\Support\Enums\MaxWidth;

@endphp
<footer
    x-data="{ sidebarCollapsed: false }"
    x-init="
        sidebarCollapsed = $store.sidebar?.isOpen === false;
        $watch('$store.sidebar.isOpen', value => {
            sidebarCollapsed = value === false;
        });
    "
    x-show="!(sidebarCollapsed && @js($footerPosition === 'sidebar.footer' || $footerPosition === 'sidebar'))"
    x-transition
    @class([
        'fi-footer my-3 flex flex-wrap items-center justify-center text-sm text-gray-500 dark:text-gray-400',
        'border-t border-gray-200 dark:border-gray-700 text-center p-2' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer' || $borderTopEnabled === true,
        'fi-sidebar gap-2' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer',
        'gap-4' => $footerPosition !== 'sidebar' && $footerPosition !== 'sidebar.footer',
        'mx-auto w-full px-4 md:px-6 lg:px-8' => $footerPosition === 'footer',
        match ($maxContentWidth ??= (filament()->getMaxContentWidth() ?? MaxWidth::SevenExtraLarge)) {
            MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
            MaxWidth::Small, 'sm' => 'max-w-sm',
            MaxWidth::Medium, 'md' => 'max-w-md',
            MaxWidth::Large, 'lg' => 'max-w-lg',
            MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
            MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
            MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
            MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
            MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
            MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
            MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
            MaxWidth::Full, 'full' => 'max-w-full',
            MaxWidth::MinContent, 'min' => 'max-w-min',
            MaxWidth::MaxContent, 'max' => 'max-w-max',
            MaxWidth::FitContent, 'fit' => 'max-w-fit',
            MaxWidth::Prose, 'prose' => 'max-w-prose',
            MaxWidth::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',
            MaxWidth::ScreenMedium, 'screen-md' => 'max-w-screen-md',
            MaxWidth::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',
            MaxWidth::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',
            MaxWidth::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',
            default => $maxContentWidth,
        } => $footerPosition === 'footer',
    ])
>
    <span @class(['flex items-center gap-2' => $isHtmlSentence])>&copy; {{ now()->format('Y') }} -
        @if($sentence)
            @if($isHtmlSentence)
                <span class="flex items-center gap-2">{!! $sentence !!}</span>
            @else
                {{ $sentence }}
            @endif
        @else
            {{ config('filament-easy-footer.app_name') }}
        @endif
    </span>

    @if($githubEnabled)
        <livewire:devonab.filament-easy-footer.github-version
            :show-logo="$showLogo"
            :show-url="$showUrl"
        />
    @endif

    @if($logoPath)
        <span class="flex items-center gap-2">
            @if($logoText)
                <span>{{ $logoText }}</span>
            @endif
            @if($logoUrl)
                <a href="{{ $logoUrl }}" class="inline-flex" target="_blank">
                    @endif
                    <img
                        src="{{ $logoPath }}"
                        alt="Logo"
                        class="w-auto object-contain"
                        style="height: {{ $logoHeight }}px;"
                    >
                    @if($logoUrl)
                </a>
            @endif
        </span>
    @endif

    @if($loadTime)
        @if($footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer')
            <span class="w-full">{{ $loadTimePrefix ?? '' }} {{ $loadTime }}s</span>
        @else
            <span>{{ $loadTimePrefix ?? '' }} {{ $loadTime }}s</span>
        @endif
    @endif

    @if(count($links) > 0)
        <ul class="gap-2 flex">
            @foreach($links as $link)
                <li>
                    <a href="{{ $link['url'] }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-600 dark:hover:text-primary-300" target="_blank">{{ $link['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</footer>
