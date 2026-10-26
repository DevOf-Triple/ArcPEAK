<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$stmt = $pdo->prepare("
    SELECT p.id, p.project_number, p.title, p.project_kind, p.created_at, p.updated_at,
           (SELECT COUNT(*) FROM portfolio_items WHERE project_id = p.id) AS items_count
    FROM portfolio_projects p
    WHERE p.user_id = ?
    ORDER BY p.project_number ASC
");
$stmt->execute([$userId]);
$projects = $stmt->fetchAll();

foreach ($projects as &$p) {
    $p['id'] = (int)$p['id'];
    $p['project_number'] = (int)$p['project_number'];
    $p['items_count'] = (int)$p['items_count'];
}
unset($p);

$active = $_SESSION['active_project_id'] ?? null;
if (!$active && $projects) {
    $active = $projects[count($projects) - 1]['id'];
}

json_response([
    'ok' => true,
    'data' => [
        'projects' => $projects,
        'active_project_id' => $active ? (int)$active : null,
    ],
]);