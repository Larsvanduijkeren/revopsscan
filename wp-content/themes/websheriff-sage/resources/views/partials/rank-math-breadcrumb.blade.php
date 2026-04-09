@php
    $wrapper_class = $wrapper_class ?? null;
@endphp
@unless(is_front_page())
@if($wrapper_class)
<div class="{{ esc_attr($wrapper_class) }}">
@endif
    {!! do_shortcode('[rank_math_breadcrumb]') !!}
@if($wrapper_class)
</div>
@endif
@endunless
