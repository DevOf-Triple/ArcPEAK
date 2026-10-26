<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$sheetId = (int)($_POST['sheet_id'] ?? 0);
if (!$sheetId) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare('SELECT id, project_id FROM sheets WHERE id = ? AND is_custom = 1 AND owner_user_id = ?');
$stmt->execute([$sheetId, $userId]);
$sheet = $stmt->fetch();
if (!$sheet) {
    json_response(['ok' => false, 'error' => 'Лист не найден'], 404);
}

try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM portfolio_items WHERE item_type = 'sheet' AND item_id = ?")->execute([$sheetId]);
    $pdo->prepare('DELETE FROM sheets WHERE id = ?')->execute([$sheetId]);
    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Не удалось удалить'], 500);
}

json_response(['ok' => true]);