<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
$itemType = $_POST['item_type'] ?? '';
$itemId = (int)($_POST['item_id'] ?? 0);

if (!$projectId || !$itemId || !in_array($itemType, ['building', 'sheet'], true)) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare('SELECT id FROM portfolio_projects WHERE id = ? AND user_id = ?');
$stmt->execute([$projectId, $userId]);
if (!$stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Проект не найден'], 404);
}

$stmt = $pdo->prepare('DELETE FROM portfolio_items WHERE project_id = ? AND item_type = ? AND item_id = ?');
$stmt->execute([$projectId, $itemType, $itemId]);

$pdo->prepare('UPDATE portfolio_projects SET updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$projectId]);

json_response(['ok' => true, 'data' => ['removed' => $stmt->rowCount() > 0]]);