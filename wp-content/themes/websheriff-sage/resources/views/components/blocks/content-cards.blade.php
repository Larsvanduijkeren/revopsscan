@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$cards = $fields['cards'] ?? null;
$card_layout = $fields['card_layout'] ?? 'grid-2';
if (!in_array($card_layout, ['grid-2', 'grid-3', 'slider'], true)) {
    $card_layout = 'grid-2';
}

$id = $block['anchor'] ?? null;
$slider_label = $title ?: __('Content cards', 'sage');
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="content-cards content-cards--{{ $card_layout }} has-waves">
    <div class="container">
        <div class="intro center" data-aos="fade-up">
            @if($label)
            <span class="label">{{ $label }}</span>
            @endif

            @if($title)
            <h2>{{ $title }}</h2>
            @endif

            @if($text)
            <div class="intro-text">{!! $text !!}</div>
            @endif
        </div>

        @if(!empty($cards) && is_array($cards))
            @if($card_layout === 'slider')
            <div class="slider" data-aos="fade-up">
                <div class="swiper content-cards-swiper" aria-label="{{ esc_attr($slider_label) }}">
                    <div class="swiper-wrapper">
                        @foreach($cards as $card)
                        <div class="swiper-slide">
                            @include('components.content-card-item', ['card' => $card])
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-scrollbar"></div>
                </div>
            </div>
            @else
            <div class="cards" data-aos="fade-up">
                @foreach($cards as $card)
                    @include('components.content-card-item', ['card' => $card])
                @endforeach
            </div>
            @endif
        @endif
    </div>
</section>
