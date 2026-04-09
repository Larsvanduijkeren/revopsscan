<?php

declare(strict_types=1);

namespace App\Walkers;

use Walker_Nav_Menu;

/**
 * Header navigation walker: optional full-width mega menu + promo row.
 *
 * Mega panel (add CSS class "mega-menu" on the top-level item in Appearance → Menus):
 * - Each direct child with its own submenu = one column (parent link = column heading, children = links).
 * - Optional promo strip: add a child item with class "mega-menu__promo".
 *   · Navigation label = button text
 *   · URL = button href
 *   · Title attribute = headline (optional)
 *   · Description = supporting copy (optional, enable under Screen Options). Allows basic HTML.
 */
class MegaMenuWalker extends Walker_Nav_Menu
{
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void
    {
        if ($depth === 1 && in_array('mega-menu__promo', (array) $item->classes, true)) {
            $classes = empty($item->classes) ? [] : (array) $item->classes;
            $class_string = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
            $indent = $depth ? str_repeat("\t", $depth) : '';

            $output .= $indent . '<li class="' . esc_attr($class_string) . '">';
            $output .= '<div class="mega-menu__promo-inner">';
            $output .= '<span class="mega-menu__promo-icon" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>';
            $output .= '<div class="mega-menu__promo-text">';

            if ($item->attr_title !== '') {
                $output .= '<strong class="mega-menu__promo-title">' . esc_html($item->attr_title) . '</strong>';
            }

            if ($item->description !== '') {
                $output .= '<div class="mega-menu__promo-desc">' . wp_kses_post($item->description) . '</div>';
            }

            $output .= '</div>';

            if (! empty($item->url) && $item->url !== '#') {
                $output .= '<a class="btn mega-menu__promo-btn" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            }

            $output .= '</div>';

            return;
        }

        parent::start_el($output, $item, $depth, $args, $id);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null): void
    {
        if ($depth === 1 && in_array('mega-menu__promo', (array) $item->classes, true)) {
            $output .= "</li>\n";

            return;
        }

        parent::end_el($output, $item, $depth, $args);
    }
}
