<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class AcfBlockServiceProvider extends SageServiceProvider
{
    /**
     * Slug => Title
     */
    protected array $blocks = [
        'hero'          => 'Hero',
        'text'          => 'Text',
        'text-media'    => 'Text media',
        'content-cards' => 'Content Cards',
        'pricing-plans' => 'Pricing plans',
        'post-archive'      => 'Post archive',
        'post-selection'    => 'Post selection',
        'faq'           => 'FAQ',
        'partners'      => 'Partners',
        'usps'          => 'USPs',
        'review-selection' => 'Review Selection',
        'comparison-table' => 'Comparison table',
        'feature-lottie-tabs' => 'Feature Lottie tabs',
        'contact'       => 'Contact',
    ];

    public function boot(): void
    {
        parent::boot();

        add_filter('block_categories_all', [$this, 'addBlockCategory'], 10, 2);
        add_action('acf/init', [$this, 'registerBlocks']);
        add_filter('allowed_block_types_all', [$this, 'allowedBlocks'], 10, 2);
    }

    public function addBlockCategory(array $categories, $post): array
    {
        foreach ($categories as $cat) {
            if (($cat['slug'] ?? null) === 'casenine') {
                return $categories;
            }
        }

        $categories[] = [
            'slug'  => 'casenine',
            'title' => __('Casenine blocks', 'sage'),
        ];

        return $categories;
    }

    public function registerBlocks(): void
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        foreach ($this->blocks as $name => $title) {
            acf_register_block_type([
                'name'               => $name,
                'title'              => __($title, 'sage'),
                'category'           => 'casenine',
                'icon'               => 'editor-code',
                'mode'               => 'auto',
                'acf_block_version' => 3,
                'api_version'       => 3,
                'supports'           => [
                    'anchor' => true,
                ],
                'render_callback'    => [$this, 'renderBlock'],
            ]);
        }
    }

    /**
     * Render callback for all ACF blocks.
     */
    public function renderBlock(array $block, string $content = '', bool $isPreview = false, int $postId = 0): void
    {
        $slug = str_replace('acf/', '', $block['name'] ?? '');
        $view = "components.blocks.{$slug}";

        $fields = $this->getBlockFields($block);

        $data = [
            'block'      => $block,
            'fields'     => $fields,
            'is_preview' => $isPreview,
            'post_id'    => $postId,
            'slug'       => $slug,
            'content'    => $content,
        ];

        $data = array_merge($data, $this->prepareBlockData($slug, $fields, $block, $postId));

        if (function_exists('view') && view()->exists($view)) {
            echo view($view, $data)->render();

            return;
        }

        echo "<!-- Missing block view: {$view} -->";
    }

    /**
     * Prepare block-specific data (queries, preloaded ACF). Avoids N+1 and keeps Blade presentational.
     */
    protected function prepareBlockData(string $slug, array $fields, array $block, int $postId): array
    {
        switch ($slug) {
            case 'faq':
                return $this->prepareFaqData($fields);
            case 'post-archive':
                return $this->preparePostArchiveData($fields, $postId);
            case 'post-selection':
                return $this->preparePostSelectionData($fields, $postId);
            case 'review-selection':
                return $this->prepareReviewSelectionData($fields);
            case 'comparison-table':
                return $this->prepareComparisonTableData($fields);
            case 'feature-lottie-tabs':
                return $this->prepareFeatureLottieTabsData($fields);
            default:
                return [];
        }
    }

    protected function prepareFaqData(array $fields): array
    {
        $termIds = $this->normalizeTermIds($fields['category_selection'] ?? null);
        $args = [
            'post_type'      => 'question',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ];
        if (!empty($termIds)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'question_category',
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                ],
            ];
        }
        $posts = get_posts($args);
        if (!empty($posts) && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return ['questions' => $posts];
    }

    /**
     * @return array{
     *     query: \WP_Query,
     *     archive_base_url: string,
     *     archive_categories: array<int, \WP_Term>,
     *     current_archive_cat: string,
     *     show_category_filter: bool
     * }
     */
    protected function preparePostArchiveData(array $fields, int $postId): array
    {
        $pageId = $postId > 0 ? $postId : (int) get_queried_object_id();
        $archive_base_url = $pageId > 0 ? (string) get_permalink($pageId) : home_url('/');

        $ppp = (int) ($fields['posts_per_page'] ?? 9);
        $ppp = max(1, min(48, $ppp));

        $show_filter = true;
        if (array_key_exists('show_category_filter', $fields)) {
            $sf = $fields['show_category_filter'];
            $show_filter = filter_var($sf, FILTER_VALIDATE_BOOLEAN) || $sf === 1 || $sf === '1';
        }

        $cat_slug = sanitize_title((string) get_query_var('archive_cat'));
        $paged = max(1, (int) get_query_var('paged') ?: (int) get_query_var('page') ?: 1);

        $args = [
            'post_type'           => 'post',
            'posts_per_page'      => $ppp,
            'paged'               => $paged,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ];

        if ($cat_slug !== '') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => [$cat_slug],
                ],
            ];
        }

        $query = new \WP_Query($args);
        if (! empty($query->posts) && function_exists('update_post_caches')) {
            update_post_caches($query->posts);
        }

        $archive_categories = [];
        if ($show_filter) {
            $terms = get_categories([
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]);
            $archive_categories = is_array($terms) ? $terms : [];
        }

        return [
            'query'                => $query,
            'archive_base_url'     => $archive_base_url,
            'archive_categories'   => $archive_categories,
            'current_archive_cat'  => $cat_slug,
            'show_category_filter' => $show_filter,
        ];
    }

    /**
     * @return array{selection_posts: array<int, \WP_Post>}
     */
    protected function preparePostSelectionData(array $fields, int $postId): array
    {
        $source = $fields['post_selection_source'] ?? 'recent';
        if (! in_array($source, ['category', 'manual', 'recent'], true)) {
            $source = 'recent';
        }

        if ($source === 'manual') {
            $picked = $fields['selected_posts'] ?? null;
            $posts = [];
            if (is_array($picked)) {
                foreach ($picked as $p) {
                    if ($p instanceof \WP_Post) {
                        $posts[] = $p;
                    } elseif (is_numeric($p)) {
                        $obj = get_post((int) $p);
                        if ($obj instanceof \WP_Post && $obj->post_type === 'post') {
                            $posts[] = $obj;
                        }
                    }
                }
            }
            if (! empty($posts) && function_exists('update_post_caches')) {
                update_post_caches($posts);
            }

            return ['selection_posts' => $posts];
        }

        $ppp = (int) ($fields['posts_per_page'] ?? 8);
        $ppp = max(2, min(24, $ppp));

        $args = [
            'post_type'           => 'post',
            'posts_per_page'      => $ppp,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ];

        if ($source === 'category') {
            $termIds = $this->normalizeTermIds($fields['post_categories'] ?? null);
            if (empty($termIds)) {
                return ['selection_posts' => []];
            }
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                ],
            ];
        }

        $posts = get_posts($args);
        if (! empty($posts) && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return ['selection_posts' => $posts];
    }

    /**
     * @return array{fl_tabs_items: array<int, array<string, mixed>>}
     */
    protected function prepareFeatureLottieTabsData(array $fields): array
    {
        $rows = $fields['tabs'] ?? null;
        if (! is_array($rows)) {
            return ['fl_tabs_items' => []];
        }

        $out = [];
        foreach ($rows as $i => $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['item_title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $lf = $row['lottie_file'] ?? '';
            $url = '';
            if (is_string($lf) && $lf !== '') {
                $url = esc_url_raw($lf);
            } elseif (is_array($lf) && ! empty($lf['url'])) {
                $url = esc_url_raw((string) $lf['url']);
            }

            $stage_image_url = '';
            $stage_image_alt = '';
            $tabImg = $row['tab_image'] ?? null;
            if (is_array($tabImg) && ! empty($tabImg['url'])) {
                $stage_image_url = esc_url_raw((string) $tabImg['url']);
                $stage_image_alt = sanitize_text_field((string) ($tabImg['alt'] ?? ''));
            } elseif (is_string($tabImg) && $tabImg !== '') {
                $stage_image_url = esc_url_raw($tabImg);
            } elseif (is_numeric($tabImg)) {
                $aid = (int) $tabImg;
                if ($aid > 0) {
                    $iu = wp_get_attachment_image_url($aid, 'large');
                    $stage_image_url = $iu ? esc_url_raw($iu) : '';
                    $stage_image_alt = sanitize_text_field((string) get_post_meta($aid, '_wp_attachment_image_alt', true));
                }
            }

            $tone = $row['icon_tone'] ?? 'orange';
            if (! in_array($tone, ['orange', 'purple', 'teal', 'pink'], true)) {
                $tone = 'orange';
            }

            $stage = $row['stage_tint'] ?? 'peach';
            if (! in_array($stage, ['peach', 'lavender', 'mint', 'rose', 'sand'], true)) {
                $stage = 'peach';
            }

            $out[] = [
                'tab_id'            => 'flt-tab-' . $i,
                'icon_class'        => \App\sanitize_fa_icon_classes((string) ($row['icon_class'] ?? '')),
                'icon_tone'         => $tone,
                'item_title'        => $title,
                'item_description'  => (string) ($row['item_description'] ?? ''),
                'lottie_url'        => $url,
                'stage_image_url'   => $stage_image_url,
                'stage_image_alt'   => $stage_image_alt,
                'stage_tint'        => $stage,
            ];
        }

        return ['fl_tabs_items' => array_values($out)];
    }

    protected function prepareReviewSelectionData(array $fields): array
    {
        $termIds = $this->normalizeTermIds($fields['category_selection'] ?? null);
        $args = [
            'post_type'      => 'review',
            'posts_per_page' => 12,
            'orderby'        => 'menu_order date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ];
        if (!empty($termIds)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'review_category',
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                ],
            ];
        }
        $posts = get_posts($args);
        if (!empty($posts) && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        $reviews = [];
        foreach ($posts as $post) {
            $rating = (int) (function_exists('get_field') ? get_field('review_rating', $post->ID) : 0);
            if ($rating < 1) {
                $rating = 5;
            }
            if ($rating > 5) {
                $rating = 5;
            }
            $quote = function_exists('get_field') ? (string) (get_field('review_quote', $post->ID) ?? '') : '';
            $roleLine = function_exists('get_field') ? (string) (get_field('review_role', $post->ID) ?? '') : '';
            $initials = function_exists('get_field') ? trim((string) (get_field('review_initials', $post->ID) ?? '')) : '';
            if ($initials === '') {
                $initials = $this->deriveInitialsFromTitle(get_the_title($post));
            }

            $reviews[] = [
                'post'     => $post,
                'rating'   => $rating,
                'quote'    => $quote,
                'role_line'=> $roleLine,
                'initials' => $initials,
            ];
        }

        return ['reviews' => $reviews];
    }

    /**
     * Table field from "Advanced Custom Fields: Table Field" (type table): cells are arrays with a "c" text key.
     *
     * @return array{comparison_headers: array<int, string>, comparison_rows: array<int, array{feature: string, cells: array<int, array{type: string, text?: string}>}>}
     */
    protected function prepareComparisonTableData(array $fields): array
    {
        $table = $fields['comparison_table'] ?? null;
        if (! is_array($table) || empty($table['body']) || ! is_array($table['body'])) {
            return [
                'comparison_headers' => [],
                'comparison_rows'    => [],
            ];
        }

        $body = $table['body'];
        $headers = [];
        if (! empty($table['use_header']) && ! empty($table['header']) && is_array($table['header'])) {
            foreach ($table['header'] as $cell) {
                $headers[] = $this->extractAcfTableCellText($cell);
            }
        }

        $body_rows = $body;
        if ($headers === [] && isset($body[0]) && is_array($body[0])) {
            foreach ($body[0] as $cell) {
                $headers[] = $this->extractAcfTableCellText($cell);
            }
            $body_rows = array_slice($body, 1);
        }

        $rows = [];
        foreach ($body_rows as $row) {
            if (! is_array($row) || $row === []) {
                continue;
            }
            $feature = $this->extractAcfTableCellText($row[0] ?? null);
            $cells = [];
            $colCount = count($row);
            for ($i = 1; $i < $colCount; $i++) {
                $cells[] = $this->normalizeComparisonTableCell(
                    $this->extractAcfTableCellText($row[$i] ?? null)
                );
            }
            $rows[] = [
                'feature' => $feature,
                'cells'   => $cells,
            ];
        }

        return [
            'comparison_headers' => $headers,
            'comparison_rows'    => $rows,
        ];
    }

    protected function extractAcfTableCellText(mixed $cell): string
    {
        if (is_string($cell)) {
            return trim($cell);
        }
        if (is_array($cell) && array_key_exists('c', $cell)) {
            return trim((string) $cell['c']);
        }

        return '';
    }

    /**
     * @return array{type: 'yes'}|array{type: 'no'}|array{type: 'text', text: string}
     */
    protected function normalizeComparisonTableCell(string $raw): array
    {
        $stripped = trim(wp_strip_all_tags($raw));
        $lower = mb_strtolower($stripped, 'UTF-8');
        if (
            $stripped === ''
            || $lower === '-'
            || $lower === 'no'
            || $lower === 'false'
            || $stripped === '—'
            || $stripped === '–'
        ) {
            return ['type' => 'no'];
        }
        if (
            $stripped === '+'
            || $lower === 'yes'
            || $lower === 'true'
            || $stripped === '✓'
            || $stripped === '√'
        ) {
            return ['type' => 'yes'];
        }

        return ['type' => 'text', 'text' => $stripped];
    }

    protected function deriveInitialsFromTitle(string $title): string
    {
        $title = trim($title);
        if ($title === '') {
            return '?';
        }
        $parts = preg_split('/\s+/u', $title) ?: [];
        $parts = array_values(array_filter($parts, static fn (string $p): bool => $p !== ''));
        if ($parts === []) {
            return mb_strtoupper(mb_substr($title, 0, 2));
        }
        $initials = '';
        $max = min(2, count($parts));
        for ($i = 0; $i < $max; $i++) {
            $initials .= mb_strtoupper(mb_substr($parts[$i], 0, 1));
        }

        return $initials !== '' ? $initials : '?';
    }

    /** @return array<int> */
    protected function normalizeTermIds($value): array
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }
        if (is_numeric($value)) {
            return [(int) $value];
        }

        return [];
    }

    /**
     * Best-effort block field retrieval.
     */
    protected function getBlockFields(array $block): array
    {
        $blockId = $block['id'] ?? null;

        if ($blockId && function_exists('get_fields')) {
            $scoped = get_fields($blockId);
            if (is_array($scoped)) {
                return $scoped;
            }
        }

        if (function_exists('get_fields')) {
            $fallback = get_fields();

            return is_array($fallback) ? $fallback : [];
        }

        return [];
    }

    /**
     * @param  bool|string[]  $allowed
     * @return bool|string[]
     */
    public function allowedBlocks($allowed, $context): bool|array
    {
        $post = $context->post ?? null;

        if ($post === null || ! isset($post->post_type)) {
            return $allowed;
        }

        return array_values(array_map(
            fn (string $slug): string => "acf/{$slug}",
            array_keys($this->blocks)
        ));
    }
}
