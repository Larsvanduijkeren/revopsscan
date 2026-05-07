@php
$logo = get_field('logo', 'option');
$logoSecondary = function_exists('get_field') ? get_field('logo_secondary', 'option') : null;
$logoSecondaryLink = function_exists('get_field') ? (get_field('logo_secondary_link', 'option') ?: []) : [];
$logoSecondaryUrl = is_array($logoSecondaryLink) ? ($logoSecondaryLink['url'] ?? '') : '';
$logoSecondaryTarget = is_array($logoSecondaryLink) ? ($logoSecondaryLink['target'] ?? '_self') : '_self';

$headerBtnPrimary = function_exists('get_field') ? (get_field('header_button_primary', 'option') ?: []) : [];
$headerBtnSecondary = function_exists('get_field') ? (get_field('header_button_secondary', 'option') ?: []) : [];

if (empty($headerBtnPrimary['url']) && function_exists('get_field')) {
    $headerBtnPrimary = get_field('header_button', 'option') ?: [];
    if (empty($headerBtnPrimary['url'])) {
        $legacyButtons = get_field('header_buttons', 'option');
        if (!empty($legacyButtons[0]['button']['url'])) {
            $headerBtnPrimary = $legacyButtons[0]['button'];
        }
    }
}

$headerSupport = function_exists('get_field') ? (get_field('header_support_link', 'option') ?: []) : [];
$headerSupportIconClass = \App\header_support_icon_class(
    function_exists('get_field') ? get_field('header_support_icon', 'option') : null
);

$headerHasBottomBar = !empty($headerSupport['url']);
$headerMainClass = 'main-header' . ($headerHasBottomBar ? '' : ' main-header--no-bottom');
@endphp

@include('partials.mobile-nav', [
    'headerBtnPrimary' => $headerBtnPrimary,
    'headerBtnSecondary' => $headerBtnSecondary,
    'headerSupport' => $headerSupport,
    'headerSupportIconClass' => $headerSupportIconClass,
])

<span class="hamburger"></span>

<div class="header-wrapper">
<header class="header">
    <div class="container">
        <div class="flex-wrapper">
            <div class="logos">
                <a href="{{ home_url('/') }}" class="logo logo-primary" aria-label="Logo for {{ get_bloginfo('name') }}">
                    @if(!empty($logo))
                    <img src="{{ $logo['sizes']['large'] ?? $logo['url'] ?? '' }}" alt="">
                    @endif
                </a>

                @if(!empty($logoSecondary))
                    @if($logoSecondaryUrl)
                    <a
                        href="{{ esc_url($logoSecondaryUrl) }}"
                        target="{{ esc_attr($logoSecondaryTarget) }}"
                        rel="{{ $logoSecondaryTarget === '_blank' ? 'noopener noreferrer' : '' }}"
                        class="logo logo-secondary"
                        aria-label="{{ esc_attr($logoSecondary['alt'] ?? __('Secondary logo', 'sage')) }}">
                        <img
                            src="{{ esc_url($logoSecondary['sizes']['large'] ?? $logoSecondary['url'] ?? '') }}"
                            alt="{{ esc_attr($logoSecondary['alt'] ?? '') }}">
                    </a>
                    @else
                    <span class="logo logo-secondary" aria-hidden="true">
                        <img
                            src="{{ esc_url($logoSecondary['sizes']['large'] ?? $logoSecondary['url'] ?? '') }}"
                            alt="{{ esc_attr($logoSecondary['alt'] ?? '') }}">
                    </span>
                    @endif
                @endif
            </div>

            <div class="{{ $headerMainClass }}">
                <div class="top-bar">
                    @php
                        $headerNavMenuArgs = [
                            'theme_location' => 'header-nav',
                            'echo' => false,
                            'container' => false,
                            'menu_class' => 'menu',
                            'depth' => 4,
                            'fallback_cb' => false,
                            'walker' => new \App\Walkers\MegaMenuWalker(),
                        ];
                    @endphp
                    {!! wp_nav_menu($headerNavMenuArgs) !!}

                    @include('partials.header-buttons')
                </div>

                @if($headerHasBottomBar)
                <div class="bottom-bar">
                    @include('partials.header-support-link')
                </div>
                @endif
            </div>
        </div>
    </div>
</header>
</div>
