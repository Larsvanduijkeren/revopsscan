<?php

declare(strict_types=1);

namespace App\Schema;

use WP_Post;

/**
 * Builds Schema.org graphs for singular templates and archives where useful.
 */
final class RichResultsBuilder
{
    public static function organizationId(): string
    {
        return trailingslashit(home_url('/')) . '#organization';
    }

    public static function websiteId(): string
    {
        return trailingslashit(home_url('/')) . '#website';
    }

    /**
     * @return list<array{name: string, url: string}>
     */
    public static function breadcrumbItemsForPost(WP_Post $post): array
    {
        $items = [
            ['name' => get_bloginfo('name'), 'url' => home_url('/')],
        ];
        $blog_page_id = (int) get_option('page_for_posts');
        if ($blog_page_id > 0) {
            $items[] = [
                'name' => get_the_title($blog_page_id),
                'url' => get_permalink($blog_page_id),
            ];
        } else {
            $items[] = [
                'name' => __('Nieuws', 'sage'),
                'url' => home_url('/nieuws'),
            ];
        }
        $items[] = [
            'name' => get_the_title($post),
            'url' => get_permalink($post),
        ];

        return $items;
    }

    /**
     * @return list<array{name: string, url: string}>
     */
    public static function breadcrumbItemsForPage(WP_Post $post): array
    {
        if ((int) $post->ID === (int) get_option('page_on_front')) {
            return [];
        }
        $items = [
            ['name' => get_bloginfo('name'), 'url' => home_url('/')],
        ];
        $ancestor_ids = get_post_ancestors($post);
        foreach (array_reverse($ancestor_ids) as $ancestor_id) {
            $items[] = [
                'name' => get_the_title($ancestor_id),
                'url' => get_permalink($ancestor_id),
            ];
        }
        $items[] = [
            'name' => get_the_title($post),
            'url' => get_permalink($post),
        ];

        return $items;
    }

    public static function blogPosting(WP_Post $post): array
    {
        $author_id = (int) $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id) ?: '';
        $permalink = get_permalink($post);
        $webpage_id = $permalink . '#webpage';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
            'url' => $permalink,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $webpage_id,
                'url' => $permalink,
            ],
            'publisher' => ['@id' => self::organizationId()],
        ];

        if (has_post_thumbnail($post)) {
            $img = get_the_post_thumbnail_url($post, 'large');
            if ($img) {
                $schema['image'] = [$img];
            }
        }

        $summary = function_exists('get_field') ? get_field('summary', $post->ID) : null;
        if (is_string($summary) && $summary !== '') {
            $schema['description'] = wp_strip_all_tags($summary);
        } else {
            $excerpt = get_the_excerpt($post);
            if ($excerpt !== '') {
                $schema['description'] = wp_strip_all_tags($excerpt);
            }
        }

        $categories = get_the_category($post->ID);
        if (! empty($categories[0]->name)) {
            $schema['articleSection'] = $categories[0]->name;
        }

        if ($author_name !== '') {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $author_name,
            ];
        }

        $plain = wp_strip_all_tags((string) apply_filters('the_content', $post->post_content));
        if ($plain !== '') {
            $schema['wordCount'] = str_word_count($plain);
        }

        return $schema;
    }

    public static function webPage(WP_Post $post): array
    {
        $permalink = get_permalink($post);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => $permalink . '#webpage',
            'url' => $permalink,
            'name' => get_the_title($post),
            'dateModified' => get_the_modified_date('c', $post),
            'isPartOf' => ['@id' => self::websiteId()],
            'publisher' => ['@id' => self::organizationId()],
        ];
    }
}
