#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Article;
use App\Domain\Contracts\ArticleRepositoryInterface;
use App\Infrastructure\JsonArticleRepository;

$repoMap = [
  ArticleRepositoryInterface::class => new JsonArticleRepository(__DIR__ . '/../storage/seeds/articles.seed.json'),
];

$repo = $repoMap[ArticleRepositoryInterface::class];

$articles = [
  Article::fromTitle(1, 'Interfaces & traits en PHP', ['php', 'poo']),
  Article::fromTitle(2, 'Organiser avec namespaces & PSR-4', ['php', 'autoload']),
];

foreach ($articles as $a) {
  $repo->save($a);
}

echo "[OK] Seed enregistré dans storage/seeds/articles.seed.json" . PHP_EOL;
