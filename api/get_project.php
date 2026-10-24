<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_GET['id'] ?? 0);
if (!$projectId) {
    json_response(['ok' => false, 'error' => 'Не указан проект'], 400);
}

$stmt = $pdo->prepare('SELECT id, project_number, title, created_at FROM portfolio_projects WHERE id = ? AND user_id = ?');
$stmt->execute([$projectId, $userId]);
$project = $stmt->fetch();
if (!$project) {
    json_response(['ok' => false, 'error' => 'Проект не найден'], 404);
}

$stmt = $pdo->prepare('SELECT item_type, item_id, added_at FROM portfolio_items WHERE project_id = ? ORDER BY added_at ASC, id ASC');
$stmt->execute([$projectId]);
$rawItems = $stmt->fetchAll();

$buildingIds = [];
$sheetIds = [];
foreach ($rawItems as $it) {
    if ($it['item_type'] === 'building') $buildingIds[] = (int)$it['item_id'];
    else $sheetIds[] = (int)$it['item_id'];
}

$buildingsMap = [];
if ($buildingIds) {
    $in = implode(',', array_fill(0, count($buildingIds), '?'));
    $st = $pdo->prepare("SELECT id, name, slug, image_path, base_price, area_m2, floors, bedrooms, bathrooms, garage, style, wall_material, roof_type, extra_features FROM buildings WHERE id IN ($in)");
    $st->execute($buildingIds);
    foreach ($st->fetchAll() as $r) {
        $r['extra_features'] = $r['extra_features'] ? json_decode($r['extra_features'], true) : [];
        $buildingsMap[(int)$r['id']] = $r;
    }
}

$sheetsMap = [];
if ($sheetIds) {
    $in = implode(',', array_fill(0, count($sheetIds), '?'));
    $st = $pdo->prepare("SELECT id, name, description, image_path, modifier_type, modifier_value FROM sheets WHERE id IN ($in)");
    $st->execute($sheetIds);
    foreach ($st->fetchAll() as $r) {
        $r['modifier_value'] = (float)$r['modifier_value'];
        $sheetsMap[(int)$r['id']] = $r;
    }
}

$items = [];
foreach ($rawItems as $it) {
    $type = $it['item_type'];
    $iid = (int)$it['item_id'];
    if ($type === 'building' && isset($buildingsMap[$iid])) {
        $b = $buildingsMap[$iid];
        $items[] = [
            'type' => 'building',
            'id' => $iid,
            'name' => $b['name'],
            'slug' => $b['slug'],
            'image_path' => $b['image_path'],
            'base_price' => (float)$b['base_price'],
            'area_m2' => (float)$b['area_m2'],
            'floors' => (int)$b['floors'],
            'bedrooms' => (int)$b['bedrooms'],
            'bathrooms' => (int)$b['bathrooms'],
            'garage' => $b['garage'],
            'style' => $b['style'],
            'wall_material' => $b['wall_material'],
            'roof_type' => $b['roof_type'],
            'extra_features' => $b['extra_features'],
            'added_at' => $it['added_at'],
        ];
    } elseif ($type === 'sheet' && isset($sheetsMap[$iid])) {
        $s = $sheetsMap[$iid];
        $items[] = [
            'type' => 'sheet',
            'id' => $iid,
            'name' => $s['name'],
            'description' => $s['description'],
            'image_path' => $s['image_path'],
            'modifier_type' => $s['modifier_type'],
            'modifier_value' => $s['modifier_value'],
            'added_at' => $it['added_at'],
        ];
    }
}

$price = calculate_project_price($pdo, $projectId);

json_response([
    'ok' => true,
    'data' => [
        'project' => [
            'id' => (int)$project['id'],
            'project_number' => (int)$project['project_number'],
            'title' => $project['title'],
            'created_at' => $project['created_at'],
        ],
        'items' => $items,
        'price' => $price,
    ],
]);