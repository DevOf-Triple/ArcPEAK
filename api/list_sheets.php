<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

require_auth();

$tagSlug = trim($_GET['tag'] ?? '');

$tags = $pdo->query('SELECT id, name, slug FROM catalog_tags ORDER BY sort_order ASC, id ASC')->fetchAll();
$tagsMap = [];
foreach ($tags as $t) {
    $tagsMap[(int)$t['id']] = [
        'id' => (int)$t['id'],
        'name' => $t['name'],
        'slug' => $t['slug'],
        'sheets' => [],
    ];
}

$sql = 'SELECT id, tag_id, name, description, image_path, modifier_type, modifier_value FROM sheets';
$params = [];
if ($tagSlug !== '') {
    $sql .= ' WHERE tag_id = (SELECT id FROM catalog_tags WHERE slug = ?)';
    $params[] = $tagSlug;
}
$sql .= ' ORDER BY tag_id ASC, sort_order ASC, id ASC';

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
    ];
}

json_response(['ok' => true, 'data' => ['tags' => array_values($tagsMap)]]);