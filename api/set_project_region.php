<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
$regionId  = (int)($_POST['region_id'] ?? 0);

if (!$projectId) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare("SELECT id FROM portfolio_projects WHERE id = ? AND user_id = ? AND project_kind = 'custom'");
$stmt->execute([$projectId, $userId]);
if (!$stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Проект не найден или не пользовательский'], 404);
}

if ($regionId > 0) {
    $stmt = $pdo->prepare('SELECT id FROM regions WHERE id = ?');
    $stmt->execute([$regionId]);
    if (!$stmt->fetch()) {
        json_response(['ok' => false, 'error' => 'Регион не найден'], 404);
    }
}

$pdo->prepare('
    INSERT INTO project_params (project_id, region_id) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE region_id = VALUES(region_id)
')->execute([$projectId, $regionId > 0 ? $regionId : null]);

$price = calculate_project_price($pdo, $projectId);

json_response([
    'ok' => true,
    'data' => [
        'region_id' => $regionId > 0 ? $regionId : null,
        'price' => $price,
    ],
]);