@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$link = $fields['link'] ?? null;
$link_url = is_array($link) ? (string) ($link['url'] ?? '') : '';
$link_title = is_array($link) ? (string) ($link['title'] ?? '') : '';
$link_target = is_array($link) ? (string) ($link['target'] ?? '') : '';
$image_small = $fields['image_small'] ?? null;
$image_tall = $fields['image_tall'] ?? null;
$features_heading = $fields['features_heading'] ?? null;
$features_raw = $fields['features'] ?? null;
$features = is_array($features_raw) ? array_values(array_filter($features_raw, static function ($row): bool {
    return is_array($row) && trim((string) ($row['feature_title'] ?? '')) !== '';
})) : [];
$id = $block['anchor'] ?? null;
$img_small_src = is_array($image_small) ? (string) ($image_small['sizes']['large'] ?? $image_small['url'] ?? '') : '';
$img_small_alt = is_array($image_small) ? (string) ($image_small['alt'] ?? '') : '';
$img_tall_src = is_array($image_tall) ? (string) ($image_tall['sizes']['large'] ?? $image_tall['url'] ?? '') : '';
$img_tall_alt = is_array($image_tall) ? (string) ($image_tall['alt'] ?? '') : '';
$has_intro = $label || $title || $text || ($link_url !== '' && $link_title !== '');
$has_media = $img_small_src !== '' || $img_tall_src !== '';
$has_features = $features_heading || count($features) > 0;
$show = $has_intro || $has_media || $has_features;
$grid_class = 'case-study__grid';
if (!$has_intro && $has_media) {
    $grid_class .= ' case-study__grid--no-intro';
}
if ($img_small_src === '') {
    $grid_class .= ' case-study__grid--no-small';
}
if ($img_tall_src === '') {
    $grid_class .= ' case-study__grid--no-tall';
}
if (!$has_intro && !$has_media && $has_features) {
    $grid_class .= ' case-study__grid--features-only';
}
@endphp
@if($show)
<section
    @if($id) id="{{ $id }}" @endif
    class="case-study has-waves">
    <div class="container">
        <div class="{{ $grid_class }}" data-aos="fade-up">
            @if($img_small_src !== '')
            <div class="case-study__media case-study__media--small">
                <img src="{{ esc_url($img_small_src) }}" alt="{{ esc_attr($img_small_alt) }}" loading="lazy" decoding="async">
            </div>
            @endif
            @if($has_intro)
            <div class="case-study__intro content">
                <x-split-badge :text="$label" />
                @if($title)
                <h2 class="h2">{{ esc_html($title) }}</h2>
                @endif
                @if($text)
                <div class="case-study__body">{!! $text !!}</div>
                @endif
                @if($link_url !== '' && $link_title !== '')
                <div class="buttons">
                    <a
                        href="{{ esc_url($link_url) }}"
                        class="btn"
                        @if($link_target !== '') target="{{ esc_attr($link_target) }}" @endif
                        @if($link_target === '_blank') rel="noopener noreferrer" @endif>{{ esc_html($link_title) }}</a>
                </div>
                @endif
            </div>
            @endif
            @if($img_tall_src !== '')
            <div class="case-study__media case-study__media--tall">
                <div class="case-study__media-tall-inner">
                    <img src="{{ esc_url($img_tall_src) }}" alt="{{ esc_attr($img_tall_alt) }}" loading="lazy" decoding="async">
                </div>
            </div>
            @endif
            @if($features_heading || count($features) > 0)
            <div class="case-study__lower case-study__lower--left">
                @if($features_heading)
                <h3 class="h3 case-study__lower-heading">{{ esc_html($features_heading) }}</h3>
                @endif
                @if(count($features) > 0)
                @php $first = $features[0]; $fa0 = \App\sanitize_fa_icon_classes((string) ($first['icon_class'] ?? '')); @endphp
                <article class="case-study__feature">
                    <div class="case-study__feature-head">
                        <h4 class="h4 case-study__feature-title">{{ esc_html($first['feature_title'] ?? '') }}</h4>
                        @if($fa0 !== '')
                        <span class="case-study__feature-icon" aria-hidden="true"><i class="{{ esc_attr($fa0) }}"></i></span>
                        @endif
                    </div>
                    @if(!empty($first['feature_text']))
                    <div class="case-study__feature-text">{!! wp_kses_post($first['feature_text']) !!}</div>
                    @endif
                </article>
                @endif
            </div>
            @if(isset($features[1]))
            @php $second = $features[1]; $fa1 = \App\sanitize_fa_icon_classes((string) ($second['icon_class'] ?? '')); @endphp
            <div class="case-study__lower case-study__lower--right">
                <article class="case-study__feature">
                    <div class="case-study__feature-head">
                        <h4 class="h4 case-study__feature-title">{{ esc_html($second['feature_title'] ?? '') }}</h4>
                        @if($fa1 !== '')
                        <span class="case-study__feature-icon" aria-hidden="true"><i class="{{ esc_attr($fa1) }}"></i></span>
                        @endif
                    </div>
                    @if(!empty($second['feature_text']))
                    <div class="case-study__feature-text">{!! wp_kses_post($second['feature_text']) !!}</div>
                    @endif
                </article>
            </div>
            @elseif(count($features) === 1)
            <div class="case-study__lower case-study__lower--right case-study__lower--spacer" aria-hidden="true"></div>
            @endif
            @if(count($features) > 2)
            <div class="case-study__more">
                @foreach(array_slice($features, 2) as $extra)
                @php $fax = \App\sanitize_fa_icon_classes((string) ($extra['icon_class'] ?? '')); @endphp
                <article class="case-study__feature">
                    <div class="case-study__feature-head">
                        <h4 class="h4 case-study__feature-title">{{ esc_html($extra['feature_title'] ?? '') }}</h4>
                        @if($fax !== '')
                        <span class="case-study__feature-icon" aria-hidden="true"><i class="{{ esc_attr($fax) }}"></i></span>
                        @endif
                    </div>
                    @if(!empty($extra['feature_text']))
                    <div class="case-study__feature-text">{!! wp_kses_post($extra['feature_text']) !!}</div>
                    @endif
                </article>
                @endforeach
            </div>
            @endif
            @endif
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="case-study case-study--empty has-waves" aria-label="{{ esc_attr(__('Case study', 'sage')) }}">
    <div class="container">
        <p class="case-study__empty-msg">{{ esc_html(__('Add a headline, images, or highlights in the sidebar.', 'sage')) }}</p>
    </div>
</section>
@endif
