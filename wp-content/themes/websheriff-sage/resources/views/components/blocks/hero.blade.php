@php
$hero_layout = ($fields['hero_layout'] ?? '') === 'split' ? 'split' : 'overlay';
$is_overlay = $hero_layout !== 'split';

$galleryRaw = $fields['gallery'] ?? null;
$gallery = is_array($galleryRaw) ? array_values(array_filter($galleryRaw, static function ($item): bool {
    return is_array($item) && (!empty($item['ID']) || !empty($item['url']));
})) : [];

$title = $fields['title'] ?? null;
$title_highlight = $fields['split_title_highlight'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$split_badge = $fields['split_badge'] ?? null;
$split_show_breadcrumb = !empty($fields['split_show_breadcrumb']);
$split_cta = $fields['split_cta'] ?? null;
$split_cta_note = $fields['split_cta_note'] ?? null;
$split_visual = $fields['split_visual'] ?? 'stats';
$split_stats_header = $fields['split_stats_header'] ?? null;
$split_stats_rows = $fields['split_stats_rows'] ?? null;
$split_stats_progress = isset($fields['split_stats_progress']) ? max(0, min(100, (int) $fields['split_stats_progress'])) : null;
$split_stats_progress_label = $fields['split_stats_progress_label'] ?? null;
$split_icon_items = $fields['split_icon_items'] ?? null;
$split_visual_gallery_raw = $fields['split_visual_gallery'] ?? null;
$split_gallery = is_array($split_visual_gallery_raw) ? array_values(array_filter($split_visual_gallery_raw, static function ($item): bool {
    return is_array($item) && (!empty($item['ID']) || !empty($item['url']));
})) : [];

$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
@endphp

@if($is_overlay)
<section
    @if($id) id="{{ $id }}" @endif
    class="hero hero--layout-overlay has-waves">

    <div class="container">
        <div class="card">
            @if(!empty($gallery))
            <div class="image">
                @if(count($gallery) > 1)
                <div class="swiper hero-gallery-swiper" aria-label="{{ __('Achtergrondafbeeldingen', 'sage') }}">
                    <div class="swiper-wrapper">
                        @foreach($gallery as $img)
                        <div class="swiper-slide">
                            <img
                                src="{{ esc_url($img['sizes']['full'] ?? $img['url'] ?? '') }}"
                                alt="{{ esc_attr($img['alt'] ?? '') }}"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                decoding="async">
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                @php $img = $gallery[0]; @endphp
                <img
                    src="{{ esc_url($img['sizes']['full'] ?? $img['url'] ?? '') }}"
                    alt="{{ esc_attr($img['alt'] ?? '') }}"
                    loading="eager"
                    decoding="async">
                @endif
            </div>
            @endif

            <div class="content">
                <div class="title-wrapper">
                    @include('partials.rank-math-breadcrumb', ['wrapper_class' => 'hero__breadcrumb'])

                    @if($title)
                    <h1>{{ esc_html($title) }}</h1>
                    @endif
                </div>

                <div class="wrapper">
                    @if($text)
                    {!! $text !!}
                    @endif

                    @if($buttons)
                    <div class="buttons">
                        @foreach($buttons as $button)
                        @php
                        $button_obj = $button['button'] ?? $button;
                        $url = $button_obj['url'] ?? null;
                        $button_title = $button_obj['title'] ?? null;
                        $target = $button_obj['target'] ?? '_self';
                        @endphp
                        @if($url && $button_title)
                        <a
                            href="{{ esc_url($url) }}"
                            target="{{ esc_attr($target) }}"
                            class="{{ $loop->first ? 'btn' : 'btn-ghost white' }}"
                            rel="{{ $target === '_blank' ? 'noopener noreferrer' : '' }}">
                            {{ esc_html($button_title) }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section
    @if($id) id="{{ $id }}" @endif
    class="hero hero--layout-split has-waves">
    <div class="container hero__split-container">
        <div class="hero__split-grid">
            <div class="hero__split-content" data-aos="fade-up">
                @if($split_show_breadcrumb)
                    @include('partials.rank-math-breadcrumb', ['wrapper_class' => 'hero__breadcrumb hero__breadcrumb--split'])
                @endif

                <x-split-badge :text="$split_badge" />

                @if($title || $title_highlight)
                <h1 class="hero__split-title">
                    @if($title)<span class="hero__split-title-main">{{ esc_html($title) }}</span>@endif
                    @if($title_highlight)<span class="hero__split-title-accent"> {{ esc_html($title_highlight) }}</span>@endif
                </h1>
                @endif

                @if($text)
                <div class="hero__split-text">{!! $text !!}</div>
                @endif

                @php
                $cta_url = is_array($split_cta) ? ($split_cta['url'] ?? '') : '';
                $cta_title = is_array($split_cta) ? ($split_cta['title'] ?? '') : '';
                $cta_target = is_array($split_cta) ? ($split_cta['target'] ?? '_self') : '_self';
                @endphp
                @if(($cta_url && $cta_title) || $split_cta_note)
                <div class="hero__split-actions">
                    @if($cta_url && $cta_title)
                    <a href="{{ esc_url($cta_url) }}" class="btn" target="{{ esc_attr($cta_target) }}" rel="{{ $cta_target === '_blank' ? 'noopener noreferrer' : '' }}">{{ esc_html($cta_title) }}</a>
                    @endif
                    @if($split_cta_note)
                    <span class="hero__split-cta-note">{{ esc_html($split_cta_note) }}</span>
                    @endif
                </div>
                @endif
            </div>

            <div class="hero__split-visual" data-aos="fade-up">
                @if($split_visual === 'stats')
                <div class="hero-split-panel hero-split-panel--stats">
                    @if($split_stats_header)
                    <p class="hero-split-panel__header">{{ esc_html($split_stats_header) }}</p>
                    @endif
                    @if(!empty($split_stats_rows) && is_array($split_stats_rows))
                    <ul class="hero-split-stats">
                        @foreach($split_stats_rows as $row)
                        @php
                        $tone = $row['tone'] ?? 'warning';
                        if (!in_array($tone, ['critical', 'warning', 'success'], true)) {
                            $tone = 'warning';
                        }
                        $fa = \App\sanitize_fa_icon_classes((string) ($row['icon_class'] ?? ''));
                        $rTitle = $row['row_title'] ?? '';
                        $rSub = $row['row_subtitle'] ?? '';
                        $rVal = $row['row_value'] ?? '';
                        @endphp
                        @if($rTitle !== '' || $rVal !== '')
                        <li class="hero-split-stats__row hero-split-stats__row--{{ $tone }}">
                            <span class="hero-split-stats__icon" aria-hidden="true">
                                @if($fa !== '')
                                <i class="{{ esc_attr($fa) }}"></i>
                                @endif
                            </span>
                            <div class="hero-split-stats__body">
                                @if($rTitle !== '')
                                <span class="hero-split-stats__title">{{ esc_html($rTitle) }}</span>
                                @endif
                                @if($rSub !== '')
                                <span class="hero-split-stats__sub">{{ esc_html($rSub) }}</span>
                                @endif
                            </div>
                            @if($rVal !== '')
                            <span class="hero-split-stats__value">{{ esc_html($rVal) }}</span>
                            @endif
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    @endif
                    @if($split_stats_progress !== null)
                    <div class="hero-split-progress" role="progressbar" aria-valuenow="{{ (int) $split_stats_progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="hero-split-progress__track">
                            <div class="hero-split-progress__fill" style="width: {{ (int) $split_stats_progress }}%;"></div>
                        </div>
                        @if($split_stats_progress_label)
                        <p class="hero-split-progress__label">{{ esc_html($split_stats_progress_label) }}</p>
                        @endif
                    </div>
                    @endif
                </div>
                @elseif($split_visual === 'icons' && !empty($split_icon_items) && is_array($split_icon_items))
                <div class="hero-split-panel hero-split-panel--icons">
                    <ul class="hero-split-icons">
                        @foreach($split_icon_items as $iconRow)
                        @php $ic = \App\sanitize_fa_icon_classes((string) ($iconRow['icon_class'] ?? '')); @endphp
                        @if($ic !== '')
                        <li class="hero-split-icons__item">
                            <span class="hero-split-icons__icon" aria-hidden="true"><i class="{{ esc_attr($ic) }}"></i></span>
                            @if(!empty($iconRow['icon_label']))
                            <span class="hero-split-icons__label">{{ esc_html($iconRow['icon_label']) }}</span>
                            @endif
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                @elseif($split_visual === 'images' && !empty($split_gallery))
                <div class="hero-split-panel hero-split-panel--images">
                    @php
                    $show_split_slider = !$is_preview && count($split_gallery) > 1;
                    $split_display = $is_preview ? array_slice($split_gallery, 0, 1) : $split_gallery;
                    @endphp
                    @if($show_split_slider)
                    <div class="swiper hero-split-visual__swiper" aria-label="{{ __('Media', 'sage') }}">
                        <div class="swiper-wrapper">
                            @foreach($split_display as $img)
                            <div class="swiper-slide">
                                <img src="{{ esc_url($img['sizes']['large'] ?? $img['url'] ?? '') }}" alt="{{ esc_attr($img['alt'] ?? '') }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-scrollbar"></div>
                    </div>
                    @else
                    @php $simg = $split_display[0]; @endphp
                    <img class="hero-split-images__single" src="{{ esc_url($simg['sizes']['large'] ?? $simg['url'] ?? '') }}" alt="{{ esc_attr($simg['alt'] ?? '') }}" loading="eager" decoding="async">
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
