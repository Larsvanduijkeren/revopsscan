@php
$order = $fields['order'] ?? null;
$media_type = $fields['media_type'] ?? 'image';
$small_images = $fields['small_images'] ?? null;
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$images = $fields['images'] ?? null;
$video_url = $fields['video_url'] ?? null;
$video_poster = $fields['video_poster'] ?? null;
$video_caption = $fields['video_caption'] ?? null;
$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
$caption_id = 'text-media-caption-' . preg_replace('/[^a-z0-9_-]+/i', '', (string) ($block['id'] ?? 'block'));
$video_url = is_string($video_url) ? trim($video_url) : '';
$play_label = $title
    ? sprintf(__('Play video: %s', 'sage'), wp_strip_all_tags($title))
    : __('Play video', 'sage');
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="text-media {{ $order }} {{ $media_type === 'image' && $small_images ? 'small-images' : '' }} has-waves">
    <div class="container">
        <div class="flex-wrapper">
            <div class="content" data-aos="fade-up">
                <x-split-badge :text="$label" />
                @if($title)
                <h2>{{ esc_html($title) }}</h2>
                @endif
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
                        class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}"
                        rel="{{ $target === '_blank' ? 'noopener noreferrer' : '' }}">
                        {{ esc_html($button_title) }}
                    </a>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            @if($media_type === 'image' && !empty($images) && is_array($images))
            <div class="text-media__visual" data-aos="fade-up">
                @php
                $show_slider = !$is_preview && count($images) > 1;
                $display_images = $is_preview ? array_slice($images, 0, 1) : $images;
                @endphp
                @if($show_slider)
                <div class="swiper text-media-swiper">
                    <div class="swiper-wrapper">
                        @foreach($display_images as $image)
                        <div class="swiper-slide">
                            <img src="{{ esc_url($image['sizes']['large'] ?? $image['url'] ?? '') }}" alt="{{ esc_attr($image['alt'] ?? '') }}">
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-scrollbar"></div>
                </div>
                @else
                <img src="{{ esc_url($display_images[0]['sizes']['large'] ?? $display_images[0]['url'] ?? '') }}" alt="{{ esc_attr($display_images[0]['alt'] ?? '') }}">
                @endif
            </div>
            @elseif($media_type === 'video' && $video_url !== '')
            <div class="text-media__visual" data-aos="fade-up">
                <div class="text-media__video">
                    @if($is_preview)
                    <div
                        class="text-media__video-trigger text-media__video-trigger--preview"
                        role="img"
                        aria-label="{{ esc_attr($play_label) }}"
                        @if($video_caption)
                        aria-describedby="{{ esc_attr($caption_id) }}"
                        @endif
                    >
                    @else
                    <a
                        href="{{ esc_url($video_url) }}"
                        class="text-media__video-trigger"
                        data-lity
                        aria-label="{{ esc_attr($play_label) }}"
                        @if($video_caption)
                        aria-describedby="{{ esc_attr($caption_id) }}"
                        @endif
                    >
                    @endif
                        <span class="text-media__video-poster-wrap">
                            @if(!empty($video_poster['url'] ?? null))
                            <img src="{{ esc_url($video_poster['sizes']['large'] ?? $video_poster['url']) }}" alt="" decoding="async">
                            @else
                            <span class="text-media__video-placeholder" aria-hidden="true"></span>
                            @endif
                            <span class="text-media__video-play" aria-hidden="true"></span>
                        </span>
                    @if($is_preview)
                    </div>
                    @else
                    </a>
                    @endif
                    @if($video_caption)
                    <p class="text-media__video-caption" id="{{ esc_attr($caption_id) }}">{{ esc_html($video_caption) }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
