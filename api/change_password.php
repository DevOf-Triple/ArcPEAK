<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$current = (string)($_POST['current_password'] ?? '');
$new1    = (string)($_POST['new_password'] ?? '');
$new2    = (string)($_POST['new_password2'] ?? '');

if ($current === '' || $new1 === '' || $new2 === '') {
    json_response(['ok' => false, 'error' => 'Заполните все поля'], 400);
}
if (mb_strlen($new1) < 6) {
    json_response(['ok' => false, 'error' => 'Новый пароль: минимум 6 символов'], 400);
}
if ($new1 !== $new2) {
    json_response(['ok' => false, 'error' => 'Новые пароли не совпадают'], 400);
}
if ($new1 === $current) {
    json_response(['ok' => false, 'error' => 'Новый пароль совпадает с текущим'], 400);
}

$stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
$stmt->execute([$userId]);
$hash = $stmt->fetchColumn();

if (!$hash || !password_verify($current, $hash)) {
    json_response(['ok' => false, 'error' => 'Текущий пароль неверный'], 400);
}

$newHash = password_hash($new1, PASSWORD_DEFAULT);
$pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $userId]);

json_response(['ok' => true]);