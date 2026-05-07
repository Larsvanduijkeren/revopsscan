@php
$id = $block['anchor'] ?? null;
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$slides = $fields['items'] ?? [];
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="gallery">
    <div class="container">
        @if($title)
        <div class="intro" data-aos="fade-up">
            @if($label)
            <span class="label">{{ $label }}</span>
            @endif

            <h2>{{ $title }}</h2>

            @if($text)
            {!! $text !!}
            @endif
        </div>
        @endif

        @if(!empty($slides) && is_array($slides))
        <div class="slider swiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($slides as $item)
                <div class="swiper-slide gallery-item">
                    @if(($item['type'] ?? 'image') === 'video' && !empty($item['vimeo_id']))
                        <iframe
                            src="https://player.vimeo.com/video/{{ $item['vimeo_id'] }}?background=1"
                            frameborder="0"
                            allow="fullscreen"
                            allowfullscreen></iframe>
                    @elseif(!empty($item['image']))
                    <div class="image">
                        <img src="{{ $item['image']['sizes']['full'] ?? $item['image']['url'] }}" alt="{{ $item['image']['alt'] ?? '' }}">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="swiper-scrollbar"></div>
        </div>
        @endif
    </div>
</section>
