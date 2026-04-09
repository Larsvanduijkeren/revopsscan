@php
$card = $card ?? [];
$card_image = $card['image'] ?? null;
$card_title = $card['title'] ?? null;
$card_text = $card['text'] ?? null;
$card_button = $card['button'] ?? null;
@endphp
<div class="content-card">
    @if(!empty($card_image))
    <div class="image">
        <img src="{{ $card_image['sizes']['large'] ?? $card_image['url'] }}" alt="{{ $card_image['alt'] ?? '' }}">
    </div>
    @endif
    <div class="content">
        @if($card_title)
        <h3 class="h4">{{ $card_title }}</h3>
        @endif
        @if($card_text)
        <div class="summary">{!! $card_text !!}</div>
        @endif
        @if(!empty($card_button['url']) && !empty($card_button['title']))
        <a href="{{ $card_button['url'] }}" target="{{ $card_button['target'] ?? '_self' }}" class="btn">{{ $card_button['title'] }}</a>
        @endif
    </div>
</div>
