export default function initTheme({$, AOS, Lenis, Swiper, Navigation, Pagination, Scrollbar, EffectFade, Autoplay, Thumbs, lottie}) {
    if (window.__casenineThemeInitialized) return;
    window.__casenineThemeInitialized = true;

    $(function () {
        smoothScroll($);
        menu($);
        accordion($);
        faq($, AOS);
        headerController($);
        stickyFeatures($);
        postIndex($);

        postSlider(Swiper, Scrollbar);
        gallerySlider(Swiper, Scrollbar);
        singleGallerySlider(Swiper, Thumbs);
        textMediaSlider(Swiper, Scrollbar, EffectFade);
        heroGallerySlider($, Swiper, EffectFade, Autoplay);
        heroSplitVisualSlider(Swiper, Scrollbar, EffectFade);
        partnersSlider(Swiper, Scrollbar, Autoplay);
        reviewSelectionSlider(Swiper, Navigation, Pagination);
        postSelectionSlider(Swiper, Scrollbar);
        contentCardsSlider(Swiper, Scrollbar);
        featureLottieTabs($, lottie);

        initAosAndLenis($, AOS, Lenis);
    });
}

function postIndex($) {
    const $indexItems = $(".post-content .content h2");
    const $indexContainer = $(".post-content .index");
    if (!$indexItems.length || !$indexContainer.length) return;

    $indexItems.each(function (index) {
        const title = $(this).text();
        const slug = "item-" + index;
        $(this).attr("id", slug);
        $indexContainer.append("<a data-ref=\"" + slug + "\" href=\"#" + slug + "\">" + title + "</a>");
    });

    const $indexLinks = $indexContainer.find("a");
    const activeOffset = 150;

    const updateActiveIndex = () => {
        const scrollTop = $(window).scrollTop();
        const centerline = scrollTop + activeOffset;

        let $activeHeading = null;
        $indexItems.each(function () {
            const top = $(this).offset().top;
            if (top <= centerline) {
                $activeHeading = $(this);
            }
        });

        const activeSlug = $activeHeading ? $activeHeading.attr("id") : $indexItems.first().attr("id");
        $indexLinks.removeClass("is-active");
        $indexLinks.filter("[data-ref=\"" + activeSlug + "\"]").addClass("is-active");
    };

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            updateActiveIndex();
            ticking = false;
        });
    };

    $(window).on("scroll", onScroll);
    updateActiveIndex();
}

function initSwiperSliders({
                               Swiper,
                               modules = [],
                               selector,
                               defaults = {},
                               getOptions = null,
                               scrollbar = true,
                           }) {
    const $els = $(selector);
    if (!$els.length || !Swiper) return;

    $els.each(function () {
        const el = this;
        if (el.swiper) return;

        const $el = $(el);
        const perElOptions = typeof getOptions === 'function' ? (getOptions(el) || {}) : {};

        const scrollbarEl = scrollbar ? $el.find('.swiper-scrollbar')[0] : null;

        const options = {
            modules: modules.filter(Boolean),
            ...defaults,
            ...perElOptions,
            ...(scrollbarEl
                ? {
                    scrollbar: {
                        el: scrollbarEl,
                        draggable: true,
                        hide: false,
                    },
                }
                : {}),
            on: {
                ...(defaults.on || {}),
                ...(perElOptions.on || {}),
            },
        };

        new Swiper(el, options);
    });
}

const slickLikeDefaults = {
    loop: false,
    autoplay: false,
    slidesPerView: 'auto',
    slidesPerGroup: 1,
    spaceBetween: 20,
    speed: 400,
    watchOverflow: true,
    resistanceRatio: 0,
    grabCursor: true,
    pagination: false,
    navigation: false,
};

function stickyFeatures($) {
    const $container = $('.sticky-features');
    if (!$container.length) return;

    let ticking = false;
    let centerline = 0;

    const update = () => {
        centerline = $(window).scrollTop() + $(window).height() / 2;

        $container.find('.feature').each(function () {
            const $feature = $(this);
            const top = $feature.offset().top;
            const bottom = top + $feature.outerHeight(true);

            if (top < centerline && bottom > centerline) {
                const dataFeature = $feature.attr('data-feature');
                $container.find('.feature').removeClass('active');
                $feature.addClass('active');
                $container.find('.image, .dot').removeClass('active');
                $container.find(`.image[data-image="${dataFeature}"], .dot[data-dot="${dataFeature}"]`).addClass('active');
            }
        });

        ticking = false;
    };

    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    };

    $(window).on('scroll', onScroll);
    update();
}

function postSlider(Swiper, Scrollbar) {
    initSwiperSliders({
        Swiper,
        selector: '.post-slider .swiper',
        modules: [Scrollbar],
        defaults: slickLikeDefaults,
    });
}

function gallerySlider(Swiper, Scrollbar) {
    initSwiperSliders({
        Swiper,
        selector: '.gallery .swiper',
        modules: [Scrollbar],
        defaults: slickLikeDefaults,
    });
}

function singleGallerySlider(Swiper, Thumbs) {
    const $galleries = $('.single-gallery');
    if (!$galleries.length || !Swiper || !Thumbs) return;

    $galleries.each(function () {
        const $block = $(this);
        const mainEl = $block.find('.main')[0];
        const thumbsEl = $block.find('.thumbs')[0];
        if (!mainEl || !thumbsEl || mainEl.swiper) return;

        const thumbsSwiper = new Swiper(thumbsEl, {
            modules: [Thumbs],
            spaceBetween: 8,
            slidesPerView: 2,
            watchSlidesProgress: true,
            breakpoints: {
                576: { slidesPerView: 4 },
            },
        });

        new Swiper(mainEl, {
            modules: [Thumbs],
            spaceBetween: 0,
            thumbs: { swiper: thumbsSwiper },
            speed: 400,
        });
    });
}

function partnersSlider(Swiper, Scrollbar, Autoplay) {
    const defaults = {
        ...slickLikeDefaults,
        loop: true,
        watchOverflow: false,
        speed: 700,
        spaceBetween: 40,
        slidesPerView: 1,
        breakpoints: {
            576: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            992: { slidesPerView: 4 },
            1200: { slidesPerView: 5 },
        },
    };

    if (Autoplay) {
        defaults.autoplay = {
            delay: 3200,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        };
    }

    initSwiperSliders({
        Swiper,
        selector: '.partners .swiper',
        modules: [Scrollbar, Autoplay].filter(Boolean),
        defaults,
    });
}

function reviewSelectionSlider(Swiper, Navigation, Pagination) {
    const selector = '.review-selection .review-selection-swiper';
    const $els = $(selector);
    if (!$els.length || !Swiper || !Navigation || !Pagination) {
        return;
    }

    $els.each(function () {
        const el = this;
        if (el.swiper) {
            return;
        }

        const $el = $(el);
        const paginationEl = $el.find('.swiper-pagination')[0];
        const prevEl = $el.find('.swiper-button-prev')[0];
        const nextEl = $el.find('.swiper-button-next')[0];
        const slideCount = $el.find('.swiper-slide').length;
        const multi = slideCount > 1;

        new Swiper(el, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 450,
            loop: false,
            rewind: multi,
            watchOverflow: true,
            resistanceRatio: 0,
            allowTouchMove: multi,
            navigation:
                prevEl && nextEl
                    ? {
                          prevEl,
                          nextEl,
                      }
                    : undefined,
            pagination: paginationEl
                ? {
                      el: paginationEl,
                      clickable: true,
                  }
                : undefined,
        });
    });
}

function postSelectionSlider(Swiper, Scrollbar) {
    initSwiperSliders({
        Swiper,
        selector: '.post-selection .post-selection-swiper',
        modules: [Scrollbar],
        defaults: {
            ...slickLikeDefaults,
            slidesPerView: 1,
            spaceBetween: 24,
            breakpoints: {
                992: { slidesPerView: 2 },
                1200: { slidesPerView: 3 },
            },
        },
    });
}

function contentCardsSlider(Swiper, Scrollbar) {
    initSwiperSliders({
        Swiper,
        selector: '.content-cards--slider .content-cards-swiper',
        modules: [Scrollbar],
        defaults: {
            ...slickLikeDefaults,
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                992: { slidesPerView: 2 },
                1200: { slidesPerView: 3 },
            },
        },
    });
}

function textMediaSlider(Swiper, Scrollbar, EffectFade) {
    initSwiperSliders({
        Swiper,
        selector: '.text-media .text-media-swiper',
        modules: [Scrollbar, EffectFade],
        defaults: {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            slidesPerView: 1,
            slidesPerGroup: 1,
            spaceBetween: 0,
            loop: true,
            speed: 500,
            watchOverflow: true,
            resistanceRatio: 0,
            pagination: false,
            navigation: false,
        },
    });
}

function heroGallerySlider($, Swiper, EffectFade, Autoplay) {
    const selector = '.hero.hero--layout-overlay .hero-gallery-swiper';
    const $els = $(selector);
    if (!$els.length || !Swiper || !EffectFade || !Autoplay) {
        return;
    }

    $els.each(function () {
        const el = this;
        if (el.swiper) {
            return;
        }
        const slideCount = $(el).find('.swiper-slide').length;
        if (slideCount < 2) {
            return;
        }

        new Swiper(el, {
            modules: [EffectFade, Autoplay],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            speed: 900,
            allowTouchMove: false,
            simulateTouch: false,
            grabCursor: false,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: false,
            navigation: false,
            scrollbar: false,
        });
    });
}

function heroSplitVisualSlider(Swiper, Scrollbar, EffectFade) {
    initSwiperSliders({
        Swiper,
        selector: '.hero--layout-split .hero-split-visual__swiper',
        modules: [Scrollbar, EffectFade],
        defaults: {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            slidesPerView: 1,
            slidesPerGroup: 1,
            spaceBetween: 0,
            loop: true,
            speed: 500,
            watchOverflow: true,
            resistanceRatio: 0,
            pagination: false,
            navigation: false,
        },
    });
}

function featureLottieTabs($, lottie) {
    const $roots = $('[data-feature-lottie-tabs]');
    if (!$roots.length || !lottie) {
        return;
    }

    $roots.each(function () {
        const $root = $(this);
        if ($root.data('feature-lottie-initialized')) {
            return;
        }
        $root.data('feature-lottie-initialized', true);

        const $host = $root.find('[data-lottie-host]');
        const $stage = $root.find('[data-lottie-stage]');
        const $tabs = $root.find('[data-flt-tab]');
        const placeholderText = $root.attr('data-lottie-placeholder') || '';

        if (!$host.length || !$tabs.length) {
            return;
        }

        let animation = null;

        function destroyAnim() {
            if (animation) {
                try {
                    animation.destroy();
                } catch (e) {
                    // ignore
                }
                animation = null;
            }
            $host.empty();
        }

        function loadStageMedia($btn) {
            destroyAnim();
            $host.empty();
            const lottieUrl = ($btn.attr('data-lottie-url') || '').trim();
            if (lottieUrl) {
                animation = lottie.loadAnimation({
                    container: $host[0],
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: lottieUrl,
                });
                return;
            }
            const imageUrl = ($btn.attr('data-image-url') || '').trim();
            if (imageUrl) {
                const imageAlt = $btn.attr('data-image-alt') || '';
                $host.append(
                    $('<img/>', {
                        class: 'feature-lottie-tabs__stage-image',
                        src: imageUrl,
                        alt: imageAlt,
                        loading: 'lazy',
                        decoding: 'async',
                    }),
                );
                return;
            }
            $host.append(
                $('<p/>', {
                    class: 'feature-lottie-tabs__placeholder',
                    text: placeholderText,
                }),
            );
        }

        function activateTab($btn) {
            const tint = $btn.attr('data-stage-tint') || 'peach';

            $tabs.removeClass('is-active').attr('aria-selected', 'false').attr('tabindex', '-1');
            $btn.addClass('is-active').attr('aria-selected', 'true').attr('tabindex', '0');

            $stage.attr('data-tint', tint);
            const bid = $btn.attr('id');
            if (bid) {
                $stage.attr('aria-labelledby', bid);
            }

            loadStageMedia($btn);
        }

        $root.on('click', '[data-flt-tab]', function () {
            activateTab($(this));
        });

        $root.on('keydown', '[data-flt-tab]', function (e) {
            const $t = $(this);
            const idx = $tabs.index($t);
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                const next = $tabs.eq((idx + 1) % $tabs.length);
                next.focus();
                activateTab(next);
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const prev = $tabs.eq((idx - 1 + $tabs.length) % $tabs.length);
                prev.focus();
                activateTab(prev);
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                activateTab($t);
            } else if (e.key === 'Home') {
                e.preventDefault();
                const first = $tabs.first();
                first.focus();
                activateTab(first);
            } else if (e.key === 'End') {
                e.preventDefault();
                const last = $tabs.last();
                last.focus();
                activateTab(last);
            }
        });

        const $first = $tabs.filter('.is-active').first().length ? $tabs.filter('.is-active').first() : $tabs.first();
        activateTab($first);
    });
}

function initAosAndLenis($, AOS, Lenis) {
    const isDesktop = $(window).width() > 991;

    if (isDesktop && Lenis) {
        const lenis = new Lenis();

        const raf = (time) => {
            lenis.raf(time);
            requestAnimationFrame(raf);
        };

        requestAnimationFrame(raf);
    }

    if (AOS) {
        AOS.init({
            offset: 50, duration: isDesktop ? 1000 : 600,
        });
    }
}

function initAccordion($, $scope) {
    const $accordions = $scope ? $scope.find('.accordion') : $('.accordion');
    const duration = 350;

    $accordions.each(function () {
        const $acc = $(this);
        if ($acc.data('accordion-initialized')) return;
        $acc.data('accordion-initialized', true);

        const $questions = $acc.find('.question');

        $questions.removeClass('open').find('.answer').hide();

        $questions.each(function () {
            const $q = $(this);
            const $header = $q.find('h4');
            const $answer = $q.find('.answer');
            if (!$header.length || !$answer.length) return;

            $header.attr({ role: 'button', tabindex: '0', 'aria-expanded': 'false' });

            const toggle = () => {
                const isOpen = $q.hasClass('open');

                $questions.not($q).removeClass('open').find('.answer').slideUp(duration);
                $questions.not($q).find('h4').attr('aria-expanded', 'false');

                if (isOpen) {
                    $answer.slideUp(duration, function () {
                        $q.removeClass('open');
                        $header.attr('aria-expanded', 'false');
                    });
                } else {
                    $q.addClass('open');
                    $header.attr('aria-expanded', 'true');
                    $answer.slideDown(duration);
                }
            };

            $header.on('click', toggle);
            $header.on('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle();
                }
            });
        });
    });
}

function faq($, AOS) {
    const $faqs = $('.faq');
    if (!$faqs.length) return;

    const refreshAos = () => {
        if (AOS && typeof AOS.refresh === 'function') {
            requestAnimationFrame(() => AOS.refresh());
        }
    };

    $faqs.each(function () {
        const $root = $(this);
        const $topics = $root.find('.faq-topic');
        const $panels = $root.find('.faq-panel');

        initAccordion($, $root);

        $root.find('.faq-topic-all').addClass('is-active');

        const reset = () => {
            $topics.removeClass('is-active');
            $panels.removeClass('hide');
        };

        $root.on('click', '.faq-topic', function () {
            const termId = $(this).data('term-id');
            const isAll = termId === '' || typeof termId === 'undefined';

            if (isAll) {
                reset();
                $(this).addClass('is-active');
                refreshAos();
                return;
            }

            const wasActive = $(this).hasClass('is-active');
            if (wasActive) {
                reset();
                refreshAos();
                return;
            }

            $topics.removeClass('is-active');
            $(this).addClass('is-active');

            $panels.addClass('hide');
            $panels.filter(`[data-term-id="${termId}"]`).removeClass('hide');

            refreshAos();
        });
    });
}

function headerController($) {
    const scrollWrapper = $(window);
    const body = $('body');

    const setScrolled = () => body.toggleClass('scrolled', scrollWrapper.scrollTop() > 10);

    setScrolled();
    scrollWrapper.on('scroll', setScrolled);
}

function accordion($) {
    initAccordion($);
}

function smoothScroll($) {
    $(document).on('click', 'a[href^="#"]', function (event) {
        const href = $(this).attr('href');
        if (!href || href === '#') return;

        const target = $(href);
        if (!target.length) return;

        event.preventDefault();
        $('html, body').animate({scrollTop: target.offset().top - 120}, 500);
    });
}

function menu($) {
    $(document).on('click', '.mobile-nav .menu-item-has-children > a', function (e) {
        e.preventDefault();
        $(this).toggleClass('open');
    });

    $(document).on('click', '.hamburger', function () {
        $('body').toggleClass('mobile-nav-open');
        setTimeout(() => $('body, html').toggleClass('no-scroll'), 500);
    });
}
