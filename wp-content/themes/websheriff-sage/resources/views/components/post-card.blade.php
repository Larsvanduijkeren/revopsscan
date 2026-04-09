@php
$permalink = get_permalink($post);
$summary = get_field('summary', $post);
$title = get_the_title($post);
$reading = \App\reading_time_label($post);
$in_slider = filter_var($in_slider ?? false, FILTER_VALIDATE_BOOLEAN);

// Primary category: Yoast primary, or first assigned category
$primary_cat = null;
$post_id = $post->ID ?? get_the_ID();
if (class_exists('WPSEO_Primary_Term')) {
    $yoast_primary = (new \WPSEO_Primary_Term('category', $post_id))->get_primary_term();
    if ($yoast_primary) {
        $term = get_term((int) $yoast_primary, 'category');
        if ($term instanceof \WP_Term && ! is_wp_error($term)) {
            $primary_cat = $term;
        }
    }
}
if (!$primary_cat) {
    $categories = get_the_terms($post_id, 'category');
    if ($categories && !is_wp_error($categories) && !empty($categories)) {
        $primary_cat = $categories[0];
    }
}

$archive_base = null;
if (isset($archive_base_url) && is_string($archive_base_url) && $archive_base_url !== '') {
    $archive_base = untrailingslashit($archive_base_url);
} elseif (get_option('page_for_posts')) {
    $archive_base = untrailingslashit((string) get_permalink((int) get_option('page_for_posts')));
} else {
    $archive_base = untrailingslashit(home_url('/nieuws'));
}
$category_url = $primary_cat ? add_query_arg('archive_cat', $primary_cat->slug, trailingslashit($archive_base)) : null;
@endphp

<article class="post-card{{ $in_slider ? ' swiper-slide' : '' }}">
    @if(has_post_thumbnail($post))
    <div class="image">
        @if($primary_cat && $category_url)
        <a href="{{ esc_url($category_url) }}" class="badge">{{ esc_html($primary_cat->name) }}</a>
        @endif
        {!! get_the_post_thumbnail($post, 'big') !!}
    </div>
    @endif

    <a href="{{ esc_url($permalink) }}">
        <span class="content">
            <span class="label">{{ esc_html(__('Nieuws', 'sage')) }}</span>
            <h3>
                {{ esc_html($title) }}
            </h3>

            @if($summary)
            <p>{{ esc_html($summary) }}</p>
            @endif

            <span class="wrap">
                <span class="reading-time">{{ esc_html($reading) }}</span>
                <span class="arrow"></span>
            </span>
        </span>
    </a>
</article>
