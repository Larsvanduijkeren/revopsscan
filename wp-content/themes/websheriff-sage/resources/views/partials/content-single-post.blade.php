@php
$summary = get_field('summary', $post->ID);
$reading = \App\reading_time_label($post);
$meta = '<span class="date">' . get_the_date('j F, Y', $post) . '</span><span class="reading-time">' . esc_html($reading) . '</span>';

$has_h2_index = (bool) preg_match('/<h2[\s>]/i', (string) ($post->post_content ?? ''));

$author_id = (int) $post->post_author;
$author_name = get_the_author_meta('display_name', $author_id);
$author_bio = trim((string) get_the_author_meta('description', $author_id));

$related_posts = [];
$cat_ids = wp_get_post_categories($post->ID, ['fields' => 'ids']);
$related_args = [
    'post_type' => 'post',
    'posts_per_page' => 8,
    'post__not_in' => [$post->ID],
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
];
if (! empty($cat_ids)) {
    $related_args['category__in'] = $cat_ids;
}
$related_query = new \WP_Query($related_args);
if ($related_query->have_posts()) {
    $related_posts = $related_query->posts;
}
if (function_exists('update_post_caches') && ! empty($related_posts)) {
    update_post_caches($related_posts);
}
@endphp

@include('partials.single-hero', [
    'label'      => __('Nieuws', 'sage'),
    'title'      => get_the_title($post),
    'summary'    => $summary ? '<p>' . esc_html($summary) . '</p>' : null,
    'meta'       => $meta,
    'button'     => ['url' => get_option('page_for_posts') ? get_permalink(get_option('page_for_posts')) : home_url('/nieuws'), 'title' => __('Alle artikelen', 'sage')],
    'image'      => $post,
    'back_url'   => null,
    'back_label' => null,
])

<section class="post-content">
    <div class="container">
        <div class="flex-wrapper" data-aos="fade-up">
            <aside class="post-content__aside{{ $has_h2_index ? '' : ' post-content__aside--no-index' }}">
                @if($has_h2_index)
                <h3 class="h4 post-content__index-title">{{ __('Op deze pagina', 'sage') }}</h3>
                <div class="index"></div>
                @endif

                <div class="post-author-card">
                    <div class="post-author-card__avatar">
                        {!! get_avatar($author_id, 96, '', esc_attr($author_name)) !!}
                    </div>
                    <div class="post-author-card__body">
                        <p class="post-author-card__label">{{ __('Auteur', 'sage') }}</p>
                        <p class="post-author-card__name">{{ esc_html($author_name) }}</p>
                        @if($author_bio !== '')
                        <p class="post-author-card__bio">{{ esc_html(wp_strip_all_tags($author_bio)) }}</p>
                        @endif
                    </div>
                </div>

                <div class="meta">
                    <span class="date">{{ get_the_date('j F, Y') }}</span>
                    <span class="reading-time">{{ esc_html($reading) }}</span>
                </div>
            </aside>
            <div class="content">
                {!! apply_filters('the_content', $post->post_content) !!}
            </div>
        </div>
    </div>
</section>

@if(!empty($related_posts))
@include('components.post-selection-section', [
    'selection_posts' => $related_posts,
    'label' => null,
    'title' => __('Gerelateerde artikelen', 'sage'),
    'intro' => null,
    'section_id' => null,
    'section_class' => 'post-selection--related',
    'is_preview' => false,
    'archive_base_url' => get_option('page_for_posts') ? get_permalink((int) get_option('page_for_posts')) : null,
])
@endif
