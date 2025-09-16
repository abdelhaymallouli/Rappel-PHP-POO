<?php
declare(strict_types=1);

// Jeu de données
$articles = [
  ['id'=>1,'title'=>'Intro Laravel','category'=>'php','views'=>120,'author'=>'Amina','published'=>true,  'tags'=>['php','laravel']],
  ['id'=>2,'title'=>'PHP 8 en pratique','category'=>'php','views'=>300,'author'=>'Yassine','published'=>true,  'tags'=>['php']],
  ['id'=>3,'title'=>'Composer & Autoload','category'=>'outils','views'=>90,'author'=>'Amina','published'=>false, 'tags'=>['composer','php']],
  ['id'=>4,'title'=>'Validation FormRequest','category'=>'laravel','views'=>210,'author'=>'Sara','published'=>true,  'tags'=>['laravel','validation']],
];

// Étape 1 — Utilitaire slugify
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// Étape 2 — Filtrer les articles publiés
$published = array_values(
  array_filter($articles, fn(array $a) => $a['published'] ?? false)
);

// Étape 3 — Mapper vers un format léger
$light = array_map(
  fn(array $a) => [
    'id'    => $a['id'],
    'title' => $a['title'],
    'slug'  => slugify($a['title']),
    'views' => $a['views'],
  ],
  $published
);

// Étape 4 — Top 3 par vues
$top = $light;
usort($top, fn($a, $b) => $b['views'] <=> $a['views']);
$top3 = array_slice($top, 0, 3);

// Étape 5 — Agréger : nombre d’articles par auteur
$byAuthor = array_reduce(
  $published,
  function(array $acc, array $a): array {
      $author = $a['author'];
      $acc[$author] = ($acc[$author] ?? 0) + 1;
      return $acc;
  },
  []
);

// Étape 6 — Fréquence des tags
$allTags = array_merge(...array_map(fn($a) => $a['tags'], $published));

$tagFreq = array_reduce(
  $allTags,
  function(array $acc, string $tag): array {
      $acc[$tag] = ($acc[$tag] ?? 0) + 1;
      return $acc;
  },
  []
);

// Étape 7 — Afficher un mini-rapport
echo "Top 3 (views):\n";
foreach ($top3 as $a) {
  echo "- {$a['title']} ({$a['views']} vues) — {$a['slug']}\n";
}

echo "\nPar auteur:\n";
foreach ($byAuthor as $author => $count) {
  echo "- $author: $count article(s)\n";
}

echo "\nTags:\n";
foreach ($tagFreq as $tag => $count) {
  echo "- $tag: $count\n";
}
