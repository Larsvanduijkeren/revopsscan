import $ from 'jquery';

window.$ = window.jQuery = $;

// Pull in static assets for dev/build (fonts/images)
import.meta.glob(['../images/**', '../fonts/**'], { eager: true });

// Core features load immediately (lightweight)
import initTheme from './main';

(async () => {
    const $ = window.jQuery;
    const hasSliders = $('.swiper').length || $('.single-gallery').length;
    const hasLity = $('[data-lity]').length > 0;
    const hasLottieTabs = $('[data-feature-lottie-tabs]').length > 0;
    const hasAos = $('[data-aos]').length;
    const isDesktop = $(window).width() > 991;
    const hasLenis = isDesktop; // Lenis only on desktop

    let Swiper = null;
    let Navigation = null;
    let Pagination = null;
    let Scrollbar = null;
    let EffectFade = null;
    let Autoplay = null;
    let Thumbs = null;
    let AOS = null;
    let Lenis = null;
    let LottieLib = null;

    if (hasSliders) {
        const swiperModule = await import('swiper');
        const swiperMods = await import('swiper/modules');
        Swiper = swiperModule.default;
        Navigation = swiperMods.Navigation;
        Pagination = swiperMods.Pagination;
        Scrollbar = swiperMods.Scrollbar;
        EffectFade = swiperMods.EffectFade;
        Autoplay = swiperMods.Autoplay;
        Thumbs = swiperMods.Thumbs;
        await import('swiper/css');
        await import('swiper/css/effect-fade');
        await import('swiper/css/navigation');
        await import('swiper/css/pagination');
        await import('swiper/css/scrollbar');
    }

    if (hasAos) {
        AOS = (await import('aos')).default;
        await import('aos/dist/aos.css');
    }

    if (hasLenis) {
        Lenis = (await import('lenis')).default;
    }

    if (hasLity) {
        await import('lity');
        await import('lity/dist/lity.min.css');
    }

    if (hasLottieTabs) {
        const lottieMod = await import('lottie-web');
        LottieLib = lottieMod.default;
    }

    initTheme({
        $,
        AOS,
        Lenis,
        Swiper,
        Navigation,
        Pagination,
        Scrollbar,
        EffectFade,
        Autoplay,
        Thumbs,
        lottie: LottieLib,
    });
})();
