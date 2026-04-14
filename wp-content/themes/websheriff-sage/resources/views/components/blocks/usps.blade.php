@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$items_raw = $fields['items'] ?? null;
$items = is_array($items_raw) ? array_values(array_filter($items_raw, static function ($row): bool {
    return is_array($row) && trim((string) ($row['usp_title'] ?? '')) !== '';
})) : [];
$id = $block['anchor'] ?? null;
$col_count = min(4, max(1, count($items)));
@endphp
@if(!empty($items))
<section
    @if($id) id="{{ $id }}" @endif
    class="usps has-waves">
    <div class="container">
        @if($label || $title)
        <div class="intro" data-aos="fade-up">
            <x-split-badge :text="$label" />
            @if($title)
            <h2 class="usps__headline h2">{{ esc_html($title) }}</h2>
            @endif
        </div>
        @endif
        <div class="usps__grid" style="--usps-cols: {{ (int) $col_count }};" data-aos="fade-up">
            @foreach($items as $item)
            @php
            $fa = \App\sanitize_fa_icon_classes((string) ($item['icon_class'] ?? ''));
            $usp_title = $item['usp_title'] ?? '';
            $usp_text = $item['usp_text'] ?? '';
            @endphp
            <article class="usp-card">
                @if($fa !== '')
                <div class="usp-card__icon" aria-hidden="true">
                    <i class="{{ esc_attr($fa) }}"></i>
                </div>
                @endif
                <h3 class="usp-card__title">{{ esc_html($usp_title) }}</h3>
                @if($usp_text !== '')
                <div class="usp-card__text">{!! wp_kses_post($usp_text) !!}</div>
                @endif
            </article>
            @endforeach
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="usps usps--empty has-waves" aria-label="{{ esc_attr(__('USPs', 'sage')) }}">
    <div class="container">
        <p class="usps__empty-msg">{{ esc_html(__('Add USPs in the sidebar.', 'sage')) }}</p>
    </div>
</section>
@endif
