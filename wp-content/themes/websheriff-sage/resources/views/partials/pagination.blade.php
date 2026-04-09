@php
$query = $query ?? null;
if (!$query || !($query instanceof \WP_Query) || $query->max_num_pages <= 1) {
    return;
}
$current = (int) get_query_var('paged') ?: (int) get_query_var('page') ?: 1;
$current = max(1, $current);
$pagination_base = $pagination_base ?? null;
$add_args = array_filter([
    'archive_cat' => get_query_var('archive_cat') ?: null,
], static fn ($v) => $v !== null && $v !== '');
$paginate_args = [
    'total'     => $query->max_num_pages,
    'current'   => $current,
    'prev_text' => '&laquo; ' . __('Vorige', 'sage'),
    'next_text' => __('Volgende', 'sage') . ' &raquo;',
    'type'      => 'list',
];
if ($pagination_base !== null && $pagination_base !== '') {
    $paginate_args['base'] = \App\paginate_base_for_url($pagination_base);
    $paginate_args['format'] = '';
}
if ($add_args !== []) {
    $paginate_args['add_args'] = $add_args;
}
@endphp

<nav class="pagination" aria-label="{{ __('Pagination', 'sage') }}">
    {!! paginate_links($paginate_args) !!}
</nav>
