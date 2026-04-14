@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$reviews = $reviews ?? [];
$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="review-selection has-waves">
    <div class="container">
        <div class="intro" data-aos="fade-up">
            <x-split-badge :text="$label" />
            @if($title)
            <h2>{{ esc_html($title) }}</h2>
            @endif
        </div>

        @if(!empty($reviews))
        <div class="slider overflow-wrap" data-aos="fade-up">
            <div class="swiper review-selection-swiper">
                <div class="swiper-wrapper">
                    @foreach($reviews as $row)
                    @php
                    $post = $row['post'];
                    $rating = (int) ($row['rating'] ?? 5);
                    $quote = $row['quote'] ?? '';
                    $roleLine = $row['role_line'] ?? '';
                    $initials = $row['initials'] ?? '';
                    $name = get_the_title($post);
                    @endphp
                    <div class="swiper-slide">
                        <article class="review-card">
                            <div class="review-card__stars" role="img" aria-label="{{ sprintf(__('%d out of 5 stars', 'sage'), $rating) }}">
                                @for($i = 1; $i <= 5; $i++)
                                <span class="review-card__star {{ $i <= $rating ? 'is-filled' : '' }}" aria-hidden="true">★</span>
                                @endfor
                            </div>
                            @if($quote !== '')
                            <p class="review-card__quote">{!! wp_kses_post($quote) !!}</p>
                            @endif
                            <footer class="review-card__footer">
                                <span class="review-card__avatar" aria-hidden="true">{{ esc_html($initials) }}</span>
                                <div class="review-card__meta">
                                    @if($name !== '')
                                    <p class="review-card__name h4">{{ esc_html($name) }}</p>
                                    @endif
                                    @if($roleLine !== '')
                                    <p class="review-card__role">{{ esc_html($roleLine) }}</p>
                                    @endif
                                </div>
                            </footer>
                        </article>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </div>
        @endif
    </div>
</section>
