@php
$title = $fields['title'] ?? null;
$headers = $comparison_headers ?? [];
$rows = $comparison_rows ?? [];
$id = $block['anchor'] ?? null;
$has_table = !empty($headers) && !empty($rows);
$col_count = $has_table ? count($headers) : 0;
@endphp
@if($has_table)
<section
    @if($id) id="{{ $id }}" @endif
    class="comparison-table">
    <div class="container" data-aos="fade-up">
        @if($title)
        <h2 class="comparison-table__title h2">{{ esc_html($title) }}</h2>
        @endif
        <div class="comparison-table__scroll" role="region" aria-label="{{ esc_attr($title ?: __('Comparison table', 'sage')) }}">
            <table class="comparison-table__table">
                <thead>
                    <tr>
                        @foreach($headers as $index => $header_label)
                        <th
                            scope="col"
                            class="{{ $index === 0 ? 'comparison-table__th comparison-table__th--feature' : 'comparison-table__th comparison-table__th--plan' }}">
                            {{ esc_html($header_label) }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="comparison-table__row{{ $loop->last ? ' comparison-table__row--pricing' : '' }}">
                        <th scope="row" class="comparison-table__feature">{{ esc_html($row['feature'] ?? '') }}</th>
                        @php
                        $cells = $row['cells'] ?? [];
                        $data_cols = max(0, $col_count - 1);
                        @endphp
                        @for($i = 0; $i < $data_cols; $i++)
                        @php $cell = $cells[$i] ?? ['type' => 'no']; @endphp
                        <td class="comparison-table__cell">
                            @if(($cell['type'] ?? '') === 'yes')
                            <span class="comparison-table__dot comparison-table__dot--yes" title="{{ esc_attr(__('Included', 'sage')) }}" aria-label="{{ esc_attr(__('Included', 'sage')) }}">
                                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                            </span>
                            @elseif(($cell['type'] ?? '') === 'no')
                            <span class="comparison-table__dot comparison-table__dot--no" title="{{ esc_attr(__('Not included', 'sage')) }}" aria-label="{{ esc_attr(__('Not included', 'sage')) }}">
                                <i class="fa-solid fa-circle" aria-hidden="true"></i>
                            </span>
                            @else
                            <span class="comparison-table__value">{{ esc_html($cell['text'] ?? '') }}</span>
                            @endif
                        </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@elseif(!empty($is_preview))
<section class="comparison-table comparison-table--empty has-waves" aria-label="{{ esc_attr(__('Comparison table', 'sage')) }}">
    <div class="container">
        <p class="comparison-table__empty-msg">{{ esc_html(__('Add table rows in the sidebar (Comparison table field).', 'sage')) }}</p>
    </div>
</section>
@endif
