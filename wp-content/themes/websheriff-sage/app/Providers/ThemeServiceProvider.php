<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        add_action('template_redirect', static function (): void {
            if (is_author()) {
                wp_safe_redirect(home_url('/'), 301);
                exit;
            }
        });

        add_filter('query_vars', static function (array $vars): array {
            if (! in_array('archive_cat', $vars, true)) {
                $vars[] = 'archive_cat';
            }

            return $vars;
        });
    }
}
