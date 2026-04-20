@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$intro = $fields['intro'] ?? null;
$plans = $fields['plans'] ?? null;
$plans_for_display = [];
if (! empty($plans) && is_array($plans)) {
    foreach ($plans as $p) {
        if (is_array($p) && trim((string) ($p['plan_title'] ?? '')) !== '') {
            $plans_for_display[] = $p;
        }
    }
}
$plan_count = count($plans_for_display);
$show_promo_banner = !empty($fields['show_promo_banner']);
$promo_text = $fields['promo_text'] ?? null;
$promo_button = $fields['promo_button'] ?? null;
$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="pricing-plans has-waves">
    <div class="container">
        <div class="intro center" data-aos="fade-up">
            <x-split-badge :text="$label" />
            @if($title)
            <h2>{{ esc_html($title) }}</h2>
            @endif
            @if($intro)
            <div class="intro-text">{!! wp_kses_post($intro) !!}</div>
            @endif
        </div>

        @if($plan_count > 0)
        <div class="pricing-plans__grid{{ $plan_count === 2 ? ' pricing-plans__grid--two' : '' }}" data-aos="fade-up">
            @foreach($plans_for_display as $plan)
            @php
            $planTitle = $plan['plan_title'] ?? '';
            $isFeatured = !empty($plan['is_featured']);
            $badgeText = $plan['badge_text'] ?? '';
            $planDescription = $plan['plan_description'] ?? '';
            $price = $plan['price'] ?? '';
            $priceSuffix = $plan['price_suffix'] ?? '';
            $priceNote = $plan['price_note'] ?? '';
            $features = $plan['features'] ?? [];
            $cta = $plan['cta'] ?? null;
            $ctaUrl = is_array($cta) ? ($cta['url'] ?? '') : '';
            $ctaTitle = is_array($cta) ? ($cta['title'] ?? '') : '';
            $ctaTarget = is_array($cta) ? ($cta['target'] ?? '_self') : '_self';
            @endphp
            <article class="pricing-card {{ $isFeatured ? 'is-featured' : '' }}">
                @if($isFeatured && $badgeText !== '')
                <x-split-badge :text="$badgeText" class="pricing-card__badge" />
                @endif
                <div class="pricing-card__body">
                    <h3 class="h4 pricing-card__title">{{ esc_html($planTitle) }}</h3>
                    @if($planDescription !== '')
                    <div class="pricing-card__description">{!! wp_kses_post($planDescription) !!}</div>
                    @endif
                    @if($price !== '' || $priceSuffix !== '')
                    <div class="pricing-card__price-row">
                        @if($price !== '')
                        <span class="pricing-card__amount h2">{{ esc_html($price) }}</span>
                        @endif
                        @if($priceSuffix !== '')
                        <span class="pricing-card__suffix">{{ esc_html($priceSuffix) }}</span>
                        @endif
                    </div>
                    @endif
                    @if($priceNote !== '')
                    <div class="pricing-card__note">{!! wp_kses_post($priceNote) !!}</div>
                    @endif
                    <div class="pricing-card__divider" role="presentation"></div>
                    @if(!empty($features) && is_array($features))
                    <ul class="pricing-card__features">
                        @foreach($features as $featureRow)
                        @php $ft = $featureRow['feature_text'] ?? ''; @endphp
                        @if($ft !== '')
                        <li>{{ esc_html($ft) }}</li>
                        @endif
                        @endforeach
                    </ul>
                    @endif
                    @if($ctaUrl !== '' && $ctaTitle !== '')
                    <div class="pricing-card__actions">
                        @if($isFeatured)
                        <a href="{{ esc_url($ctaUrl) }}" class="btn" target="{{ esc_attr($ctaTarget) }}" rel="{{ $ctaTarget === '_blank' ? 'noopener noreferrer' : '' }}">{{ esc_html($ctaTitle) }}</a>
                        @else
                        <a href="{{ esc_url($ctaUrl) }}" class="btn-ghost" target="{{ esc_attr($ctaTarget) }}" rel="{{ $ctaTarget === '_blank' ? 'noopener noreferrer' : '' }}">{{ esc_html($ctaTitle) }}</a>
                        @endif
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        @endif

        @if($show_promo_banner && ($promo_text || (!empty($promo_button['url']) && !empty($promo_button['title']))))
        <div class="pricing-plans__banner" data-aos="fade-up">
            <div class="pricing-plans__banner-inner">
                @if($promo_text)
                <div class="pricing-plans__banner-main">
                    <div class="pricing-plans__banner-text">{!! wp_kses_post($promo_text) !!}</div>
                </div>
                @endif
                @if(!empty($promo_button['url']) && !empty($promo_button['title']))
                <a href="{{ esc_url($promo_button['url']) }}" class="btn forward pricing-plans__banner-btn" target="{{ esc_attr($promo_button['target'] ?? '_self') }}" rel="{{ ($promo_button['target'] ?? '') === '_blank' ? 'noopener noreferrer' : '' }}">{{ esc_html($promo_button['title']) }}</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</section>
