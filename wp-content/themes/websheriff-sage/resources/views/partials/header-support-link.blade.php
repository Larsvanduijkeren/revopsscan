@if(!empty($headerSupport['url']))
<a
    class="header-support-link"
    href="{{ esc_url($headerSupport['url']) }}"
    target="{{ esc_attr($headerSupport['target'] ?? '_self') }}"
>
    <i class="{{ esc_attr($headerSupportIconClass) }}" aria-hidden="true"></i>
    <span>{{ !empty($headerSupport['title']) ? $headerSupport['title'] : __('Support', 'sage') }}</span>
</a>
@endif
