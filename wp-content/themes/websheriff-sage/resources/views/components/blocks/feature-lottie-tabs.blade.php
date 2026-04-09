@php
$section_title = $fields['section_title'] ?? null;
$panel_label = $fields['panel_label'] ?? null;
$panel_badge = $fields['panel_badge'] ?? null;
$panel_title = $fields['panel_title'] ?? null;
$show_panel_arrow = filter_var($fields['show_panel_arrow'] ?? true, FILTER_VALIDATE_BOOLEAN)
    || ($fields['show_panel_arrow'] ?? null) === 1
    || ($fields['show_panel_arrow'] ?? null) === '1';
$items = is_array($fl_tabs_items ?? null) ? $fl_tabs_items : [];
$id = $block['anchor'] ?? null;
$first_tint = $items[0]['stage_tint'] ?? 'peach';
$panel_dom_id = 'flt-panel-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($block['id'] ?? 'block'));
@endphp
@if(!empty($items))
<section
    @if($id) id="{{ $id }}" @endif
    class="feature-lottie-tabs has-waves"
    data-feature-lottie-tabs
    data-lottie-placeholder="{{ esc_attr(__('Add a Lottie JSON file or a stage image for each tab.', 'sage')) }}">
    <div class="container">
        @if($section_title)
        <h2 class="feature-lottie-tabs__section-title h2" data-aos="fade-up">{{ esc_html($section_title) }}</h2>
        @endif

        <div class="feature-lottie-tabs__panel" data-aos="fade-up">
            <div class="feature-lottie-tabs__nav">
                <header class="feature-lottie-tabs__nav-head">
                    @if($panel_label || $panel_badge)
                    <p class="feature-lottie-tabs__nav-kicker">
                        @if($panel_label)
                        <span class="feature-lottie-tabs__nav-label">{{ esc_html($panel_label) }}</span>
                        @endif
                        @if($panel_badge)
                        <span class="feature-lottie-tabs__badge">{{ esc_html($panel_badge) }}</span>
                        @endif
                    </p>
                    @endif
                    <div class="feature-lottie-tabs__nav-head-row">
                        @if($panel_title)
                        <h3 class="feature-lottie-tabs__nav-title">{{ esc_html($panel_title) }}</h3>
                        @endif
                        @if($show_panel_arrow)
                        <span class="feature-lottie-tabs__nav-arrow" aria-hidden="true">
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        @endif
                    </div>
                </header>

                <ul class="feature-lottie-tabs__list" role="tablist" aria-label="{{ esc_attr($panel_title ?: __('Features', 'sage')) }}">
                    @foreach($items as $index => $item)
                    @php
                    $tab_id = $item['tab_id'] ?? 'flt-tab-' . $index;
                    $is_active = $index === 0;
                    $lottie_url = $item['lottie_url'] ?? '';
                    $stage_image_url = $item['stage_image_url'] ?? '';
                    $stage_image_alt = $item['stage_image_alt'] ?? '';
                    $tone = $item['icon_tone'] ?? 'orange';
                    $stage_tint = $item['stage_tint'] ?? 'peach';
                    $fa = $item['icon_class'] ?? '';
                    $desc = $item['item_description'] ?? '';
                    @endphp
                    <li class="feature-lottie-tabs__item" role="none">
                        <button
                            type="button"
                            class="feature-lottie-tabs__tab{{ $is_active ? ' is-active' : '' }}"
                            id="{{ esc_attr($tab_id) }}-btn"
                            role="tab"
                            aria-selected="{{ $is_active ? 'true' : 'false' }}"
                            aria-controls="{{ esc_attr($panel_dom_id) }}"
                            tabindex="{{ $is_active ? '0' : '-1' }}"
                            data-flt-tab
                            data-lottie-url="{{ esc_url($lottie_url) }}"
                            data-image-url="{{ esc_url($stage_image_url) }}"
                            data-image-alt="{{ esc_attr($stage_image_alt) }}"
                            data-stage-tint="{{ esc_attr($stage_tint) }}">
                            <span class="feature-lottie-tabs__tab-main">
                                @if($fa !== '')
                                <span class="feature-lottie-tabs__icon feature-lottie-tabs__icon--{{ esc_attr($tone) }}" aria-hidden="true">
                                    <i class="{{ esc_attr($fa) }}"></i>
                                </span>
                                @endif
                                <span class="feature-lottie-tabs__tab-text">
                                    <span class="feature-lottie-tabs__tab-title">{{ esc_html($item['item_title'] ?? '') }}</span>
                                    @if($desc !== '')
                                    <span class="feature-lottie-tabs__tab-desc">{!! wp_kses_post($desc) !!}</span>
                                    @endif
                                </span>
                            </span>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div
                class="feature-lottie-tabs__stage"
                data-lottie-stage
                data-tint="{{ esc_attr($first_tint) }}"
                id="{{ esc_attr($panel_dom_id) }}"
                role="tabpanel"
                aria-live="polite"
                aria-labelledby="{{ esc_attr(($items[0]['tab_id'] ?? 'flt-tab-0') . '-btn') }}">
                <div class="feature-lottie-tabs__lottie" data-lottie-host></div>
            </div>
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="feature-lottie-tabs feature-lottie-tabs--empty has-waves">
    <div class="container">
        <p class="feature-lottie-tabs__empty-msg">{{ esc_html(__('Add at least one tab with a title in the sidebar.', 'sage')) }}</p>
    </div>
</section>
@endif
