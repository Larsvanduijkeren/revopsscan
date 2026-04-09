<?php

declare(strict_types=1);

namespace App\Providers;

use App\Schema\JsonLd;
use App\Schema\RichResultsBuilder;
use Roots\Acorn\Sage\SageServiceProvider;
use WP_Post;

class RichResultsServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        add_action('wp_head', [$this, 'outputSingularSchemas'], 8);
        add_action('wp_head', [$this, 'outputBlogIndexItemList'], 9);
    }

    public function outputSingularSchemas(): void
    {
        if (! is_singular()) {
            return;
        }
        $post = get_queried_object();
        if (! $post instanceof WP_Post) {
            return;
        }

        if (is_singular('post')) {
            $crumbs = JsonLd::breadcrumbList(RichResultsBuilder::breadcrumbItemsForPost($post));
            if ($crumbs !== []) {
                JsonLd::print($crumbs);
            }
            JsonLd::print(RichResultsBuilder::blogPosting($post));

            return;
        }

        if (is_singular('page')) {
            $crumbs = JsonLd::breadcrumbList(RichResultsBuilder::breadcrumbItemsForPage($post));
            if ($crumbs !== []) {
                JsonLd::print($crumbs);
            }
            JsonLd::print(RichResultsBuilder::webPage($post));

            return;
        }
    }

    /**
     * Blog / posts index: ItemList of recent articles (helps discovery; not a duplicate of BlogPosting).
     */
    public function outputBlogIndexItemList(): void
    {
        if (! is_home() || is_paged()) {
            return;
        }

        $blog_url = get_post_type_archive_link('post');
        if (! $blog_url) {
            $page_id = (int) get_option('page_for_posts');
            $blog_url = $page_id > 0 ? get_permalink($page_id) : home_url('/');
        }

        $q = new \WP_Query([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ]);

        if (! $q->have_posts()) {
            return;
        }

        $elements = [];
        $pos = 1;
        foreach ($q->posts as $article) {
            if (! $article instanceof WP_Post) {
                continue;
            }
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'url' => get_permalink($article),
                'name' => get_the_title($article),
            ];
            ++$pos;
        }
        wp_reset_postdata();

        if ($elements === []) {
            return;
        }

        JsonLd::print([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            '@id' => trailingslashit($blog_url) . '#collection',
            'url' => $blog_url,
            'name' => get_bloginfo('name') . ' — ' . __('Articles', 'sage'),
            'isPartOf' => ['@id' => RichResultsBuilder::websiteId()],
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($elements),
                'itemListElement' => $elements,
            ],
        ]);
    }
}
