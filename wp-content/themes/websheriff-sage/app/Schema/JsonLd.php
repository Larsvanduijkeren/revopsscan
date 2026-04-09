<?php

declare(strict_types=1);

namespace App\Schema;

/**
 * Schema.org JSON-LD helpers (rich results).
 */
final class JsonLd
{
    public static function encode(array $data): string
    {
        return wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function print(array $schema): void
    {
        echo '<script type="application/ld+json">' . self::encode($schema) . '</script>' . "\n";
    }

    /**
     * @param  array<int, array{name: string, url: string}>  $items
     */
    public static function breadcrumbList(array $items): array
    {
        $elements = [];
        $pos = 1;
        foreach ($items as $item) {
            if (($item['name'] ?? '') === '' || ($item['url'] ?? '') === '') {
                continue;
            }
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
            ++$pos;
        }

        if ($elements === []) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values($elements),
        ];
    }
}
