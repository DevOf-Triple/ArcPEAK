<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

require_auth();

$where = [];
$params = [];

$style = trim($_GET['style'] ?? '');
if ($style !== '') {
    $parts = array_filter(array_map('trim', explode(',', $style)));
    if ($parts) {
        $where[] = 'style IN (' . implode(',', array_fill(0, count($parts), '?')) . ')';
        foreach ($parts as $p) $params[] = $p;
    }
}

$area = trim($_GET['area'] ?? '');
if ($area !== '') {
    if (strpos($area, '-') !== false) {
        [$min, $max] = explode('-', $area, 2);
        if ($min !== '') { $where[] = 'area_m2 >= ?'; $params[] = (float)$min; }
        if ($max !== '') { $where[] = 'area_m2 <= ?'; $params[] = (float)$max; }
    }
}

$floors = trim($_GET['floors'] ?? '');
if ($floors !== '') {
    $parts = array_filter(array_map('trim', explode(',', $floors)));
    $eq = [];
    foreach ($parts as $p) {
        if ($p === '3plus') {
            $where[] = 'floors >= ?';
            $params[] = 3;
        } else {
            $eq[] = (int)$p;
        }
    }
    if ($eq) {
        $where[] = 'floors IN (' . implode(',', array_fill(0, count($eq), '?')) . ')';
        foreach ($eq as $v) $params[] = $v;
    }
}

$bedrooms = trim($_GET['bedrooms'] ?? '');
if ($bedrooms !== '') {
    $parts = array_filter(array_map('trim', explode(',', $bedrooms)));
    $eq = [];
    foreach ($parts as $p) {
        if ($p === '5plus') {
            $where[] = 'bedrooms >= ?';
            $params[] = 5;
        } else {
            $eq[] = (int)$p;
        }
    }
    if ($eq) {
        $where[] = 'bedrooms IN (' . implode(',', array_fill(0, count($eq), '?')) . ')';
        foreach ($eq as $v) $params[] = $v;
    }
}

$bathrooms = trim($_GET['bathrooms'] ?? '');
if ($bathrooms !== '') {
    $parts = array_filter(array_map('trim', explode(',', $bathrooms)));
    $eq = [];
    foreach ($parts as $p) {
        if ($p === '3plus') {
            $where[] = 'bathrooms >= ?';
            $params[] = 3;
        } else {
            $eq[] = (int)$p;
        }
    }
    if ($eq) {
        $where[] = 'bathrooms IN (' . implode(',', array_fill(0, count($eq), '?')) . ')';
        foreach ($eq as $v) $params[] = $v;
    }
}

$garage = trim($_GET['garage'] ?? '');
if ($garage !== '') {
    $parts = array_filter(array_map('trim', explode(',', $garage)));
    if ($parts) {
        $where[] = 'garage IN (' . implode(',', array_fill(0, count($parts), '?')) . ')';
        foreach ($parts as $p) $params[] = $p;
    }
}

$material = trim($_GET['material'] ?? '');
if ($material !== '') {
    $parts = array_filter(array_map('trim', explode(',', $material)));
    if ($parts) {
        $where[] = 'wall_material IN (' . implode(',', array_fill(0, count($parts), '?')) . ')';
        foreach ($parts as $p) $params[] = $p;
    }
}

$roof = trim($_GET['roof'] ?? '');
if ($roof !== '') {
    $parts = array_filter(array_map('trim', explode(',', $roof)));
    if ($parts) {
        $where[] = 'roof_type IN (' . implode(',', array_fill(0, count($parts), '?')) . ')';
        foreach ($parts as $p) $params[] = $p;
    }
}

$features = trim($_GET['features'] ?? '');
if ($features !== '') {
    $parts = array_filter(array_map('trim', explode(',', $features)));
    foreach ($parts as $f) {
        $where[] = 'JSON_CONTAINS(extra_features, JSON_QUOTE(?))';
        $params[] = $f;
    }
}

$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
if ($priceMin !== '' && is_numeric($priceMin)) {
    $where[] = 'base_price >= ?';
    $params[] = (float)$priceMin;
}
if ($priceMax !== '' && is_numeric($priceMax)) {
    $where[] = 'base_price <= ?';
    $params[] = (float)$priceMax;
}

$sortMap = [
    'price_asc' => 'base_price ASC, id ASC',
    'price_desc' => 'base_price DESC, id ASC',
    'area_asc' => 'area_m2 ASC, id ASC',
    'area_desc' => 'area_m2 DESC, id ASC',
    'newest' => 'created_at DESC, id DESC',
];
$sort = $_GET['sort'] ?? 'newest';
$orderBy = $sortMap[$sort] ?? $sortMap['newest'];

$sql = 'SELECT id, name, slug, image_path, description, base_price, area_m2, floors, bedrooms, bathrooms, garage, style, wall_material, roof_type, extra_features FROM buildings';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY ' . $orderBy;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

foreach ($rows as &$r) {
    $r['id'] = (int)$r['id'];
    $r['base_price'] = (float)$r['base_price'];
    $r['area_m2'] = (float)$r['area_m2'];
    $r['floors'] = (int)$r['floors'];
    $r['bedrooms'] = (int)$r['bedrooms'];
    $r['bathrooms'] = (int)$r['bathrooms'];
    $r['extra_features'] = $r['extra_features'] ? json_decode($r['extra_features'], true) : [];
}
unset($r);

json_response(['ok' => true, 'data' => ['buildings' => $rows, 'count' => count($rows)]]);