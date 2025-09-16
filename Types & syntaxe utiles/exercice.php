<?php
declare(strict_types=1);

// Function to normalize article data
function buildArticle(array $row): array {
    // Apply defaults using ??=
    $row['title']     ??= 'No Title';
    $row['author']    ??= 'N/A';
    $row['published'] ??= true;

    // Normalize title (string)
    $title = trim((string)$row['title']);

    // Normalize excerpt (string|null, empty string becomes null)
    $excerpt = isset($row['excerpt']) ? trim((string)$row['excerpt']) : null;
    $excerpt = ($excerpt === '') ? null : $excerpt;

    // Normalize views (int >= 0)
    $views = (int)($row['views'] ?? 0);
    $views = max(0, $views);

    // Return normalized array
    return [
        'title'     => $title,
        'excerpt'   => $excerpt,
        'views'     => $views,
        'published' => (bool)$row['published'],
        'author'    => trim((string)$row['author']),
    ];
}

// Test cases
$testCases = [
    // Case 1: Missing values
    [
        'author' => 'Yassine',
        'views'  => '300',
    ],
    // Case 2: Zero and empty string
    [
        'title'     => 'PHP Guide',
        'excerpt'   => '',
        'views'     => 0,
        'published' => false,
        'author'    => 'Amina',
    ],
    // Case 3: Null values
    [
        'title'     => 'Advanced PHP',
        'excerpt'   => null,
        'views'     => null,
        'published' => null,
        'author'    => 'N/A',
    ],
];

// Run tests and output results
foreach ($testCases as $index => $testCase) {
    echo "Test Case " . ($index + 1) . ":\n";
    print_r(buildArticle($testCase));
    echo "\n";
}
?>