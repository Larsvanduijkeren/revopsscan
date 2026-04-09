@php
$selection_posts = isset($selection_posts) && is_array($selection_posts) ? $selection_posts : [];
$label = $label ?? null;
$title = $title ?? null;
$intro = $intro ?? null;
$section_id = $section_id ?? null;
$section_class = trim((string) ($section_class ?? ''));
$is_preview = $is_preview ?? false;
@endphp
@if(!empty($selection_posts))
<section
    @if($section_id) id="{{ $section_id }}" @endif
    class="post-selection has-waves{{ $section_class !== '' ? ' ' . $section_class : '' }}">
    <div class="container">
        @if($label || $title || $intro)
        <div class="intro" data-aos="fade-up">
            @if($label)
            <span class="label">{{ esc_html($label) }}</span>
            @endif
            @if($title)
            <h2 class="post-selection__title h2">{{ esc_html($title) }}</h2>
            @endif
            @if($intro)
            <div class="post-selection__intro">{!! $intro !!}</div>
            @endif
        </div>
        @endif

        <div class="slider overflow-wrap" data-aos="fade-up">
            <div class="swiper post-selection-swiper" aria-label="{{ esc_attr($title ?: __('Articles', 'sage')) }}">
                <div class="swiper-wrapper">
                    @foreach($selection_posts as $card_post)
                    <div class="swiper-slide">
                        @include('components.post-card', ['post' => $card_post, 'in_slider' => false, 'archive_base_url' => $archive_base_url ?? null])
                    </div>
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="post-selection post-selection--empty has-waves" aria-label="{{ esc_attr(__('Post selection', 'sage')) }}">
    <div class="container">
        <p class="post-selection__empty-msg">{{ esc_html(__('Choose posts (manual, category, or recent) in the sidebar.', 'sage')) }}</p>
    </div>
</section>
@endif
