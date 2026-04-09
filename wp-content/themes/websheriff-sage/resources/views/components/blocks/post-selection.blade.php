@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$intro = $fields['intro'] ?? null;
$selection_posts = $selection_posts ?? [];
$id = $block['anchor'] ?? null;
@endphp
@include('components.post-selection-section', [
    'selection_posts' => $selection_posts,
    'label' => $label,
    'title' => $title,
    'intro' => $intro,
    'section_id' => $id,
    'section_class' => '',
    'is_preview' => !empty($is_preview),
    'archive_base_url' => null,
])
