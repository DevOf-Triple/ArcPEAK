<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

$adminId = require_admin();

$targetId = (int)($_POST['user_id'] ?? 0);
if (!$targetId) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}
if ($targetId === $adminId) {
    json_response(['ok' => false, 'error' => 'Нельзя удалить самого себя'], 403);
}

$stmt = $pdo->prepare('SELECT is_root_admin FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$isRoot = $stmt->fetchColumn();
if ($isRoot === false) {
    json_response(['ok' => false, 'error' => 'Пользователь не найден'], 404);
}
if ((int)$isRoot === 1) {
    json_response(['ok' => false, 'error' => 'Нельзя удалить главного администратора'], 403);
}

// CASCADE в БД удалит проекты, айтемы, params, overrides, calculations
$pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$targetId]);

json_response(['ok' => true]);