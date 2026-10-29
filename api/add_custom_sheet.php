<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
$tagId     = (int)($_POST['tag_id'] ?? 0);
$name      = trim($_POST['name'] ?? '');
$value     = (float)($_POST['value'] ?? 0);

if (!$projectId || !$tagId || $name === '') {
    json_response(['ok' => false, 'error' => 'Заполните категорию и название'], 400);
}
if (mb_strlen($name) > 120) $name = mb_substr($name, 0, 120);

$stmt = $pdo->prepare("SELECT id FROM portfolio_projects WHERE id = ? AND user_id = ? AND project_kind = 'custom'");
$stmt->execute([$projectId, $userId]);
if (!$stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Проект не найден или не пользовательский'], 404);
}

$stmt = $pdo->prepare('SELECT id, is_key, is_radio, slug FROM catalog_tags WHERE id = ?');
$stmt->execute([$tagId]);
$tag = $stmt->fetch();
if (!$tag) {
    json_response(['ok' => false, 'error' => 'Категория не найдена'], 404);
}

$modType = 'flat_add';
if ($tag['slug'] === 'type')   $modType = 'base_type_factor';
if ($tag['slug'] === 'area')   $modType = 'base_area';
if ($tag['slug'] === 'floors') $modType = 'base_floors';

$sheetId = 0;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO sheets (tag_id, name, description, modifier_type, modifier_value, is_custom, owner_user_id, project_id, sort_order)
        VALUES (?, ?, ?, ?, ?, 1, ?, ?, 999)
    ');
    $stmt->execute([$tagId, $name, 'Пользовательский вариант', $modType, $value, $userId, $projectId]);
    $sheetId = (int)$pdo->lastInsertId();

    $pdo->prepare('INSERT IGNORE INTO portfolio_items (project_id, item_type, item_id) VALUES (?, "sheet", ?)')
        ->execute([$projectId, $sheetId]);

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Не удалось создать лист: ' . $e->getMessage()], 500);
}

$price = calculate_project_price($pdo, $projectId);

json_response([
    'ok' => true,
    'data' => [
        'sheet' => [
            'id' => $sheetId,
            'tag_id' => $tagId,
            'name' => $name,
            'modifier_type' => $modType,
            'modifier_value' => $value,
            'is_custom' => true,
        ],
        'price' => $price,
    ],
]);