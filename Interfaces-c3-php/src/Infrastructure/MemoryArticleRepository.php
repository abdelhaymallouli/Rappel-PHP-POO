<?php
declare(strict_types=1);


namespace App\Infrastructure;


use App\Domain\Article;
use App\Domain\Contracts\ArticleRepositoryInterface;
use DomainException;


final class MemoryArticleRepository implements ArticleRepositoryInterface {
/** @var array<string,Article> indexed by slug */
private array $articles = [];


/** @return list<Article> */
public function all(): array {
return array_values($this->articles);
}


public function save(Article $article): void {
$slug = $article->slug();
if (isset($this->articles[$slug])) {
throw new DomainException("Slug already exists: {$slug}");
}
$this->articles[$slug] = $article;
}
}