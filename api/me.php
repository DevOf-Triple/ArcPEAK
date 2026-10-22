<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    exit(json_encode(['ok' => true, 'user' => null]));
}

echo json_encode([
    'ok' => true,
    'user' => [
        'id' => (int)$_SESSION['user_id'],
        'login' => $_SESSION['user_login'],
        'full_name' => $_SESSION['user_name'],
    ],
]);