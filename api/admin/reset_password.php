<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/helpers.php';

require_admin();

$targetId = (int)($_POST['user_id'] ?? 0);
if (!$targetId) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

$stmt = $pdo->prepare('SELECT id, login FROM users WHERE id = ?');
$stmt->execute([$targetId]);
$user = $stmt->fetch();
if (!$user) {
    json_response(['ok' => false, 'error' => 'Пользователь не найден'], 404);
}

// Генерация читаемого пароля (без похожих символов)
$chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$newPassword = '';
for ($i = 0; $i < 12; $i++) {
    $newPassword .= $chars[random_int(0, strlen($chars) - 1)];
}

$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $targetId]);

json_response([
    'ok' => true,
    'data' => [
        'user_id'      => $targetId,
        'login'        => $user['login'],
        'new_password' => $newPassword,
    ],
]);