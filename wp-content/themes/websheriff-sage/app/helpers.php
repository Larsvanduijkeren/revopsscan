<?php
    
    namespace App;
    
    /**
     * Estimate reading time (Dutch label) for a post.
     */
    function reading_time_label(\WP_Post $post, int $wpm = 220): string
    {
        $content = $post->post_content ?? '';
        
        // Expand blocks/shortcodes and remove markup
        $content = do_shortcode($content);
        $content = wp_strip_all_tags($content);
        
        // Count words (simple + good enough for NL)
        $words = str_word_count($content);
        
        $minutes = (int) max(1, ceil($words / max(1, $wpm)));
        
        return "{$minutes} min leestijd";
    }

    /**
     * Strip unsafe characters from Font Awesome class strings (editor-controlled).
     */
    function sanitize_fa_icon_classes(string $raw): string
    {
        return trim((string) preg_replace('/[^a-zA-Z0-9_\s-]/', '', $raw));
    }

    /**
     * Font Awesome classes for the header support link (ACF: short name e.g. headset, or full fa-* classes).
     */
    function header_support_icon_class(?string $raw): string
    {
        $raw = $raw === null ? '' : trim($raw);
        if ($raw === '') {
            return 'fa-solid fa-headset';
        }
        $sanitized = sanitize_fa_icon_classes($raw);
        if ($sanitized === '') {
            return 'fa-solid fa-headset';
        }
        if (str_contains($sanitized, 'fa-')) {
            return $sanitized;
        }

        return 'fa-solid fa-' . ltrim($sanitized, '-');
    }

    /**
     * Base URL pattern for paginate_links() on a static page (replaces page number with %#%).
     */
    function paginate_base_for_url(string $url): string
    {
        $big = 999999999;
        $url = untrailingslashit($url);
        if (get_option('permalink_structure')) {
            return esc_url_raw(str_replace((string) $big, '%#%', trailingslashit($url) . "page/{$big}/"));
        }

        return esc_url_raw(add_query_arg('paged', '%#%', remove_query_arg(['paged', 'page'], $url)));
    }
