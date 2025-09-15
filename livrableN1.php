<?php
declare(strict_types=1);

// Helper function to normalize strings (trim and handle empty to null)
function normalizeString(mixed $value, string $default = ''): ?string {
    $value = isset($value) ? trim((string)$value) : $default;
    return $value === '' ? null : $value;
}

// Helper function to normalize integers (ensure non-negative)
function normalizeInt(mixed $value): int {
    return max(0, (int)($value ?? 0));
}

function buildArticle(array $row): array {
    // Set defaults using ??=
    $row['title'] ??= 'No Title';
    $row['author'] ??= 'Unknown';
    $row['published'] ??= true;

    return [
        'title' => normalizeString($row['title'], 'No Title'),
        'excerpt' => normalizeString($row['excerpt']),
        'views' => normalizeInt($row['views']),
        'published' => (bool)($row['published'] ?? true),
        'author' => normalizeString($row['author'], 'Unknown'),
    ];
}


// Test Case 1: Missing Values
$case1 = [
    'views' => '100',
    'excerpt' => 'Some text',
];
echo "Test Case 1:\n";
print_r(buildArticle($case1));

// Test Case 2: Zeros and Empty Strings
$case2 = [
    'title' => '  Test Article  ',
    'excerpt' => '',
    'views' => '0',
    'published' => false,
    'author' => '  Amina  ',
];
echo "\nTest Case 2:\n";
print_r(buildArticle($case2));

// Test Case 3: Null Values
$case3 = [
    'title' => null,
    'excerpt' => null,
    'views' => null,
    'published' => null,
    'author' => null,
];
echo "\nTest Case 3:\n";
print_r(buildArticle($case3));