<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
$title = trim($_POST['title'] ?? '');

if (!$projectId || $title === '') {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}
if (mb_strlen($title) > 150) $title = mb_substr($title, 0, 150);

$stmt = $pdo->prepare('SELECT id FROM portfolio_projects WHERE id = ? AND user_id = ?');
$stmt->execute([$projectId, $userId]);
if (!$stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Проект не найден'], 404);
}

$pdo->prepare('UPDATE portfolio_projects SET title = ? WHERE id = ?')->execute([$title, $projectId]);

json_response(['ok' => true, 'data' => ['id' => $projectId, 'title' => $title]]);