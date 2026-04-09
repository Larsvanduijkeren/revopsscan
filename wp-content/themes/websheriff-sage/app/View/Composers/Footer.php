<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Footer extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'sections.footer',
    ];
}
