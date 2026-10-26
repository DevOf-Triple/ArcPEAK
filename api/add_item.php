<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_POST['project_id'] ?? 0);
$itemType  = $_POST['item_type'] ?? '';
$itemId    = (int)($_POST['item_id'] ?? 0);

if (!$projectId || !$itemId || !in_array($itemType, ['building', 'sheet'], true)) {
    json_response(['ok' => false, 'error' => 'Неверные данные'], 400);
}

// Проект юзера + тип
$stmt = $pdo->prepare('SELECT id, project_kind FROM portfolio_projects WHERE id = ? AND user_id = ?');
$stmt->execute([$projectId, $userId]);
$project = $stmt->fetch();
if (!$project) {
    json_response(['ok' => false, 'error' => 'Проект не найден'], 404);
}

// Валидация типа проекта
if ($project['project_kind'] === 'ready' && $itemType === 'sheet') {
    json_response(['ok' => false, 'error' => 'В готовый проект нельзя добавлять листы'], 400);
}
if ($project['project_kind'] === 'custom' && $itemType === 'building') {
    json_response(['ok' => false, 'error' => 'В пользовательский проект нельзя добавлять готовые здания'], 400);
}

// Проверка что item существует и доступен
if ($itemType === 'building') {
    $stmt = $pdo->prepare('SELECT id FROM buildings WHERE id = ?');
    $stmt->execute([$itemId]);
    if (!$stmt->fetch()) {
        json_response(['ok' => false, 'error' => 'Здание не найдено'], 404);
    }
    $tagInfo = null;
} else {
    $stmt = $pdo->prepare('
        SELECT s.id, s.tag_id, s.is_custom, s.owner_user_id, t.is_radio
        FROM sheets s
        JOIN catalog_tags t ON t.id = s.tag_id
        WHERE s.id = ?
    ');
    $stmt->execute([$itemId]);
    $tagInfo = $stmt->fetch();
    if (!$tagInfo) {
        json_response(['ok' => false, 'error' => 'Лист не найден'], 404);
    }
    // Кастомные листы видны только их владельцу
    if ((int)$tagInfo['is_custom'] === 1 && (int)$tagInfo['owner_user_id'] !== $userId) {
        json_response(['ok' => false, 'error' => 'Лист не найден'], 404);
    }
}

$pdo->beginTransaction();

try {
    // Радио-тег — снять старый лист этой группы
    if ($itemType === 'sheet' && (int)$tagInfo['is_radio'] === 1) {
        $pdo->prepare("
            DELETE pi FROM portfolio_items pi
            JOIN sheets s ON s.id = pi.item_id AND pi.item_type = 'sheet'
            WHERE pi.project_id = ? AND s.tag_id = ? AND pi.item_id != ?
        ")->execute([$projectId, (int)$tagInfo['tag_id'], $itemId]);
    }

    $stmt = $pdo->prepare('INSERT IGNORE INTO portfolio_items (project_id, item_type, item_id) VALUES (?, ?, ?)');
    $stmt->execute([$projectId, $itemType, $itemId]);
    $inserted = $stmt->rowCount() > 0;

    $pdo->prepare('UPDATE portfolio_projects SET updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$projectId]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Не удалось добавить элемент'], 500);
}

$price = calculate_project_price($pdo, $projectId);

json_response([
    'ok' => true,
    'data' => [
        'added' => $inserted,
        'price' => $price,
    ],
]);