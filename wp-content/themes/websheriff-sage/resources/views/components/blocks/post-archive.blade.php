@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$id = $block['anchor'] ?? null;

$query = $query ?? null;
$archive_base_url = $archive_base_url ?? '';
$archive_categories = is_array($archive_categories ?? null) ? $archive_categories : [];
$current_archive_cat = $current_archive_cat ?? '';
$show_category_filter = ! empty($show_category_filter);
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="post-archive has-waves">
    <div class="container">
        <div class="intro center" data-aos="fade-up">
            @if($label)
            <span class="label">{{ esc_html($label) }}</span>
            @endif

            @if($title)
            <h1>{{ esc_html($title) }}</h1>
            @endif

            @if($text)
            {!! $text !!}
            @endif

            @if($buttons)
            <div class="buttons">
                @foreach($buttons as $button)
                @php
                $button_obj = $button['button'] ?? $button;
                $url = $button_obj['url'] ?? null;
                $button_title = $button_obj['title'] ?? null;
                $target = $button_obj['target'] ?? '_self';
                @endphp
                @if($url && $button_title)
                <a
                    href="{{ esc_url($url) }}"
                    target="{{ esc_attr($target) }}"
                    class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}"
                    rel="{{ $target === '_blank' ? 'noopener noreferrer' : '' }}">
                    {{ esc_html($button_title) }}
                </a>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        @if($show_category_filter && !empty($archive_categories))
        <nav class="post-archive__filters" aria-label="{{ esc_attr(__('Filter by category', 'sage')) }}" data-aos="fade-up">
            @php
            $all_url = remove_query_arg(['archive_cat', 'paged', 'page'], $archive_base_url);
            @endphp
            <ul class="post-archive__filter-list">
                <li class="post-archive__filter-item">
                    <a
                        href="{{ esc_url($all_url) }}"
                        class="post-archive__filter-link {{ $current_archive_cat === '' ? 'is-active' : '' }}">
                        {{ esc_html(__('Alles', 'sage')) }}
                    </a>
                </li>
                @foreach($archive_categories as $term)
                @if($term instanceof \WP_Term)
                @php
                $filter_url = add_query_arg([
                    'archive_cat' => $term->slug,
                    'paged' => false,
                    'page' => false,
                ], $archive_base_url);
                @endphp
                <li class="post-archive__filter-item">
                    <a
                        href="{{ esc_url($filter_url) }}"
                        class="post-archive__filter-link {{ $current_archive_cat === $term->slug ? 'is-active' : '' }}">
                        {{ esc_html($term->name) }}
                    </a>
                </li>
                @endif
                @endforeach
            </ul>
        </nav>
        @endif

        @if(isset($query) && $query instanceof \WP_Query && $query->have_posts())
        <div class="post-archive__cards" data-aos="fade-up">
            @while($query->have_posts())
            @php $query->the_post(); $card_post = get_post(); @endphp
            @include('components.post-card', ['post' => $card_post, 'in_slider' => false, 'archive_base_url' => $archive_base_url])
            @endwhile
            @php wp_reset_postdata(); @endphp
        </div>
        @include('partials.pagination', ['query' => $query, 'pagination_base' => $archive_base_url])
        @elseif(!empty($is_preview))
        <p class="post-archive__empty">{{ esc_html(__('No posts match this filter.', 'sage')) }}</p>
        @else
        <p class="post-archive__empty">{{ esc_html(__('No posts found.', 'sage')) }}</p>
        @endif
    </div>
</section>
