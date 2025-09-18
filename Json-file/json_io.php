<?php
declare(strict_types=1);

/** Helpers génériques JSON */
function loadJson(string $path): array {
    $raw = @file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException("Fichier introuvable ou illisible: $path");
    }
    try {
        /** @var array $data */
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return $data;
    } catch (JsonException $e) {
        throw new RuntimeException("JSON invalide dans $path", previous: $e);
    }
}

/**
 * Atomic save JSON with validation
 */
function saveJson(string $path, array $data): void {
    // Validation : title et slug obligatoires
    foreach ($data as $item) {
        if (empty($item['title']) || empty($item['slug'])) {
            throw new DomainException("Article invalide: title/slug manquant");
        }
    }

    $dir = dirname($path);
    if (!is_dir($dir)) { mkdir($dir, 0777, true); }

    $json = json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    if ($json === false) {
        throw new RuntimeException("Échec d'encodage JSON (retour false).");
    }

    // Atomic write : d'abord .tmp, puis rename
    $tmp = $path . '.tmp';
    $ok = @file_put_contents($tmp, $json . PHP_EOL, LOCK_EX);
    if ($ok === false) {
        throw new RuntimeException("Écriture impossible: $tmp");
    }

    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException("Impossible de renommer $tmp → $path");
    }
}

/** Génère un slug simple */
function slugify(string $value): string {
    $s = strtolower($value);
    $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
    return trim($s, '-');
}

/** Génère N articles factices */
function generateArticles(int $count): array {
    $out = [];
    for ($i = 1; $i <= $count; $i++) {
        $title = "Article $i";
        $out[] = [
            'id'      => $i,
            'title'   => $title,
            'slug'    => slugify($title),
            'excerpt' => "Résumé de l’article $i",
            'tags'    => ['php','demo'],
        ];
    }
    return $out;
}

/** Fusionne 2 tableaux d’articles en évitant les doublons de slug */
function mergeArticles(array $base, array $extra): array {
    $seen = [];
    $merged = [];

    foreach (array_merge($base, $extra) as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug === '' || isset($seen[$slug])) {
            continue; // ignore doublons ou slug vide
        }
        $seen[$slug] = true;
        $merged[] = $article;
    }
    return $merged;
}

/** ---------------- CLI ---------------- */
try {
    global $argv;

    if (($argv[1] ?? null) === null || ($argv[2] ?? null) === null) {
        throw new InvalidArgumentException("Usage: php script.php <path> <nb_items>");
    }

    $seedPath = $argv[1];
    $count    = max(1, (int)$argv[2]);

    // 1) Générer des articles
    $articles = generateArticles($count);

    // 2) Importer un extra si dispo
    $extraPath = __DIR__ . '/articles.extra.json';
    if (file_exists($extraPath)) {
        $extra = loadJson($extraPath);
        $articles = mergeArticles($articles, $extra);
        echo "[OK] Fusion avec articles.extra.json (" . count($articles) . " articles)" . PHP_EOL;
    }

    // 3) Sauvegarde atomique
    saveJson($seedPath, $articles);
    echo "[OK] Seed écrit: $seedPath" . PHP_EOL;

    // 4) Vérif lecture
    $loaded = loadJson($seedPath);
    echo "[OK] Relu: " . count($loaded) . " article(s)." . PHP_EOL;
    echo "Premier titre: " . ($loaded[0]['title'] ?? 'N/A') . PHP_EOL;

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "[ERR] " . $e->getMessage() . PHP_EOL);
    if ($e->getPrevious()) {
        fwrite(STDERR, "Cause: " . get_class($e->getPrevious()) . " — " . $e->getPrevious()->getMessage() . PHP_EOL);
    }
    exit(1);
}


// php json_io.php articles.seed.json 5   