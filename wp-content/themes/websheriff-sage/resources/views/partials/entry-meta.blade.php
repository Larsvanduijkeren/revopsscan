<time class="dt-published" datetime="{{ get_post_time('c', true) }}">
    {{ get_the_date() }}
</time>

<p>
    <span>{{ __('By', 'sage') }}</span>
    <span class="p-author h-card">{{ get_the_author() }}</span>
</p>
