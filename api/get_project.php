<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$projectId = (int)($_GET['id'] ?? 0);
if (!$projectId) {
    json_response(['ok' => false, 'error' => 'Не указан проект'], 400);
}

$stmt = $pdo->prepare('SELECT id, project_number, title, project_kind, created_at FROM portfolio_projects WHERE id = ? AND user_id = ?');
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
    $st = $pdo->prepare("
        SELECT s.id, s.tag_id, s.name, s.description, s.image_path,
               s.modifier_type, s.modifier_value, s.is_custom,
               t.slug AS tag_slug, t.name AS tag_name, t.is_key, t.is_radio
        FROM sheets s
        JOIN catalog_tags t ON t.id = s.tag_id
        WHERE s.id IN ($in)
    ");
    $st->execute($sheetIds);
    foreach ($st->fetchAll() as $r) {
        $r['modifier_value'] = (float)$r['modifier_value'];
        $r['is_custom'] = (int)$r['is_custom'];
        $r['is_key'] = (int)$r['is_key'];
        $r['is_radio'] = (int)$r['is_radio'];
        $sheetsMap[(int)$r['id']] = $r;
    }
}

$items = [];
foreach ($rawItems as $it) {
    $type = $it['item_type'];
    $iid  = (int)$it['item_id'];

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
            'tag_id' => (int)$s['tag_id'],
            'tag_slug' => $s['tag_slug'],
            'tag_name' => $s['tag_name'],
            'is_key' => $s['is_key'],
            'is_radio' => $s['is_radio'],
            'is_custom' => $s['is_custom'],
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

$params = null;
if ($project['project_kind'] === 'custom') {
    $stmt = $pdo->prepare('
        SELECT pp.region_id, r.name AS region_name, r.factor AS region_factor
        FROM project_params pp
        LEFT JOIN regions r ON r.id = pp.region_id
        WHERE pp.project_id = ?
    ');
    $stmt->execute([$projectId]);
    $row = $stmt->fetch();
    $params = [
        'region_id' => $row && $row['region_id'] ? (int)$row['region_id'] : null,
        'region_name' => $row && $row['region_name'] ? $row['region_name'] : null,
        'region_factor' => $row && $row['region_factor'] ? (float)$row['region_factor'] : null,
    ];
}

json_response([
    'ok' => true,
    'data' => [
        'project' => [
            'id' => (int)$project['id'],
            'project_number' => (int)$project['project_number'],
            'title' => $project['title'],
            'project_kind' => $project['project_kind'],
            'created_at' => $project['created_at'],
        ],
        'params' => $params,
        'items' => $items,
        'price' => $price,
    ],
]);