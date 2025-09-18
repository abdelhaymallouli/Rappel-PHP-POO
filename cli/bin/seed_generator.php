#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Usage:
 *   php bin/seed_generator.php --input=PATH|- [--published-only] [--limit=N] [--help]
 *   cat file.csv | php bin/seed_generator.php --input=-
 *
 * Converts a CSV file with columns (title,excerpt,views,published,author) to normalized JSON.
 */

const EXIT_OK          = 0;
const EXIT_USAGE       = 2;
const EXIT_DATA_ERROR  = 3;

function usage(): void {
    $msg = <<<TXT
Seed Generator — Options:
  --input=PATH    Path to CSV file or '-' for STDIN (required)
  --published-only  Include only published articles
  --limit[=N]     Limit the number of articles output (optional)
  --help          Display this help

Examples:
  php bin/seed_generator.php --input=articles.csv --published-only --limit=2
  cat articles.csv | php bin/seed_generator.php --input=-
TXT;
    fwrite(STDOUT, $msg . PHP_EOL);
}

function readCsvFrom(string $input): array {
    $fh = $input === '-' ? STDIN : @fopen($input, 'r');
    if ($fh === false) {
        fwrite(STDERR, "Erreur: impossible d'ouvrir l'entrée: $input\n");
        exit(EXIT_DATA_ERROR);
    }

    $header = fgetcsv($fh);
    if ($header === false || !in_array('title', $header) || !in_array('published', $header)) {
        if ($fh !== STDIN) fclose($fh);
        fwrite(STDERR, "Erreur: en-têtes CSV invalides (title,excerpt,views,published,author requis)\n");
        exit(EXIT_DATA_ERROR);
    }

    $rows = [];
    while (($line = fgetcsv($fh)) !== false) {
        $rows[] = array_combine($header, array_pad($line, count($header), ''));
    }
    if ($fh !== STDIN) fclose($fh);

    return $rows;
}

function normalizeArticle(array $row): array {
    return [
        'title'     => trim((string)($row['title'] ?? 'Sans titre')),
        'excerpt'   => ($row['excerpt'] ?? null) !== '' ? (string)$row['excerpt'] : null,
        'views'     => (int)($row['views'] ?? 0),
        'published' => in_array(strtolower((string)($row['published'] ?? 'true')), ['1', 'true', 'yes', 'y', 'on'], true),
        'author'    => (string)($row['author'] ?? 'N/A'),
    ];
}

// ---- main ----
$opts = getopt('', ['input:', 'published-only', 'limit::', 'help']);

if (array_key_exists('help', $opts)) {
    usage();
    exit(EXIT_OK);
}

$input = $opts['input'] ?? null;
if ($input === null) {
    fwrite(STDERR, "Erreur: --input est requis (chemin ou '-')\n\n");
    usage();
    exit(EXIT_USAGE);
}

$publishedOnly = array_key_exists('published-only', $opts);
$limit = isset($opts['limit']) ? max(1, (int)$opts['limit']) : null;

try {
    $rows = readCsvFrom($input);
    $items = array_map('normalizeArticle', $rows);

    if ($publishedOnly) {
        $items = array_values(array_filter($items, fn($a) => $a['published']));
    }

    if ($limit !== null) {
        $items = array_slice($items, 0, $limit);
    }

    fwrite(STDOUT, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL);
    exit(EXIT_OK);
} catch (Throwable $e) {
    fwrite(STDERR, "Erreur: " . $e->getMessage() . PHP_EOL);
    exit(EXIT_DATA_ERROR);
}