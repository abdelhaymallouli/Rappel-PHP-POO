<?php
declare(strict_types=1);

//dataset
$articles = [
    ['id' => 1, 'title' => 'Intro Laravel', 'category' => 'php', 'views' => 120, 'author' => 'Amina', 'published' => true, 'tags' => ['php', 'laravel']],
    ['id' => 2, 'title' => 'PHP 8 en pratique', 'category' => 'php', 'views' => 300, 'author' => 'Yassine', 'published' => true, 'tags' => ['php']],
    ['id' => 3, 'title' => 'Composer & Autoload', 'category' => 'outils', 'views' => 90, 'author' => 'Amina', 'published' => false, 'tags' => ['composer', 'php']],
    ['id' => 4, 'title' => 'Validation FormRequest', 'category' => 'laravel', 'views' => 210, 'author' => 'Sara', 'published' => true, 'tags' => ['laravel', 'validation']],
];

// Step 1 : Slugify function 
function slugify(string $title): string{
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// Step 2 : filter published articles 
$published = array_values(
    array_filter(
        $articles,
        fn(array $articles): bool => $article['published'] ?? false
    )
    );

// step 3 : normalize to json format 
$normalized = array_map(
    fn(array $article): array => [
        'id' => $article['id'],
        'slug' => slugify($article['title']),
        'views' => $article['views'],
        'author' => $article['author'],
        'category' => $article['category'],
    ],
    $published
);
// step 4 : sort by views 
usort(
    $normalized, 
    fn(array $a, array $b): int => $b['views'] <=> $a['views']
);

// step 5 : compute summary 
$summary = array_reduce(
    $published,
    fn(array $acc, array $article): array => [
        'count' => $acc['count'] + 1,
        'views_sum' => $acc['views_sum'] + $article['views'],
        'by_category' => array_merge(
            $acc['by_category'],
            [$article['category'] => ($acc['by_category'][$article['category']] ?? 0) + 1]
        ),
    ],
    ['count' => 0, 'views_sum' => 0, 'by_category' => []]
);

// Step 6: Display results
echo "Normalized Articles:\n";
print_r($normalized);

echo "\nSummary:\n";
print_r($summary);

?>