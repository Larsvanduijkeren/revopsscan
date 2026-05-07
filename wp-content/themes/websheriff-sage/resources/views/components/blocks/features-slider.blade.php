@php
$top_icon_raw = $fields['top_icon'] ?? '';
$top_icon = '';
if (is_string($top_icon_raw) && $top_icon_raw !== '') {
    $sanitized = \App\sanitize_fa_icon_classes($top_icon_raw);
    if ($sanitized !== '') {
        $top_icon = str_contains($sanitized, 'fa-') ? $sanitized : 'fa-solid fa-' . ltrim($sanitized, '-');
    }
}
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$top_button = $fields['top_button'] ?? null;
$items_raw = $fields['items'] ?? null;
$items = is_array($items_raw) ? array_values(array_filter($items_raw, static function ($row): bool {
    return is_array($row) && trim((string) ($row['item_title'] ?? '')) !== '';
})) : [];
$autoplay_delay = (int) ($fields['autoplay_delay'] ?? 4500);
if ($autoplay_delay < 1500) {
    $autoplay_delay = 4500;
}

$id = $block['anchor'] ?? null;
$slider_label = $title ?: __('Features', 'sage');

$top_btn_url = is_array($top_button) ? ($top_button['url'] ?? '') : '';
$top_btn_title = is_array($top_button) ? ($top_button['title'] ?? '') : '';
$top_btn_target = is_array($top_button) ? ($top_button['target'] ?? '_self') : '_self';
$has_top_row = $top_icon !== '' || ($top_btn_url && $top_btn_title);
@endphp

@if(!empty($items))
<section
    @if($id) id="{{ $id }}" @endif
    class="features-slider has-waves">
    <div class="container">
        @if($has_top_row)
        <div class="features-slider__top-row" data-aos="fade-up">
            @if($top_icon !== '')
            <span class="features-slider__top-icon" aria-hidden="true">
                <i class="{{ esc_attr($top_icon) }}"></i>
            </span>
            @else
            <span class="features-slider__top-spacer" aria-hidden="true"></span>
            @endif

            <span class="features-slider__top-line" aria-hidden="true"></span>

            @if($top_btn_url && $top_btn_title)
            <a
                href="{{ esc_url($top_btn_url) }}"
                target="{{ esc_attr($top_btn_target) }}"
                rel="{{ $top_btn_target === '_blank' ? 'noopener noreferrer' : '' }}"
                class="btn-ghost small features-slider__top-btn">
                {{ esc_html($top_btn_title) }}
            </a>
            @endif
        </div>
        @endif

        <div class="features-slider__header" data-aos="fade-up">
            <div class="features-slider__heading">
                <x-split-badge :text="$label" />

                @if($title)
                <h2 class="features-slider__title">{{ esc_html($title) }}</h2>
                @endif
            </div>

            @if($text)
            <div class="features-slider__intro">
                <div class="features-slider__text">{!! wp_kses_post($text) !!}</div>
            </div>
            @endif
        </div>

        <div
            class="features-slider__stage"
            data-aos="fade-up"
            data-features-slider
            data-autoplay="{{ count($items) > 1 ? '1' : '0' }}"
            data-autoplay-delay="{{ esc_attr($autoplay_delay) }}">

            <button
                type="button"
                class="features-slider__nav features-slider__nav--prev"
                data-features-slider-prev
                aria-label="{{ esc_attr(__('Previous feature', 'sage')) }}">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>

            <div class="swiper features-slider__swiper" aria-label="{{ esc_attr($slider_label) }}">
                <div class="swiper-wrapper">
                    @foreach($items as $item)
                    @php
                    $image = $item['image'] ?? null;
                    $fa = \App\sanitize_fa_icon_classes((string) ($item['icon_class'] ?? ''));
                    $item_title = $item['item_title'] ?? '';
                    $item_text = $item['item_text'] ?? '';
                    $link = $item['link'] ?? null;
                    $link_url = is_array($link) ? ($link['url'] ?? '') : '';
                    $link_title = is_array($link) ? ($link['title'] ?? '') : '';
                    $link_target = is_array($link) ? ($link['target'] ?? '_self') : '_self';
                    @endphp
                    <div class="swiper-slide">
                        <article class="feature-card">
                            <div class="feature-card__media">
                                @if($fa !== '')
                                <span class="feature-card__icon" aria-hidden="true">
                                    <i class="{{ esc_attr($fa) }}"></i>
                                </span>
                                @endif

                                @if(!empty($image))
                                <img
                                    src="{{ esc_url($image['sizes']['large'] ?? $image['url'] ?? '') }}"
                                    alt="{{ esc_attr($image['alt'] ?? '') }}"
                                    loading="lazy"
                                    decoding="async">
                                @endif
                            </div>

                            <div class="feature-card__body">
                                <h3 class="feature-card__title">{{ esc_html($item_title) }}</h3>

                                @if($item_text !== '')
                                <div class="feature-card__text">{!! wp_kses_post($item_text) !!}</div>
                                @endif

                                @if($link_url && $link_title)
                                <a
                                    href="{{ esc_url($link_url) }}"
                                    target="{{ esc_attr($link_target) }}"
                                    rel="{{ $link_target === '_blank' ? 'noopener noreferrer' : '' }}"
                                    class="feature-card__link">
                                    {{ esc_html($link_title) }}
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                                @endif
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>
            </div>

            <button
                type="button"
                class="features-slider__nav features-slider__nav--next"
                data-features-slider-next
                aria-label="{{ esc_attr(__('Next feature', 'sage')) }}">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        @if(count($items) > 1)
        <div class="features-slider__controls" data-aos="fade-up">
            <button
                type="button"
                class="features-slider__autoplay"
                data-features-slider-autoplay
                aria-label="{{ esc_attr(__('Pause autoplay', 'sage')) }}"
                data-pause-label="{{ esc_attr(__('Pause autoplay', 'sage')) }}"
                data-play-label="{{ esc_attr(__('Resume autoplay', 'sage')) }}">
                <i class="fa-solid fa-pause" aria-hidden="true" data-features-slider-autoplay-icon></i>
            </button>
        </div>
        @endif
    </div>
</section>
@elseif(!empty($is_preview))
<section class="features-slider features-slider--empty has-waves" aria-label="{{ esc_attr(__('Features slider', 'sage')) }}">
    <div class="container">
        <p class="features-slider__empty-msg">{{ esc_html(__('Add features in the sidebar to populate the slider.', 'sage')) }}</p>
    </div>
</section>
@endif
