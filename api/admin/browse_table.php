<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

require_admin();

$allowed = [
    'users', 'buildings', 'building_sheets', 'catalog_tags',
    'portfolio_items', 'portfolio_projects', 'project_params',
    'project_sheet_overrides', 'regions', 'saved_calculations', 'sheets',
];

$table = $_GET['table'] ?? '';
if (!in_array($table, $allowed, true)) {
    json_response(['ok' => false, 'error' => 'Таблица недоступна'], 400);
}

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$offset  = ($page - 1) * $perPage;

$total = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
$stmt  = $pdo->query("SELECT * FROM `$table` LIMIT $perPage OFFSET $offset");
$rows  = $stmt->fetchAll();

if ($rows) {
    $columns = array_keys($rows[0]);
} else {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    $columns = array_map(fn($c) => $c['Field'], $stmt->fetchAll());
}

json_response([
    'ok' => true,
    'data' => [
        'table'   => $table,
        'columns' => $columns,
        'rows'    => $rows,
        'total'   => $total,
        'page'    => $page,
        'per_page'=> $perPage,
    ],
]);