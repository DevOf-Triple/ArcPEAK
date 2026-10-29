<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

require_auth();

$rows = $pdo->query('SELECT id, name, factor FROM regions ORDER BY name ASC')->fetchAll();
foreach ($rows as &$r) {
    $r['id'] = (int)$r['id'];
    $r['factor'] = (float)$r['factor'];
}
unset($r);

json_response(['ok' => true, 'data' => ['regions' => $rows]]);