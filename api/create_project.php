<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$title = trim($_POST['title'] ?? '');
if ($title === '') $title = 'Проект';
if (mb_strlen($title) > 150) $title = mb_substr($title, 0, 150);

$number = get_free_project_number($pdo, $userId);

try {
    $stmt = $pdo->prepare('INSERT INTO portfolio_projects (user_id, project_number, title) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $number, $title]);
    $id = (int)$pdo->lastInsertId();

    $_SESSION['active_project_id'] = $id;

    json_response([
        'ok' => true,
        'data' => [
            'id' => $id,
            'project_number' => $number,
            'title' => $title,
        ],
    ]);
} catch (PDOException $e) {
    json_response(['ok' => false, 'error' => 'Не удалось создать проект'], 500);
}