@php
$styleRaw = $fields['style'] ?? 'default';
$style = in_array($styleRaw, ['default', 'accent'], true) ? $styleRaw : 'default';
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;

$id = $block['anchor'] ?? null;
@endphp

@if($title)
<section
    @if($id) id="{{ $id }}" @endif
    class="cta-banner cta-banner--{{ $style }} has-waves">
    <div class="container">
        <div class="cta-banner__card" data-aos="fade-up">
            <div class="cta-banner__content">
                <x-split-badge :text="$label" />

                <h2 class="cta-banner__title">{{ esc_html($title) }}</h2>

                @if($text)
                <div class="cta-banner__text">{!! wp_kses_post($text) !!}</div>
                @endif
            </div>

            @if($buttons)
            @php
            $renderable_buttons = [];
            foreach ($buttons as $button) {
                $button_obj = $button['button'] ?? $button;
                $url = $button_obj['url'] ?? null;
                $button_title = $button_obj['title'] ?? null;
                if ($url && $button_title) {
                    $renderable_buttons[] = [
                        'url'    => $url,
                        'title'  => $button_title,
                        'target' => $button_obj['target'] ?? '_self',
                    ];
                }
            }
            @endphp

            @if(!empty($renderable_buttons))
            <div class="cta-banner__buttons">
                @foreach($renderable_buttons as $i => $cta_button)
                <a
                    href="{{ esc_url($cta_button['url']) }}"
                    target="{{ esc_attr($cta_button['target']) }}"
                    rel="{{ $cta_button['target'] === '_blank' ? 'noopener noreferrer' : '' }}"
                    class="{{ $i === 0 ? 'btn' : 'btn-ghost' }}">
                    {{ esc_html($cta_button['title']) }}
                </a>
                @endforeach
            </div>
            @endif
            @endif
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="cta-banner cta-banner--empty has-waves" aria-label="{{ esc_attr(__('CTA banner', 'sage')) }}">
    <div class="container">
        <p class="cta-banner__empty-msg">{{ esc_html(__('Add a title (and optional buttons) in the sidebar.', 'sage')) }}</p>
    </div>
</section>
@endif
