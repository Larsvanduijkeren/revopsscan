@php
    $hasPrimary = !empty($headerBtnPrimary['url']) && !empty($headerBtnPrimary['title']);
    $hasSecondary = !empty($headerBtnSecondary['url']) && !empty($headerBtnSecondary['title']);
@endphp
@if($hasPrimary || $hasSecondary)
<div class="header-buttons">
    @if($hasSecondary)
    <a
        class="btn small"
        href="{{ esc_url($headerBtnSecondary['url']) }}"
        target="{{ esc_attr($headerBtnSecondary['target'] ?? '_self') }}"
        rel="{{ ($headerBtnSecondary['target'] ?? '') === '_blank' ? 'noopener noreferrer' : '' }}"
    >{{ esc_html($headerBtnSecondary['title']) }}</a>
    @endif
    @if($hasPrimary)
    <a
        class="btn btn-accent small"
        href="{{ esc_url($headerBtnPrimary['url']) }}"
        target="{{ esc_attr($headerBtnPrimary['target'] ?? '_self') }}"
        rel="{{ ($headerBtnPrimary['target'] ?? '') === '_blank' ? 'noopener noreferrer' : '' }}"
    >{{ esc_html($headerBtnPrimary['title']) }}</a>
    @endif
</div>
@endif
