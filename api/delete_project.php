<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
if (!$projectId) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare('SELECT id FROM portfolio_projects WHERE id = ? AND user_id = ?');
$stmt->execute([$projectId, $userId]);
if (!$stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Проект не найден'], 404);
}

$pdo->prepare('DELETE FROM portfolio_projects WHERE id = ?')->execute([$projectId]);

$active = $_SESSION['active_project_id'] ?? null;
$nextActive = null;

if ((int)$active === $projectId) {
    $stmt = $pdo->prepare('SELECT id FROM portfolio_projects WHERE user_id = ? ORDER BY project_number ASC LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row) {
        $nextActive = (int)$row['id'];
        $_SESSION['active_project_id'] = $nextActive;
    } else {
        unset($_SESSION['active_project_id']);
    }
} else {
    $nextActive = $active ? (int)$active : null;
}

json_response(['ok' => true, 'data' => ['next_active_id' => $nextActive]]);