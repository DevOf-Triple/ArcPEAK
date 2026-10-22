<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../config/db.php';

$login = trim($_POST['login'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($login === '' || $password === '') {
    exit(json_encode(['ok' => false, 'error' => 'Заполни все поля']));
}

$stmt = $pdo->prepare('SELECT id, login, password_hash, full_name, email FROM users WHERE login = ? OR email = ? LIMIT 1');
$stmt->execute([$login, $login]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    exit(json_encode(['ok' => false, 'error' => 'Неверный логин или пароль']));
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_login'] = $user['login'];
$_SESSION['user_name'] = $user['full_name'];

echo json_encode([
    'ok' => true,
    'user' => [
        'id' => (int)$user['id'],
        'login' => $user['login'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
    ],
]);