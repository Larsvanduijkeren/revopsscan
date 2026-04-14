@props([
    'text' => null,
    'class' => '',
])
@if($text)
<p @class(['split-badge', $class])>
    <span class="split-badge__dot" aria-hidden="true"></span>
    <span class="split-badge__text">{{ esc_html($text) }}</span>
</p>
@endif
