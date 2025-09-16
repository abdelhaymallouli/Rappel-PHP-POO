<?php
declare(strict_types=1);

// Dataset
$articles = [
  ['id'=>1,'title'=>'Intro Laravel','category'=>'php','views'=>120,'author'=>'Amina','published'=>true,  'tags'=>['php','laravel']],
  ['id'=>2,'title'=>'PHP 8 en pratique','category'=>'php','views'=>300,'author'=>'Yassine','published'=>true,  'tags'=>['php']],
  ['id'=>3,'title'=>'Composer & Autoload','category'=>'outils','views'=>90,'author'=>'Amina','published'=>false, 'tags'=>['composer','php']],
  ['id'=>4,'title'=>'Validation FormRequest','category'=>'laravel','views'=>210,'author'=>'Sara','published'=>true,  'tags'=>['laravel','validation']],
];

// Step 1 — Slugify Utility
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// Step 2 — Keep only published articles
$published = array_values(
  array_filter($articles, fn($a) => $a['published'] ?? false)
);

// Step 3 — Normalize: id, slug, views, author, category
$normalized = array_map(
  fn($a) => [
    'id'       => $a['id'],
    'slug'     => slugify($a['title']),
    'views'    => $a['views'],
    'author'   => $a['author'],
    'category' => $a['category'],
  ],
  $published
);

// Step 4 — Sort by views desc
usort($normalized, fn($x, $y) => $y['views'] <=> $x['views']);

// Step 5 — Overall Summary
$summary = array_reduce(
  $published,
  function(array $acc, array $a): array {
      $acc['count']      = ($acc['count'] ?? 0) + 1;
      $acc['views_sum']  = ($acc['views_sum'] ?? 0) + $a['views'];
      $cat = $a['category'];
      $acc['by_category'][$cat] = ($acc['by_category'][$cat] ?? 0) + 1;
      return $acc;
  },
  ['count'=>0, 'views_sum'=>0, 'by_category'=>[]]
);

// Display
print_r($normalized);
print_r($summary);
