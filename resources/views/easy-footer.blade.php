@php
    use Filament\Support\Enums\Width;
    @endphp
<footer
    @class([
        'fi-footer my-3 flex flex-wrap items-center justify-center text-sm text-gray-500 dark:text-gray-400',
        'border-t border-gray-200 dark:border-gray-700 text-center p-2' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer' || $borderTopEnabled === true,
        'fi-sidebar gap-2' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer',
        'gap-4' => $footerPosition !== 'sidebar' && $footerPosition !== 'sidebar.footer',
        'mx-auto w-full px-4 md:px-6 lg:px-8' => $footerPosition === 'footer',
        match ($maxContentWidth ??= (filament()->getMaxContentWidth() ?? Width::SevenExtraLarge)) {
            Width::ExtraSmall, 'xs' => 'max-w-xs',
            Width::Small, 'sm' => 'max-w-sm',
            Width::Medium, 'md' => 'max-w-md',
            Width::Large, 'lg' => 'max-w-lg',
            Width::ExtraLarge, 'xl' => 'max-w-xl',
            Width::TwoExtraLarge, '2xl' => 'max-w-2xl',
            Width::ThreeExtraLarge, '3xl' => 'max-w-3xl',
            Width::FourExtraLarge, '4xl' => 'max-w-4xl',
            Width::FiveExtraLarge, '5xl' => 'max-w-5xl',
            Width::SixExtraLarge, '6xl' => 'max-w-6xl',
            Width::SevenExtraLarge, '7xl' => 'max-w-7xl',
            Width::Full, 'full' => 'max-w-full',
            Width::MinContent, 'min' => 'max-w-min',
            Width::MaxContent, 'max' => 'max-w-max',
            Width::FitContent, 'fit' => 'max-w-fit',
            Width::Prose, 'prose' => 'max-w-prose',
            Width::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',
            Width::ScreenMedium, 'screen-md' => 'max-w-screen-md',
            Width::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',
            Width::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',
            Width::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',
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
                    <a href="{{ $link['url'] }}"
                       class="text-primary-600 dark:text-primary-400 hover:text-primary-600 dark:hover:text-primary-300"
                       target="_blank">{{ $link['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</footer>
