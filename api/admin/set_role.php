<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

$adminId = require_admin();

$targetId = (int)($_POST['user_id'] ?? 0);
$newRole  = $_POST['role'] ?? '';

if (!$targetId || !in_array($newRole, ['user', 'curator', 'admin'], true)) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare('SELECT id, role, is_root_admin FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$target = $stmt->fetch();
if (!$target) {
    json_response(['ok' => false, 'error' => 'Пользователь не найден'], 404);
}

if ((int)$target['is_root_admin'] === 1) {
    json_response(['ok' => false, 'error' => 'Нельзя изменить роль главного администратора'], 403);
}

// Назначение/снятие роли admin — только root admin
if ($newRole === 'admin' || $target['role'] === 'admin') {
    $stmt = $pdo->prepare('SELECT is_root_admin FROM users WHERE id = ?');
    $stmt->execute([$adminId]);
    if ((int)$stmt->fetchColumn() !== 1) {
        json_response(['ok' => false, 'error' => 'Только главный администратор может управлять ролями администраторов'], 403);
    }
}

$pdo->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$newRole, $targetId]);

json_response(['ok' => true, 'data' => ['user_id' => $targetId, 'role' => $newRole]]);