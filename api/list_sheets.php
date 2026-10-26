<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$userId = require_auth();

$tagSlug = trim($_GET['tag'] ?? '');

$tags = $pdo->query('
    SELECT id, name, slug, is_key, is_radio, sort_order
    FROM catalog_tags
    ORDER BY sort_order ASC, id ASC
')->fetchAll();

$tagsMap = [];
foreach ($tags as $t) {
    $tagsMap[(int)$t['id']] = [
        'id' => (int)$t['id'],
        'name' => $t['name'],
        'slug' => $t['slug'],
        'is_key' => (int)$t['is_key'],
        'is_radio' => (int)$t['is_radio'],
        'sheets' => [],
    ];
}

// Только дефолтные листы (is_custom = 0). Кастомные подтягиваются через get_project.
$sql = '
    SELECT s.id, s.tag_id, s.name, s.description, s.image_path,
           s.modifier_type, s.modifier_value
    FROM sheets s
    WHERE s.is_custom = 0
';
$params = [];
if ($tagSlug !== '') {
    $sql .= ' AND s.tag_id = (SELECT id FROM catalog_tags WHERE slug = ?)';
    $params[] = $tagSlug;
}
$sql .= ' ORDER BY s.tag_id ASC, s.sort_order ASC, s.id ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

foreach ($stmt->fetchAll() as $s) {
    $tid = (int)$s['tag_id'];
    if (!isset($tagsMap[$tid])) continue;
    $tagsMap[$tid]['sheets'][] = [
        'id' => (int)$s['id'],
        'name' => $s['name'],
        'description' => $s['description'],
        'image_path' => $s['image_path'],
        'modifier_type' => $s['modifier_type'],
        'modifier_value' => (float)$s['modifier_value'],
        'is_custom' => 0,
    ];
}

json_response(['ok' => true, 'data' => ['tags' => array_values($tagsMap)]]);