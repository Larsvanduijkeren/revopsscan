@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$reviews_label = trim((string) ($fields['reviews_label'] ?? ''));
if ($reviews_label === '') {
    $reviews_label = __('What our customers say', 'sage');
}
$promo_link = $fields['promo_link'] ?? null;
$promo_link_url = is_array($promo_link) ? (string) ($promo_link['url'] ?? '') : '';
$promo_link_title = is_array($promo_link) ? (string) ($promo_link['title'] ?? '') : '';
$promo_link_target = is_array($promo_link) ? (string) ($promo_link['target'] ?? '') : '';
$promo_image = $fields['promo_image'] ?? null;
$promo_has_media = is_array($promo_image) && !empty($promo_image['url'] ?? null);
$reviews = $reviews ?? [];
$id = $block['anchor'] ?? null;
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="review-selection has-waves">
    <div class="container">
        <div class="review-selection__grid">
            <aside class="review-selection__promo {{ $promo_has_media ? 'review-selection__promo--has-media' : '' }}" data-aos="fade-up">
                <div class="review-selection__promo-copy">
                    <x-split-badge :text="$label" />
                    @if($title)
                    <h2 class="review-selection__promo-title">{{ esc_html($title) }}</h2>
                    @endif
                    @if($promo_link_url !== '')
                    <a
                        class="btn btn-accent review-selection__promo-cta"
                        href="{{ esc_url($promo_link_url) }}"
                        @if($promo_link_target !== '') target="{{ esc_attr($promo_link_target) }}" rel="{{ $promo_link_target === '_blank' ? 'noopener noreferrer' : '' }}" @endif
                    >{{ $promo_link_title !== '' ? esc_html($promo_link_title) : __('Learn more', 'sage') }}</a>
                    @endif
                </div>
                @if($promo_has_media)
                @php
                $promoImgW = (int) ($promo_image['width'] ?? 0);
                $promoImgH = (int) ($promo_image['height'] ?? 0);
                @endphp
                <div class="review-selection__promo-media" aria-hidden="true">
                    <img
                        src="{{ esc_url($promo_image['sizes']['large'] ?? $promo_image['url']) }}"
                        alt=""
                        loading="lazy"
                        decoding="async"
                        @if($promoImgW > 0) width="{{ $promoImgW }}" @endif
                        @if($promoImgH > 0) height="{{ $promoImgH }}" @endif>
                </div>
                @endif
            </aside>

            @if(!empty($reviews))
            <div class="review-selection__reviews" data-aos="fade-up">
                <p class="review-selection__reviews-kicker">{{ esc_html($reviews_label) }}</p>
                <div class="review-selection__reviews-panel">
                    <div class="swiper review-selection-swiper">
                        <div class="swiper-wrapper">
                            @foreach($reviews as $row)
                            @php
                            $post = $row['post'];
                            $quote = $row['quote'] ?? '';
                            $roleLine = $row['role_line'] ?? '';
                            $name = get_the_title($post);
                            @endphp
                            <div class="swiper-slide">
                                <article class="review-card">
                                    @if($quote !== '')
                                    <blockquote class="review-card__quote">
                                        <p>{!! wp_kses_post($quote) !!}</p>
                                    </blockquote>
                                    @endif
                                    @if($name !== '' || $roleLine !== '')
                                    <p class="review-card__byline">
                                        @if($name !== '')<span class="review-card__byline-name">{{ esc_html($name) }}</span>@endif
                                        @if($name !== '' && $roleLine !== '')<span class="review-card__byline-sep" aria-hidden="true"> · </span>@endif
                                        @if($roleLine !== '')<span class="review-card__byline-role">{{ esc_html($roleLine) }}</span>@endif
                                    </p>
                                    @endif
                                </article>
                            </div>
                            @endforeach
                        </div>
                        <div class="review-selection__controls">
                            <button type="button" class="swiper-button-prev review-selection__nav-btn" aria-label="{{ __('Previous testimonial', 'sage') }}"></button>
                            <div class="swiper-pagination review-selection__pagination"></div>
                            <button type="button" class="swiper-button-next review-selection__nav-btn" aria-label="{{ __('Next testimonial', 'sage') }}"></button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
