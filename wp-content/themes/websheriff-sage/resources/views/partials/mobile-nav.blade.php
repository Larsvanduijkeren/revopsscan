<div class="mobile-nav">
    <div class="content">
        <div class="nav">
            <div class="flex-wrapper">
                @php
                    $mobileHeaderNavArgs = [
                        'theme_location' => 'header-nav',
                        'echo' => false,
                        'container' => false,
                        'menu_class' => 'menu',
                        'depth' => 4,
                        'fallback_cb' => false,
                        'walker' => new \App\Walkers\MegaMenuWalker(),
                    ];
                @endphp
                {!! wp_nav_menu($mobileHeaderNavArgs) !!}

                @include('partials.header-support-link')

                @include('partials.header-buttons')
            </div>
        </div>
    </div>
</div>
