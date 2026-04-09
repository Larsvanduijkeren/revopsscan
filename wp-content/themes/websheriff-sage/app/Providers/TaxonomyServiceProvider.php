<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class TaxonomyServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerTaxonomies'], 0);
    }

    public function registerTaxonomies(): void
    {
        $this->registerQuestionCategoryTaxonomy();
        $this->registerReviewCategoryTaxonomy();
    }

    protected function registerQuestionCategoryTaxonomy(): void
    {
        register_taxonomy('question_category', ['question'], [
            'labels'            => [
                'name'          => __('Categories', 'sage'),
                'singular_name' => __('Category', 'sage'),
            ],
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'public'            => false,
            'rewrite'           => false,
            'query_var'         => false,
            'show_in_rest'      => true,
        ]);
    }

    protected function registerReviewCategoryTaxonomy(): void
    {
        register_taxonomy('review_category', ['review'], [
            'labels'            => [
                'name'          => __('Review categories', 'sage'),
                'singular_name' => __('Review category', 'sage'),
            ],
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'public'            => false,
            'rewrite'           => false,
            'query_var'         => false,
            'show_in_rest'      => false,
        ]);
    }
}
