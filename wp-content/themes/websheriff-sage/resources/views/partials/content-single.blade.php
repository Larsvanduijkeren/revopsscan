{{-- Fallback for singular types without partials.content-single-{post_type} (e.g. page). --}}
<section class="single-generic-content">
    <div class="container">
        @php(the_content())
    </div>
</section>
