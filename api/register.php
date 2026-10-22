<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../config/db.php';

$login = trim($_POST['login'] ?? '');
$password = (string)($_POST['password'] ?? '');
$password2 = (string)($_POST['password2'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($login === '' || $password === '' || $full_name === '' || $email === '') {
    exit(json_encode(['ok' => false, 'error' => 'Заполни все поля']));
}
if (mb_strlen($login) < 3 || mb_strlen($login) > 50) {
    exit(json_encode(['ok' => false, 'error' => 'Логин: от 3 до 50 символов']));
}
if (mb_strlen($password) < 6) {
    exit(json_encode(['ok' => false, 'error' => 'Пароль: минимум 6 символов']));
}
if ($password !== $password2) {
    exit(json_encode(['ok' => false, 'error' => 'Пароли не совпадают']));
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['ok' => false, 'error' => 'Некорректная почта']));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE login = ? OR email = ? LIMIT 1');
$stmt->execute([$login, $email]);
if ($stmt->fetch()) {
    exit(json_encode(['ok' => false, 'error' => 'Логин или почта уже заняты']));
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (login, password_hash, full_name, email) VALUES (?, ?, ?, ?)');
$stmt->execute([$login, $hash, $full_name, $email]);

$id = (int)$pdo->lastInsertId();
$_SESSION['user_id'] = $id;
$_SESSION['user_login'] = $login;
$_SESSION['user_name'] = $full_name;

echo json_encode([
    'ok' => true,
    'user' => [
        'id' => $id,
        'login' => $login,
        'full_name' => $full_name,
        'email' => $email,
    ],
]);