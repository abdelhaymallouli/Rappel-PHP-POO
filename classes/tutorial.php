<?php
declare(strict_types=1);

$raw = [
  ['id'=>1,'title'=>'Intro Laravel','excerpt'=>null,'views'=>120],
  ['id'=>2,'title'=>'PHP 8 en pratique','excerpt'=>'Tour des nouveautés','views'=>300],
  ['id'=>3,'title'=>'Composer & Autoload','excerpt'=>null,'views'=>90],
];


class Article {
    public function __construct(
        public int $id,
        public string $title,
        public ?string $excerpt = null,
        public int $views = 0,
    ) {}

    // Generate a slug (URL-friendly title)
    public function slug(): string {
        $s = strtolower($this->title);
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s);
        return trim($s, '-');
    }

    // Export the object back to an array
    public function toArray(): array {
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'excerpt' => $this->excerpt,
            'views'   => $this->views,
            'slug'    => $this->slug(),
        ];
    }
}



class ArticleFactory {
    public static function fromArray(array $a): Article {
        // Defaults and simple validation
        $id      = (int)($a['id'] ?? 0);
        $title   = trim((string)($a['title'] ?? 'Sans titre'));
        $excerpt = isset($a['excerpt']) ? (string)$a['excerpt'] : null;
        $views   = (int)($a['views'] ?? 0);

        return new Article($id, $title, $excerpt, $views);
    }
}

$articles = array_map(
  fn(array $a) => ArticleFactory::fromArray($a),
  $raw
);


foreach ($articles as $art) {
    $data = $art->toArray();
    echo "- {$data['title']} ({$data['views']} vues) — slug: {$data['slug']}\n";
}
