<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$title = trim($_POST['title'] ?? '');
if ($title === '') $title = 'Проект';
if (mb_strlen($title) > 150) $title = mb_substr($title, 0, 150);

$kind = $_POST['project_kind'] ?? 'ready';
if (!in_array($kind, ['ready', 'custom'], true)) {
    json_response(['ok' => false, 'error' => 'Неверный тип проекта'], 400);
}

$number = get_free_project_number($pdo, $userId);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('INSERT INTO portfolio_projects (user_id, project_number, title, project_kind) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $number, $title, $kind]);
    $id = (int)$pdo->lastInsertId();

    if ($kind === 'custom') {
        $pdo->prepare('INSERT INTO project_params (project_id, region_id) VALUES (?, NULL)')->execute([$id]);
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Не удалось создать проект'], 500);
}

$_SESSION['active_project_id'] = $id;

json_response([
    'ok' => true,
    'data' => [
        'id' => $id,
        'project_number' => $number,
        'title' => $title,
        'project_kind' => $kind,
    ],
]);