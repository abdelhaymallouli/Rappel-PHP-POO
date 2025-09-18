<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Seed\ArticleFactory;

$options = getopt('', ['count::', 'out::', 'topic::']);
$count = isset($options['count']) ? (int)$options['count'] : 10;
$out   = $options['out'] ?? 'storage/articles.seed.json';
$topic = $options['topic'] ?? null; // Get the topic parameter

// Générer
$factory  = new ArticleFactory();
$articles = $factory->make($count, $topic); // Pass topic to make method

// Créer le dossier si nécessaire
$dir = dirname($out);
if (!is_dir($dir)) {
    if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
        fwrite(STDERR, "Erreur: impossible de créer le dossier $dir\n");
        exit(1);
    }
}

// Écrire JSON joli
$json = json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "Erreur JSON: " . json_last_error_msg() . "\n");
    exit(1);
}
file_put_contents($out, $json);

echo "✅ Seed généré : $out (" . count($articles) . " articles)\n";