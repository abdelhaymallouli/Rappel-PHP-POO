<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Article;
use App\Domain\Contracts\ArticleRepositoryInterface;
use RuntimeException;

final class JsonArticleRepository implements ArticleRepositoryInterface {
  public function __construct(private string $path) {}

  /** @return list<Article> */
  public function all(): array {
    if (!is_file($this->path)) return [];
    $raw = file_get_contents($this->path);
    if ($raw === false) throw new RuntimeException("Lecture impossible: {$this->path}");
    /** @var array<int,array{id:int,title:string,slug:string,tags:array}> $rows */
    $rows = json_decode($raw, true) ?: [];
    return array_map(
      fn(array $r) => new Article($r['id'], $r['title'], $r['slug'], $r['tags'] ?? []),
      $rows
    );
  }

  public function save(Article $article): void {
    $rows = array_map(fn(Article $a) => $a->toArray(), $this->all());
    $rows[] = $article->toArray();
    $dir = dirname($this->path);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    if (false === file_put_contents($this->path, json_encode($rows, JSON_PRETTY_PRINT))) {
      throw new RuntimeException("Écriture impossible: {$this->path}");
    }
  }
}
