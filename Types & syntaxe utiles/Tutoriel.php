<?php
declare(strict_types=1);

// Step 0: dataset
$input = [
    'title'     => 'PHP 8 in Practice',
    'excerpt'   => '',
    'views'     => '300',
    // 'published' is absent
    'author'    => 'Yassine'
];

// Step 1: Normalize with ?? and union types
function strOrNull(?string $s): ?string {
    $s = $s !== null ? trim($s) : null;
    return $s === '' ? null : $s;
}

function intOrZero(int|string|null $v): int {
    return max(0, (int)($v ?? 0));
}

// Step 2: Build a clean structure
$normalized = [
    'title'     => trim((string)($input['title'] ?? 'No Title')),
    'excerpt'   => strOrNull($input['excerpt'] ?? null),
    'views'     => intOrZero($input['views'] ?? null),
    'published' => $input['published'] ?? true,
    'author'    => trim((string)($input['author'] ?? 'N/A')),
];

// Output the normalized array
print_r($normalized);

// Step 3: Apply defaults with ??=
$defaults = [
    'per_page' => 10,
    'sort'     => 'created_desc',
];

$userQuery = ['per_page' => null]; // Simulated input
$userQuery['per_page'] ??= $defaults['per_page']; // Sets to 10
$userQuery['sort']     ??= $defaults['sort'];     // Sets to 'created_desc'

// Output the userQuery array
print_r($userQuery);
?>